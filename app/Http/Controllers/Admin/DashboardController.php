<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AssessmentAspect;
use App\Models\Alternative;
use App\Models\Criteria;
use App\Models\SubCriteria;
use App\Models\Surveyor;
use App\Models\SurveyorAssignment;
use App\Models\User;
use App\Models\Assessment;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $statusCounts = SurveyorAssignment::query()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $recentAssignments = SurveyorAssignment::with(['surveyor.user', 'alternative'])
            ->latest('assigned_at')
            ->latest('id')
            ->limit(5)
            ->get();

        $assignmentsTotal = SurveyorAssignment::count();

        // Hitung total assessment yang sudah masuk
        $assessmentCount = Assessment::count();

        // Hitung total sub-kriteria yang harus dinilai per alternatif
        $totalSubCriteriaCount = SubCriteria::count();

        // Cari alternatif dan berapa sub-kriteria yang sudah dinilai
        $alternativesWithAssessmentCounts = DB::table('alternatives')
            ->leftJoin('assessments', 'alternatives.id', '=', 'assessments.alternative_id')
            ->selectRaw('alternatives.id, alternatives.code, alternatives.name, COUNT(DISTINCT assessments.sub_criteria_id) as assessed_sub_criteria')
            ->groupBy('alternatives.id', 'alternatives.code', 'alternatives.name')
            ->orderBy('assessed_sub_criteria')
            ->limit(10)
            ->get();

        // Tentukan alternatif yang belum lengkap dinilai
        $incompleteAlternatives = $alternativesWithAssessmentCounts
            ->where('assessed_sub_criteria', '<', $totalSubCriteriaCount)
            ->values();

        // Ringkas surveyor terbanyak assessment
        $topSurveyors = DB::table('assessments')
            ->join('surveyors', 'assessments.surveyor_id', '=', 'surveyors.id')
            ->join('users', 'surveyors.user_id', '=', 'users.id')
            ->selectRaw('surveyors.code, users.name, COUNT(*) as total_assessments')
            ->groupBy('surveyors.code', 'users.name')
            ->orderByDesc('total_assessments')
            ->limit(5)
            ->get();

        return view('admin.dashboard', [
            'criteriaCount' => Criteria::count(),
            'subCriteriaCount' => SubCriteria::count(),
            'assessmentAspectCount' => AssessmentAspect::count(),
            'alternativeCount' => Alternative::count(),
            'surveyorCount' => Surveyor::count(),
            'activeSurveyorCount' => Surveyor::where('is_active', true)->count(),
            'adminUserCount' => User::whereIn('role', ['admin', 'kepala_dinas'])->count(),
            'assignmentsTotal' => $assignmentsTotal,
            'assignedCount' => (int) ($statusCounts['assigned'] ?? 0),
            'inProgressCount' => (int) ($statusCounts['in_progress'] ?? 0),
            'submittedCount' => (int) ($statusCounts['submitted'] ?? 0),
            'reviewedCount' => (int) ($statusCounts['reviewed'] ?? 0),
            'recentAssignments' => $recentAssignments,
            'assessmentCount' => $assessmentCount,
            'incompleteAlternatives' => $incompleteAlternatives,
            'topSurveyors' => $topSurveyors,
            'totalSubCriteriaPerAlternative' => $totalSubCriteriaCount,
        ]);
    }
}
