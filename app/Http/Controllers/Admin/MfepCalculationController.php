<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AssessmentPeriod;
use App\Models\MfepCalculation;
use App\Models\MfepResult;
use App\Services\MfepCalculationService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class MfepCalculationController extends Controller
{
    public function __construct(protected MfepCalculationService $calculationService)
    {
    }

    /**
     * Show ranking results page
     */
    public function ranking(Request $request): View
    {
        $periods = AssessmentPeriod::orderByDesc('year')->get();

        // Default ke periode aktif, atau bisa pilih manual
        $selectedPeriodId = $request->input('period_id')
            ?? AssessmentPeriod::where('status', 'active')->value('id');

        $calculations = MfepCalculation::where('status', 'finalized')
            ->when($selectedPeriodId, fn ($q) => $q->where('period_id', $selectedPeriodId))
            ->orderByDesc('finalized_at')
            ->paginate(10);

        $selectedCalculationId = $request->input('calculation_id');
        $results               = collect();
        $selectedCalculation   = null;

        if ($selectedCalculationId) {
            $selectedCalculation = MfepCalculation::find($selectedCalculationId);
            if ($selectedCalculation && $selectedCalculation->status === 'finalized') {
                $results = MfepResult::where('mfep_calculation_id', $selectedCalculationId)
                    ->with(['alternative', 'details.criteria', 'details.subCriteria'])
                    ->orderBy('rank')
                    ->get();
            }
        }

        return view('admin.mfep.ranking', [
            'calculations'        => $calculations,
            'results'             => $results,
            'selectedCalculation' => $selectedCalculation,
            'periods'             => $periods,
            'selectedPeriodId'    => $selectedPeriodId,
        ]);
    }

    /**
     * Show calculation trigger page
     */
    public function create(): View
    {
        $activePeriod    = AssessmentPeriod::where('status', 'active')->first();
        $lastCalculation = MfepCalculation::orderByDesc('id')->first();

        return view('admin.mfep.create', [
            'lastCalculation' => $lastCalculation,
            'activePeriod'    => $activePeriod,
        ]);
    }

    /**
     * Trigger MFEP calculation
     */
    public function store(Request $request): RedirectResponse
    {
        $activePeriod = AssessmentPeriod::where('status', 'active')->first();

        if (! $activePeriod) {
            return back()->with('error', 'Tidak ada periode penilaian yang sedang aktif. Aktifkan periode terlebih dahulu.');
        }

        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        // Jika sudah ada perhitungan untuk periode aktif, hapus agar tidak menumpuk
        MfepCalculation::where('period_id', $activePeriod->id)->delete();

        $mfepCalculation = MfepCalculation::create([
            'code'                  => 'MFEP-' . now()->format('YmdHis'),
            'name'                  => $validated['name'],
            'description'           => $validated['description'] ?? null,
            'calculation_date'      => now()->toDateString(),
            'status'                => 'draft',
            'period_id'             => $activePeriod->id,
            'calculated_by_user_id' => auth()->id(),
        ]);

        try {
            $this->calculationService->calculate($mfepCalculation->id);

            return redirect()->route('admin.mfep.ranking', ['calculation_id' => $mfepCalculation->id])
                ->with('success', 'Perhitungan MFEP berhasil dilakukan!');
        } catch (\Exception $e) {
            $mfepCalculation->delete();

            return back()
                ->withInput()
                ->with('error', 'Gagal melakukan perhitungan: ' . $e->getMessage());
        }
    }

    /**
     * Show calculation details
     */
    public function show(MfepCalculation $calculation): View
    {
        $calculation->load('period');

        $results = MfepResult::where('mfep_calculation_id', $calculation->id)
            ->with(['alternative', 'details.criteria', 'details.subCriteria', 'details.assessmentAspect'])
            ->orderBy('rank')
            ->get();

        return view('admin.mfep.show', [
            'calculation' => $calculation,
            'results'     => $results,
        ]);
    }

    /**
     * Export calculation details to PDF
     */
    public function exportPdf(MfepCalculation $calculation)
    {
        $calculation->load('period');

        $results = MfepResult::where('mfep_calculation_id', $calculation->id)
            ->with(['alternative', 'details.criteria'])
            ->orderBy('rank')
            ->get();

        $pdf = Pdf::loadView('admin.mfep.show-pdf', [
            'calculation' => $calculation,
            'results'     => $results,
            'generatedAt' => now(),
        ])->setPaper('a4', 'landscape');

        return $pdf->download('mfep-' . $calculation->code . '-' . now()->format('Ymd-His') . '.pdf');
    }

    /**
     * Delete a calculation
     */
    public function destroy(MfepCalculation $calculation): RedirectResponse
    {
        $calculation->delete();

        return redirect()->route('admin.mfep.ranking')
            ->with('success', 'Perhitungan MFEP berhasil dihapus!');
    }
}