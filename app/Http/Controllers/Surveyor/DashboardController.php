<?php

namespace App\Http\Controllers\Surveyor;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\SubCriteria;
use App\Models\SurveyorAssignment;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Surveyor dashboard dengan daftar tugas mereka
     */
    public function index()
    {
        $surveyor = auth()->user()->surveyor;

        if (!$surveyor) {
            return redirect()->route('login')->with('error', 'Data surveyor tidak ditemukan.');
        }

        $assignments = SurveyorAssignment::where('surveyor_id', $surveyor->id)
            ->with(['alternative', 'assessments'])
            ->latest('assigned_at')
            ->get();

        // Hitung progress per assignment
        $assignments = $assignments->map(function ($assignment) {
            $totalSubCriteria = DB::table('sub_criteria')->count();
            $completedSubCriteria = DB::table('assessments')
                ->where('surveyor_id', $assignment->surveyor_id)
                ->where('alternative_id', $assignment->alternative_id)
                ->distinct('sub_criteria_id')
                ->count();

            $assignment->progress_percent = $totalSubCriteria > 0 
                ? round(($completedSubCriteria / $totalSubCriteria) * 100, 1)
                : 0;
            $assignment->completed_sub_criteria = $completedSubCriteria;
            $assignment->total_sub_criteria = $totalSubCriteria;

            return $assignment;
        });

        $statusCounts = $assignments->groupBy('status')->map->count();

        return view('surveyor.dashboard', [
            'surveyor' => $surveyor,
            'assignments' => $assignments,
            'statusCounts' => $statusCounts,
            'totalAssignments' => $assignments->count(),
        ]);
    }

    /**
     * Detail tugas dan halaman input penilaian
     */
    public function showTask(SurveyorAssignment $assignment)
    {
        $surveyor = auth()->user()->surveyor;

        if (! $surveyor) {
            return redirect()->route('login')->with('error', 'Data surveyor tidak ditemukan.');
        }

        if ((int) $assignment->surveyor_id !== (int) $surveyor->id) {
            abort(403, 'Anda tidak memiliki akses ke tugas ini.');
        }

        $assignment->load('alternative');

        $assessments = Assessment::query()
            ->where('surveyor_id', $assignment->surveyor_id)
            ->where('alternative_id', $assignment->alternative_id)
            ->get();

        // Cek progress penilaian
        $subCriteria = SubCriteria::query()
            ->with('criteria')
            ->orderBy('criteria_id')
            ->orderBy('order')
            ->get();

        $subCriteria = $subCriteria->map(function ($item) use ($assessments) {
            $assessment = $assessments
                ->firstWhere('sub_criteria_id', $item->id);

            $item->assessment = $assessment;
            $item->is_completed = $assessment !== null;

            return $item;
        });

        return view('surveyor.task.show', [
            'assignment' => $assignment,
            'surveyor' => $surveyor,
            'subCriteria' => $subCriteria,
        ]);
    }
}
