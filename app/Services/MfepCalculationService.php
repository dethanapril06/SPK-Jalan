<?php

namespace App\Services;

use App\Models\Alternative;
use App\Models\Criteria;
use App\Models\SubCriteria;
use App\Models\Assessment;
use App\Models\MfepCalculation;
use App\Models\MfepResult;
use App\Models\MfepResultDetail;
use Illuminate\Support\Facades\DB;

class MfepCalculationService
{
    public function calculate(int $mfepCalculationId): MfepCalculation
    {
        $mfepCalculation = MfepCalculation::findOrFail($mfepCalculationId);
        
        DB::transaction(function () use ($mfepCalculation) {
            $mfepCalculation->update([
                'started_at' => now(),
                'status' => 'draft',
            ]);

            $alternatives = Alternative::orderBy('order')->get();
            $criteria = Criteria::orderBy('order')->get();

            foreach ($alternatives as $alternative) {
                $this->calculateForAlternative($mfepCalculation, $alternative, $criteria);
            }

            $this->assignRankings($mfepCalculation);

            $mfepCalculation->update([
                'status' => 'finalized',
                'finalized_at' => now(),
            ]);
        });

        return $mfepCalculation->refresh();
    }

    private function calculateForAlternative(MfepCalculation $mfepCalculation, Alternative $alternative, $criteria): void
    {
        $totalWeightedScore = 0;
        $resultsToSave = [];

        // Loop setiap Kriteria
        foreach ($criteria as $criterion) {
            $subCriteriaList = SubCriteria::where('criteria_id', $criterion->id)
                ->orderBy('order')
                ->get();

            $subCriteriaScores = [];

            // Kumpulkan skor semua sub-kriteria dalam kriteria ini
            foreach ($subCriteriaList as $subCriteria) {
                $subCriteriaScore = $this->calculateSubCriteriaScore(
                    $alternative->id,
                    $subCriteria->id
                );

                if ($subCriteriaScore !== null) {
                    $subCriteriaScores[] = $subCriteriaScore;
                }
            }

            // Hitung Nilai Evaluasi (E) = rata-rata sub-kriteria
            $evaluationValue = count($subCriteriaScores) > 0 
                ? array_sum($subCriteriaScores) / count($subCriteriaScores)
                : 0;

            // Hitung E × W (Weighted Value)
            $weight = (float) $criterion->weight;
            $weightedValue = $evaluationValue * $weight;

            // Akumulasi ke total skor
            $totalWeightedScore += $weightedValue;

            // Simpan detail per kriteria (bukan per sub-kriteria)
            $resultsToSave[] = [
                'criteria_id' => $criterion->id,
                'evaluation_value' => $evaluationValue,
                'weight' => $weight,
                'weighted_value' => $weightedValue,
            ];
        }

        // Skor Akhir = Σ(E × W) - TIDAK dinormalisasi
        $finalScore = $totalWeightedScore;

        $mfepResult = MfepResult::create([
            'mfep_calculation_id' => $mfepCalculation->id,
            'alternative_id' => $alternative->id,
            'raw_score' => $finalScore,
            'weighted_score' => $finalScore,
        ]);

        foreach ($resultsToSave as $detailData) {
            $detailData['mfep_result_id'] = $mfepResult->id;
            MfepResultDetail::create($detailData);
        }
    }

    private function calculateSubCriteriaScore(int $alternativeId, int $subCriteriaId): ?float
    {
        $assessments = Assessment::where('alternative_id', $alternativeId)
            ->where('sub_criteria_id', $subCriteriaId)
            ->with('assessmentAspect')
            ->get();

        if ($assessments->isEmpty()) {
            return null;
        }

        $values = $assessments->map(function ($assessment) {
            return $assessment->assessmentAspect?->value ?? 0;
        })->toArray();

        return array_sum($values) / count($values);
    }

    private function assignRankings(MfepCalculation $mfepCalculation): void
    {
        $results = MfepResult::where('mfep_calculation_id', $mfepCalculation->id)
            ->orderByDesc('weighted_score')
            ->get();

        foreach ($results->values() as $index => $result) {
            $result->update(['rank' => $index + 1]);
        }

        $topResult = $results->first();
        if ($topResult) {
            $topResult->update(['is_recommended' => true]);
        }
    }
}