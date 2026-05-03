<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\Alternative;
use App\Models\Surveyor;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AssessmentReportController extends Controller
{
    public function index(Request $request)
    {
        $surveyors = Surveyor::with('user')
            ->orderBy('code')
            ->get();

        $alternatives = Alternative::query()
            ->orderBy('order')
            ->orderBy('code')
            ->get();

        $query = $this->buildFilteredQuery($request);

        $assessments = $query
            ->orderByDesc('assessed_at')
            ->orderByDesc('id')
            ->get();

        $summary = [
            'total_assessments' => (clone $query)->count(),
            'total_surveyors' => (clone $query)->distinct('surveyor_id')->count('surveyor_id'),
            'total_alternatives' => (clone $query)->distinct('alternative_id')->count('alternative_id'),
        ];

        return view('admin.reports.assessments', [
            'assessments' => $assessments,
            'surveyors' => $surveyors,
            'alternatives' => $alternatives,
            'summary' => $summary,
            'filters' => [
                'surveyor_id' => $request->input('surveyor_id'),
                'alternative_id' => $request->input('alternative_id'),
                'date_from' => $request->input('date_from'),
                'date_to' => $request->input('date_to'),
            ],
        ]);
    }

    public function exportExcel(Request $request): StreamedResponse
    {
        $assessments = $this->buildFilteredQuery($request)
            ->orderByDesc('assessed_at')
            ->orderByDesc('id')
            ->get();

        $filename = 'report-penilaian-'.now()->format('Ymd-His').'.csv';

        return response()->streamDownload(function () use ($assessments) {
            $handle = fopen('php://output', 'w');

            // UTF-8 BOM supaya karakter tampil benar saat dibuka di Excel.
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, [
                'No',
                'Kode Surveyor',
                'Nama Surveyor',
                'Kode Alternatif',
                'Nama Alternatif',
                'Kriteria',
                'Sub Kriteria',
                'Aspek',
                'Nilai',
                'Catatan',
                'Dinilai Pada',
            ]);

            foreach ($assessments as $index => $assessment) {
                fputcsv($handle, [
                    $index + 1,
                    $assessment->surveyor?->code ?? '-',
                    $assessment->surveyor?->user?->name ?? '-',
                    $assessment->alternative?->code ?? '-',
                    $assessment->alternative?->name ?? '-',
                    $assessment->subCriteria?->criteria?->name ?? '-',
                    $assessment->subCriteria?->name ?? '-',
                    $assessment->assessmentAspect?->name ?? '-',
                    $assessment->assessmentAspect?->value ?? '-',
                    $assessment->notes ?: '-',
                    $assessment->assessed_at?->format('d-m-Y H:i') ?? '-',
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function exportPdf(Request $request)
    {
        $assessments = $this->buildFilteredQuery($request)
            ->orderByDesc('assessed_at')
            ->orderByDesc('id')
            ->get();

        $pdf = Pdf::loadView('admin.reports.assessments-pdf', [
            'assessments' => $assessments,
            'generatedAt' => now(),
        ])->setPaper('a4', 'landscape');

        return $pdf->download('report-penilaian-'.now()->format('Ymd-His').'.pdf');
    }

    private function buildFilteredQuery(Request $request): Builder
    {
        $query = Assessment::query()
            ->with([
                'surveyor.user',
                'alternative',
                'subCriteria.criteria',
                'assessmentAspect',
            ]);

        if ($request->filled('surveyor_id')) {
            $query->where('surveyor_id', (int) $request->integer('surveyor_id'));
        }

        if ($request->filled('alternative_id')) {
            $query->where('alternative_id', (int) $request->integer('alternative_id'));
        }

        if ($request->filled('date_from')) {
            $query->whereDate('assessed_at', '>=', $request->string('date_from')->toString());
        }

        if ($request->filled('date_to')) {
            $query->whereDate('assessed_at', '<=', $request->string('date_to')->toString());
        }

        return $query;
    }
}
