<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\AssessmentPeriod;
use App\Models\Alternative;
use App\Models\Criteria;
use App\Models\SubCriteria;
use App\Models\Surveyor;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AssessmentReportController extends Controller
{
    public function index(Request $request)
    {
        $periods = AssessmentPeriod::orderByDesc('year')
            ->orderByDesc('id')
            ->get();

        $surveyors = Surveyor::with('user')
            ->orderBy('code')
            ->get();

        $alternatives = Alternative::query()
            ->orderBy('order')
            ->orderBy('code')
            ->get();

        $criterias = Criteria::orderBy('order')
            ->orderBy('code')
            ->get();

        $subCriterias = SubCriteria::with('criteria')
            ->orderBy('criteria_id')
            ->orderBy('order')
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
            'periods' => $periods,
            'surveyors' => $surveyors,
            'alternatives' => $alternatives,
            'criterias' => $criterias,
            'subCriterias' => $subCriterias,
            'summary' => $summary,
            'filters' => [
                'period_id' => $request->input('period_id'),
                'surveyor_id' => $request->input('surveyor_id'),
                'alternative_id' => $request->input('alternative_id'),
                'criteria_id' => $request->input('criteria_id'),
                'sub_criteria_id' => $request->input('sub_criteria_id'),
                'date_from' => $request->input('date_from'),
                'date_to' => $request->input('date_to'),
            ],
        ]);
    }

    public function exportExcel(Request $request): StreamedResponse
    {
        $query = $this->buildFilteredQuery($request)
            ->orderByDesc('assessed_at')
            ->orderByDesc('id');

        $filename = 'report-penilaian-'.now()->format('Ymd-His').'.csv';

        return response()->streamDownload(function () use ($query) {
            $handle = fopen('php://output', 'w');

            // UTF-8 BOM supaya karakter tampil benar saat dibuka di Excel.
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, [
                'No',
                'Periode',
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

            $index = 0;
            // Gunakan lazy(500) agar relasi eager loading (with) dimuat per batch 500 baris,
            // mencegah N+1 query (7500+ query) yang terjadi jika menggunakan cursor().
            foreach ($query->lazy(500) as $assessment) {
                fputcsv($handle, [
                    $index + 1,
                    $assessment->period ? ($assessment->period->name . ' (' . $assessment->period->year . ')') : '-',
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
                $index++;
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function exportPdf(Request $request)
    {
        // Tingkatkan batas waktu eksekusi dan memori khusus proses generate PDF besar
        set_time_limit(300);
        ini_set('max_execution_time', '300');
        ini_set('memory_limit', '512M');

        $query = $this->buildFilteredQuery($request)
            ->orderByDesc('assessed_at')
            ->orderByDesc('id');

        // Gunakan get() agar relasi eager loading dimuat sekaligus hanya dalam 6 query SQL,
        // mencegah N+1 query yang membuat server hang/timeout di production.
        $assessments = $query->get();
        $totalCount = $assessments->count();

        $pdf = Pdf::loadView('admin.reports.assessments-pdf', [
            'assessments' => $assessments,
            'totalCount'  => $totalCount,
            'generatedAt' => now(),
            'filters'     => [
                'period_id' => $request->input('period_id'),
                'surveyor_id' => $request->input('surveyor_id'),
                'alternative_id' => $request->input('alternative_id'),
                'criteria_id' => $request->input('criteria_id'),
                'sub_criteria_id' => $request->input('sub_criteria_id'),
                'date_from' => $request->input('date_from'),
                'date_to' => $request->input('date_to'),
            ],
        ])
        ->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled'      => false,
            'defaultFont'          => 'sans-serif',
        ])
        ->setPaper('a4', 'landscape');

        return $pdf->download('report-penilaian-'.now()->format('Ymd-His').'.pdf');
    }

    private function buildFilteredQuery(Request $request): Builder
    {
        $query = Assessment::query()
            ->with([
                'period',
                'surveyor.user',
                'alternative',
                'subCriteria.criteria',
                'assessmentAspect',
            ]);

        if ($request->filled('period_id')) {
            $query->where('period_id', (int) $request->integer('period_id'));
        }

        if ($request->filled('surveyor_id')) {
            $query->where('surveyor_id', (int) $request->integer('surveyor_id'));
        }

        if ($request->filled('alternative_id')) {
            $query->where('alternative_id', (int) $request->integer('alternative_id'));
        }

        if ($request->filled('criteria_id')) {
            $query->whereHas('subCriteria', function ($q) use ($request) {
                $q->where('criteria_id', (int) $request->integer('criteria_id'));
            });
        }

        if ($request->filled('sub_criteria_id')) {
            $query->where('sub_criteria_id', (int) $request->integer('sub_criteria_id'));
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
