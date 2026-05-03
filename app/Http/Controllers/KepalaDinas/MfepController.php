<?php

namespace App\Http\Controllers\KepalaDinas;

use App\Http\Controllers\Controller;
use App\Models\MfepCalculation;
use App\Models\MfepResult;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MfepController extends Controller
{
    public function ranking(Request $request): View
    {
        $calculations = MfepCalculation::where('status', 'finalized')
            ->orderByDesc('finalized_at')
            ->paginate(10);

        $selectedCalculationId = $request->input('calculation_id');
        $results = collect();
        $selectedCalculation = null;

        if ($selectedCalculationId) {
            $selectedCalculation = MfepCalculation::find($selectedCalculationId);
            if ($selectedCalculation && $selectedCalculation->status === 'finalized') {
                $results = MfepResult::where('mfep_calculation_id', $selectedCalculationId)
                    ->with('alternative')
                    ->orderBy('rank')
                    ->get();
            }
        }

        return view('kepala-dinas.mfep.ranking', [
            'calculations' => $calculations,
            'results' => $results,
            'selectedCalculation' => $selectedCalculation,
        ]);
    }

    public function show(MfepCalculation $calculation): View
    {
        abort_if($calculation->status !== 'finalized', 404);

        $results = MfepResult::where('mfep_calculation_id', $calculation->id)
            ->with(['alternative', 'details.criteria'])
            ->orderBy('rank')
            ->get();

        return view('kepala-dinas.mfep.show', [
            'calculation' => $calculation,
            'results' => $results,
        ]);
    }

    public function exportPdf(MfepCalculation $calculation)
    {
        abort_if($calculation->status !== 'finalized', 404);

        $results = MfepResult::where('mfep_calculation_id', $calculation->id)
            ->with(['alternative', 'details.criteria'])
            ->orderBy('rank')
            ->get();

        $pdf = Pdf::loadView('admin.mfep.show-pdf', [
            'calculation' => $calculation,
            'results' => $results,
            'generatedAt' => now(),
        ])->setPaper('a4', 'landscape');

        return $pdf->download('mfep-'.$calculation->code.'-'.now()->format('Ymd-His').'.pdf');
    }
}
