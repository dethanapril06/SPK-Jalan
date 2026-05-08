<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSurveyorAssignmentRequest;
use App\Http\Requests\UpdateSurveyorAssignmentRequest;
use App\Models\Assessment;
use App\Models\Alternative;
use App\Models\AssessmentPeriod;
use App\Models\SubCriteria;
use App\Models\Surveyor;
use App\Models\SurveyorAssignment;
use Illuminate\Http\RedirectResponse;
use App\Notifications\TugasBaru;

class SurveyorAssignmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $activePeriod = AssessmentPeriod::where('status', 'active')->first();

        $query = SurveyorAssignment::with(['surveyor.user', 'alternative', 'assignedByUser', 'period'])
            ->latest('assigned_at')
            ->latest('id');

        // Default tampilkan assignment periode aktif, bisa switch ke periode lain
        if (request()->filled('period_id')) {
            $query->where('period_id', request()->period_id);
        } elseif ($activePeriod) {
            $query->where('period_id', $activePeriod->id);
        }

        $assignments = $query->get();

        $periods = AssessmentPeriod::orderByDesc('year')->get();

        return view('admin.assignments.index', compact('assignments', 'activePeriod', 'periods'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $activePeriod = AssessmentPeriod::where('status', 'active')->first();

        if (! $activePeriod) {
            return redirect()->route('admin.assignments.index')
                ->with('error', 'Tidak ada periode penilaian yang sedang aktif. Aktifkan periode terlebih dahulu.');
        }

        $surveyors = Surveyor::with('user')
            ->where('is_active', true)
            ->orderBy('code')
            ->get();
        $alternatives = Alternative::orderBy('order')->orderBy('code')->get();

        return view('admin.assignments.create', compact('surveyors', 'alternatives', 'activePeriod'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSurveyorAssignmentRequest $request): RedirectResponse
    {
        $activePeriod = AssessmentPeriod::where('status', 'active')->first();

        if (! $activePeriod) {
            return redirect()->route('admin.assignments.index')
                ->with('error', 'Tidak ada periode penilaian yang sedang aktif.');
        }

        $assignment = SurveyorAssignment::create([
            ...$request->validated(),
            'period_id'          => $activePeriod->id,
            'assigned_by_user_id' => $request->user()->id,
            'assigned_at'        => now(),
        ]);

        // Kirim notifikasi ke surveyor
        $assignment->load('alternative');
        $assignment->surveyor->user->notify(new TugasBaru($assignment));

        return redirect()->route('admin.assignments.index')
            ->with('success', 'Surveyor berhasil ditugaskan ke alternatif.');
    }

    /**
     * Display the specified resource.
     */
    public function show(SurveyorAssignment $assignment)
    {
        $assignment->load(['surveyor.user', 'alternative', 'assignedByUser', 'period']);

        $assessments = Assessment::with(['subCriteria.criteria', 'assessmentAspect'])
            ->where('surveyor_id', $assignment->surveyor_id)
            ->where('alternative_id', $assignment->alternative_id)
            ->where('period_id', $assignment->period_id)
            ->orderBy('sub_criteria_id')
            ->get();

        $totalSubCriteria     = SubCriteria::count();
        $completedSubCriteria = $assessments->pluck('sub_criteria_id')->unique()->count();
        $progressPercent      = $totalSubCriteria > 0
            ? round(($completedSubCriteria / $totalSubCriteria) * 100, 1)
            : 0;

        return view('admin.assignments.show', compact(
            'assignment',
            'assessments',
            'totalSubCriteria',
            'completedSubCriteria',
            'progressPercent'
        ));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SurveyorAssignment $assignment)
    {
        $assignment->load(['surveyor.user', 'alternative', 'assignedByUser', 'period']);
        $surveyors   = Surveyor::with('user')->orderBy('code')->get();
        $alternatives = Alternative::orderBy('order')->orderBy('code')->get();

        return view('admin.assignments.edit', compact('assignment', 'surveyors', 'alternatives'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSurveyorAssignmentRequest $request, SurveyorAssignment $assignment): RedirectResponse
    {
        $assignment->update($request->validated());

        return redirect()->route('admin.assignments.index')
            ->with('success', 'Penugasan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SurveyorAssignment $assignment): RedirectResponse
    {
        $assignment->delete();

        return redirect()->route('admin.assignments.index')
            ->with('success', 'Penugasan berhasil dihapus.');
    }
}