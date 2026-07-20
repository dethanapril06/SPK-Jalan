<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Report Penilaian</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #111827;
        }

        .header {
            margin-bottom: 14px;
        }

        .title {
            font-size: 16px;
            font-weight: 700;
            margin: 0;
        }

        .subtitle {
            margin: 3px 0 0;
            color: #4b5563;
            font-size: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        th,
        td {
            border: 1px solid #d1d5db;
            padding: 5px;
            vertical-align: top;
            word-wrap: break-word;
        }

        th {
            background: #f3f4f6;
            text-align: left;
            font-weight: 700;
        }

        .muted {
            color: #6b7280;
            font-size: 10px;
        }
    </style>
</head>

<body>
    <div class="header">
        <p class="title">Report Penilaian Surveyor</p>
        <p class="subtitle">Digenerate pada: {{ $generatedAt->format('d-m-Y H:i') }}</p>
        <p class="subtitle">Total data: {{ $totalCount ?? (is_countable($assessments) ? count($assessments) : 0) }} baris</p>
        @if (!empty(array_filter($filters ?? [])))
            <p class="subtitle">Filter aktif diterapkan pada report ini.</p>
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 4%;">No</th>
                <th style="width: 10%;">Periode</th>
                <th style="width: 10%;">Surveyor</th>
                <th style="width: 12%;">Alternatif</th>
                <th style="width: 12%;">Kriteria</th>
                <th style="width: 12%;">Sub Kriteria</th>
                <th style="width: 12%;">Aspek</th>
                <th style="width: 5%;">Nilai</th>
                <th style="width: 14%;">Catatan</th>
                <th style="width: 9%;">Dinilai Pada</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($assessments as $assessment)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>
                        {{ $assessment->period?->name ?? '-' }}
                        <div class="muted">{{ $assessment->period?->year ?? '-' }}</div>
                    </td>
                    <td>
                        {{ $assessment->surveyor?->code ?? '-' }}
                        <div class="muted">{{ $assessment->surveyor?->user?->name ?? '-' }}</div>
                    </td>
                    <td>
                        {{ $assessment->alternative?->code ?? '-' }}
                        <div class="muted">{{ $assessment->alternative?->name ?? '-' }}</div>
                    </td>
                    <td>{{ $assessment->subCriteria?->criteria?->name ?? '-' }}</td>
                    <td>{{ $assessment->subCriteria?->name ?? '-' }}</td>
                    <td>{{ $assessment->assessmentAspect?->name ?? '-' }}</td>
                    <td>{{ $assessment->assessmentAspect?->value ?? '-' }}</td>
                    <td>{{ $assessment->notes ?: '-' }}</td>
                    <td>{{ $assessment->assessed_at?->format('d-m-Y H:i') ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="10">Tidak ada data penilaian.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>

</html>
