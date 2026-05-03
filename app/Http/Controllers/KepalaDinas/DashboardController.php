<?php

namespace App\Http\Controllers\KepalaDinas;

use App\Http\Controllers\Controller;
use App\Models\Alternative;
use App\Models\MfepCalculation;
use App\Models\MfepResult;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $finalizedCalculationsCount = MfepCalculation::where('status', 'finalized')->count();

        $latestCalculation = MfepCalculation::where('status', 'finalized')
            ->orderByDesc('finalized_at')
            ->orderByDesc('id')
            ->first();

        $latestResults = collect();
        $recommendedResult = null;

        if ($latestCalculation) {
            $latestResults = MfepResult::where('mfep_calculation_id', $latestCalculation->id)
                ->with('alternative')
                ->orderBy('rank')
                ->get();

            $recommendedResult = $latestResults->firstWhere('is_recommended', true) ?? $latestResults->first();
        }

        return view('kepala-dinas.dashboard', [
            'alternativeCount' => Alternative::count(),
            'finalizedCalculationsCount' => $finalizedCalculationsCount,
            'latestCalculation' => $latestCalculation,
            'latestResults' => $latestResults,
            'recommendedResult' => $recommendedResult,
        ]);
    }
}
