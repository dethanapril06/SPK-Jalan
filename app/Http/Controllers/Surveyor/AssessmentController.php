<?php

namespace App\Http\Controllers\Surveyor;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\AssessmentAspect;
use App\Models\SubCriteria;
use App\Models\SurveyorAssignment;
use Illuminate\Http\Request;

class AssessmentController extends Controller
{
    /**
     * Show form untuk input penilaian per sub-kriteria
     */
    public function show(SurveyorAssignment $assignment)
    {
        $surveyor = auth()->user()->surveyor;

        if (! $surveyor) {
            return redirect()->route('login')->with('error', 'Data surveyor tidak ditemukan.');
        }

        if ((int) $assignment->surveyor_id !== (int) $surveyor->id) {
            abort(403, 'Anda tidak memiliki akses ke tugas ini.');
        }

        $assignment->load(['alternative']);

        // Cek apakah ada penugasan yang aktif
        if (!in_array($assignment->status, ['assigned', 'in_progress'])) {
            return back()->with('error', 'Penugasan sudah diselesaikan.');
        }

        return redirect()->route('surveyor.task.show', $assignment);
    }

    /**
     * Form edit untuk input penilaian per sub-kriteria
     */
    public function edit(SurveyorAssignment $assignment, Request $request)
    {
        $surveyor = auth()->user()->surveyor;

        if (! $surveyor) {
            return redirect()->route('login')->with('error', 'Data surveyor tidak ditemukan.');
        }

        if ((int) $assignment->surveyor_id !== (int) $surveyor->id) {
            abort(403, 'Anda tidak memiliki akses ke tugas ini.');
        }

        $assignment->load(['alternative']);

        $subCriteriaId = $request->query('sub_criteria_id');
        $subCriteria = SubCriteria::findOrFail($subCriteriaId);
        $subCriteria->load(['criteria', 'assessmentAspects']);

        // Cari assessment yang sudah ada
        $existingAssessment = Assessment::where('surveyor_id', $surveyor->id)
            ->where('alternative_id', $assignment->alternative_id)
            ->where('sub_criteria_id', $subCriteriaId)
            ->first();

        $aspects = $subCriteria->assessmentAspects()->orderBy('order')->get();

        return view('surveyor.assessment.edit', [
            'assignment' => $assignment,
            'surveyor' => $surveyor,
            'subCriteria' => $subCriteria,
            'assessment' => $existingAssessment,
            'aspects' => $aspects,
        ]);
    }

    /**
     * Simpan penilaian
     */
    public function update(SurveyorAssignment $assignment, Request $request)
    {
        $surveyor = auth()->user()->surveyor;

        if (! $surveyor) {
            return redirect()->route('login')->with('error', 'Data surveyor tidak ditemukan.');
        }

        if ((int) $assignment->surveyor_id !== (int) $surveyor->id) {
            abort(403, 'Anda tidak memiliki akses ke tugas ini.');
        }

        $validated = $request->validate([
            'sub_criteria_id' => ['required', 'integer', 'exists:sub_criteria,id'],
            'assessment_aspect_id' => ['required', 'integer', 'exists:assessment_aspects,id'],
            'notes' => ['nullable', 'string'],
        ]);

        // Cek sub-kriteria valid untuk alternative ini
        $subCriteria = SubCriteria::findOrFail($validated['sub_criteria_id']);
        $aspect = AssessmentAspect::findOrFail($validated['assessment_aspect_id']);

        if ((int)$aspect->sub_criteria_id !== (int)$subCriteria->id) {
            return back()->withErrors(['assessment_aspect_id' => 'Aspek tidak sesuai dengan sub-kriteria.']);
        }

        // Cek atau buat assessment
        $assessment = Assessment::updateOrCreate(
            [
                'surveyor_id' => $surveyor->id,
                'alternative_id' => $assignment->alternative_id,
                'sub_criteria_id' => $validated['sub_criteria_id'],
            ],
            [
                'assessment_aspect_id' => $validated['assessment_aspect_id'],
                'notes' => $validated['notes'] ?? null,
                'assessed_at' => now(),
            ]
        );

        // Update status assignment jika masih assigned
        if ($assignment->status === 'assigned') {
            $assignment->update([
                'status' => 'in_progress',
                'started_at' => $assignment->started_at ?? now(),
            ]);
        }

        // Cek apakah semua sub-kriteria sudah dinilai
        $totalSubCriteria = SubCriteria::count();
        $completedSubCriteria = Assessment::where('surveyor_id', $surveyor->id)
            ->where('alternative_id', $assignment->alternative_id)
            ->distinct('sub_criteria_id')
            ->count();

        if ($completedSubCriteria >= $totalSubCriteria) {
            $assignment->update([
                'status' => 'submitted',
                'submitted_at' => now(),
            ]);

            return redirect()->route('surveyor.task.show', $assignment)
                ->with('success', 'Semua penilaian selesai dan sudah dikumpulkan!');
        }

        return redirect()->route('surveyor.task.show', $assignment)
            ->with('success', 'Penilaian berhasil disimpan.');
    }
}
