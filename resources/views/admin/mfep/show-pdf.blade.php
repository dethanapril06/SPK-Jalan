<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Report MFEP - {{ $calculation->code }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #1f2937;
        }

        h1,
        h2,
        h3,
        h4,
        p {
            margin: 0;
        }

        .mb-8 {
            margin-bottom: 8px;
        }

        .mb-12 {
            margin-bottom: 12px;
        }

        .mb-16 {
            margin-bottom: 16px;
        }

        .muted {
            color: #6b7280;
        }

        .header {
            border-bottom: 1px solid #d1d5db;
            padding-bottom: 8px;
            margin-bottom: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .meta th,
        .meta td {
            border: 1px solid #d1d5db;
            padding: 6px 8px;
            text-align: left;
            vertical-align: top;
        }

        .detail th,
        .detail td {
            border: 1px solid #d1d5db;
            padding: 5px 6px;
            vertical-align: top;
            word-break: break-word;
        }

        .detail thead th {
            background: #f3f4f6;
            font-weight: 700;
            text-align: center;
        }

        .detail {
            table-layout: fixed;
        }

        .result-block {
            page-break-inside: avoid;
            margin-bottom: 12px;
        }

        .result-title {
            font-weight: 700;
            margin-bottom: 4px;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .recommended {
            color: #065f46;
            font-weight: 700;
        }
    </style>
</head>

<body>
    <div class="header">
        <h2 class="mb-8">Report Detail Hasil MFEP</h2>
        <p class="muted">Generated: {{ $generatedAt->format('d-m-Y H:i') }}</p>
    </div>

    <table class="meta mb-16">
        <tbody>
            <tr>
                <th style="width: 180px;">Nama Perhitungan</th>
                <td>{{ $calculation->name }}</td>
                <th style="width: 180px;">Kode</th>
                <td>{{ $calculation->code }}</td>
            </tr>
            <tr>
                <th>Status</th>
                <td>{{ ucfirst($calculation->status) }}</td>
                <th>Tanggal Perhitungan</th>
                <td>{{ $calculation->calculation_date?->format('d-m-Y') ?? '-' }}</td>
            </tr>
            <tr>
                <th>Periode</th>
                <td>{{ $calculation->period?->name ?? '-' }}</td>
                <th></th>
                <td></td>
            </tr>
            <tr>
                <th>Selesai pada</th>
                <td>{{ $calculation->finalized_at?->format('d-m-Y H:i') ?? '-' }}</td>
                <th>Dihitung oleh</th>
                <td>{{ $calculation->calculatedByUser?->name ?? '-' }}</td>
            </tr>
        </tbody>
    </table>

    @forelse ($results as $result)
        @php
            $details = $result->details;
            $detailCount = max(1, $details->count());
        @endphp

        <div class="result-block">
            <div class="result-title">Alternatif: {{ $result->alternative->code }} - {{ $result->alternative->name }}
            </div>
            <table class="detail">
                <thead>
                    <tr>
                        <th style="width: 50px;">Rank</th>
                        <th style="width: 180px;">Alternatif</th>
                        <th style="width: 140px;">Kriteria</th>
                        <th style="width: 70px;">E</th>
                        <th style="width: 60px;">W</th>
                        <th style="width: 80px;">E x W</th>
                        <th style="width: 90px;">Skor Akhir</th>
                        <th style="width: 100px;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @if ($details->isNotEmpty())
                        @foreach ($details as $detail)
                            <tr>
                                @if ($loop->first)
                                    <td rowspan="{{ $detailCount }}" class="text-center">
                                        #{{ $result->rank }}
                                    </td>
                                    <td rowspan="{{ $detailCount }}">
                                        {{ $result->alternative->code }} - {{ $result->alternative->name }}
                                    </td>
                                @endif

                                <td>{{ $detail->criteria->code }} - {{ $detail->criteria->name }}</td>
                                <td class="text-center">
                                    {{ number_format((float) $detail->evaluation_value, 4, ',', '.') }}</td>
                                <td class="text-center">{{ number_format((float) $detail->weight, 2, ',', '.') }}</td>
                                <td class="text-right">
                                    {{ number_format((float) $detail->weighted_value, 4, ',', '.') }}</td>

                                @if ($loop->first)
                                    <td rowspan="{{ $detailCount }}" class="text-right">
                                        {{ number_format((float) $result->weighted_score, 4, ',', '.') }}
                                    </td>
                                    <td rowspan="{{ $detailCount }}"
                                        class="text-center {{ $result->is_recommended ? 'recommended' : '' }}">
                                        {{ $result->is_recommended ? 'Rekomendasi' : 'Aktif' }}
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td class="text-center">#{{ $result->rank }}</td>
                            <td>{{ $result->alternative->code }} - {{ $result->alternative->name }}</td>
                            <td colspan="4" class="text-center">Tidak ada detail perhitungan</td>
                            <td class="text-right">{{ number_format((float) $result->weighted_score, 4, ',', '.') }}
                            </td>
                            <td class="text-center {{ $result->is_recommended ? 'recommended' : '' }}">
                                {{ $result->is_recommended ? 'Rekomendasi' : 'Aktif' }}
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    @empty
        <table class="detail">
            <tbody>
                <tr>
                    <td class="text-center">Belum ada hasil perhitungan</td>
                </tr>
            </tbody>
        </table>
    @endforelse
</body>

</html>
