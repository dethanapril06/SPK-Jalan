<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Http\Requests\AssessmentPeriodRequest;
use App\Models\AssessmentPeriod;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AssessmentPeriodController extends Controller
{
    /**
     * Daftar semua periode penilaian.
     */
    public function index(Request $request): View
    {
        $query = AssessmentPeriod::query()
            ->withCount(['surveyorAssignments', 'assessments', 'mfepCalculations'])
            ->latest();

        // Filter by tahun
        if ($request->filled('year')) {
            $query->where('year', $request->year);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $periods = $query->paginate(10)->withQueryString();

        return view('admin.assessment-periods.index', compact('periods'));
    }

    /**
     * Form tambah periode baru.
     */
    public function create(): View
    {
        return view('admin.assessment-periods.create');
    }

    /**
     * Simpan periode baru ke database.
     */
    public function store(AssessmentPeriodRequest $request): RedirectResponse
    {
        AssessmentPeriod::create([
            ...$request->validated(),
            'created_by_user_id' => auth()->id(),
        ]);

        return redirect()
            ->route('admin.assessment-periods.index')
            ->with('success', 'Periode penilaian berhasil ditambahkan.');
    }

    /**
     * Detail satu periode.
     */
    public function show(AssessmentPeriod $assessmentPeriod): View
    {
        $assessmentPeriod->loadCount([
            'surveyorAssignments',
            'assessments',
            'mfepCalculations',
        ]);

        return view('admin.assessment-periods.show', compact('assessmentPeriod'));
    }

    /**
     * Form edit periode.
     */
    public function edit(AssessmentPeriod $assessmentPeriod): View
    {
        return view('admin.assessment-periods.edit', compact('assessmentPeriod'));
    }

    /**
     * Update data periode.
     */
    public function update(AssessmentPeriodRequest $request, AssessmentPeriod $assessmentPeriod): RedirectResponse
    {
        // Jika periode sudah closed, tidak boleh diubah
        if ($assessmentPeriod->isClosed()) {
            return redirect()
                ->route('admin.assessment-periods.index')
                ->with('error', 'Periode yang sudah ditutup tidak dapat diubah.');
        }

        $assessmentPeriod->update($request->validated());

        return redirect()
            ->route('admin.assessment-periods.index')
            ->with('success', 'Periode penilaian berhasil diperbarui.');
    }

    /**
     * Hapus periode.
     */
    public function destroy(AssessmentPeriod $assessmentPeriod): RedirectResponse
    {
        // Jika sudah ada data assessment, tidak boleh dihapus
        if ($assessmentPeriod->assessments()->exists()) {
            return redirect()
                ->route('admin.assessment-periods.index')
                ->with('error', 'Periode tidak dapat dihapus karena sudah memiliki data penilaian.');
        }

        $assessmentPeriod->delete();

        return redirect()
            ->route('admin.assessment-periods.index')
            ->with('success', 'Periode penilaian berhasil dihapus.');
    }

    /**
     * Ubah status periode (draft → active → closed).
     */
    public function updateStatus(Request $request, AssessmentPeriod $assessmentPeriod): RedirectResponse
    {
        $request->validate([
            'status' => ['required', 'in:draft,active,closed'],
        ]);

        $newStatus = $request->status;

        // Guard: tidak bisa langsung dari draft ke closed
        if ($assessmentPeriod->isDraft() && $newStatus === 'closed') {
            return redirect()->back()->with('error', 'Periode draft tidak dapat langsung ditutup. Aktifkan terlebih dahulu.');
        }

        // Guard: closed hanya bisa kembali ke active, tidak ke draft
        if ($assessmentPeriod->isClosed() && $newStatus === 'draft') {
            return redirect()->back()->with('error', 'Periode yang sudah ditutup hanya dapat diaktifkan kembali, tidak bisa dikembalikan ke draft.');
        }

        // Jika mengaktifkan periode ini (dari draft maupun closed),
        // pastikan tidak ada periode active lain
        if ($newStatus === 'active') {
            $alreadyActive = AssessmentPeriod::where('status', 'active')
                ->where('id', '!=', $assessmentPeriod->id)
                ->exists();

            if ($alreadyActive) {
                return redirect()->back()->with('error', 'Sudah ada periode yang sedang aktif. Tutup periode tersebut terlebih dahulu.');
            }
        }

        $assessmentPeriod->update(['status' => $newStatus]);

        $label = match ($newStatus) {
            'active' => 'diaktifkan',
            'closed' => 'ditutup',
            default  => 'diubah',
        };

        return redirect()
            ->route('admin.assessment-periods.index')
            ->with('success', "Periode penilaian berhasil {$label}.");
    }
}