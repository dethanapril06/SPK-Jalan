@extends('layouts.admin')

@section('title', 'Detail Hasil MFEP')

@push('styles')
    <link rel="stylesheet" href="{{ asset('template/assets/extensions/sweetalert2/sweetalert2.min.css') }}">
    <style>
        .mfep-detail-table td,
        .mfep-detail-table th {
            border-color: #cfd4da;
        }

        .mfep-detail-table tr.criteria-divider td {
            border-bottom: 1px solid #adb5bd;
        }
    </style>
@endpush

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Detail Hasil Perhitungan MFEP</h3>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.mfep.ranking') }}">Ranking MFEP</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">
                                Detail
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="mb-4">
                <h5 class="mb-3">Informasi Perhitungan</h5>
                <div class="table-responsive">
                    <table class="table table-bordered align-middle mb-0">
                        <tbody>
                            <tr>
                                <th style="width: 220px;">Nama Perhitungan</th>
                                <td>{{ $calculation->name }}</td>
                            </tr>
                            <tr>
                                <th>Kode</th>
                                <td>{{ $calculation->code }}</td>
                            </tr>
                            <tr>
                                <th>Periode</th>
                                <td>{{ $calculation->period?->name ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Status</th>
                                <td>
                                    <span
                                        class="badge bg-{{ $calculation->status === 'finalized' ? 'success' : 'warning' }}">
                                        {{ ucfirst($calculation->status) }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th>Tanggal Perhitungan</th>
                                <td>{{ $calculation->calculation_date?->format('d M Y') ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Selesai pada</th>
                                <td>{{ $calculation->finalized_at?->format('d M Y H:i') ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Dihitung oleh</th>
                                <td>{{ $calculation->calculatedByUser?->name ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Deskripsi</th>
                                <td>{{ $calculation->description ?? '-' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">Detail Hasil Per Alternatif</h5>
                    <a href="{{ route('admin.mfep.pdf', $calculation->id) }}" class="btn btn-danger btn-sm">
                        <i class="bi bi-file-earmark-pdf"></i> Export PDF
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle mfep-detail-table">
                        <thead>
                            <tr>
                                <th style="width: 70px;">Rank</th>
                                <th style="min-width: 220px;">Alternatif</th>
                                <th style="min-width: 180px;">Kriteria</th>
                                <th class="text-center" style="width: 90px;">E</th>
                                <th class="text-center" style="width: 90px;">W</th>
                                <th class="text-end" style="width: 110px;">E × W</th>
                                <th class="text-end" style="width: 140px;">Skor Akhir</th>
                                <th style="width: 140px;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($results as $result)
                                @php
                                    $details = $result->details;
                                    $detailCount = max(1, $details->count());
                                    $totalCheckSum = $details->sum('weighted_value');
                                @endphp

                                @if ($details->isNotEmpty())
                                    @foreach ($details as $detail)
                                        <tr class="criteria-divider">
                                            @if ($loop->first)
                                                <td rowspan="{{ $detailCount }}" class="text-center fw-bold align-top">
                                                    #{{ $result->rank }}
                                                </td>
                                                <td rowspan="{{ $detailCount }}" class="align-top">
                                                    <strong>{{ $result->alternative->name }}</strong><br>
                                                    <small class="text-muted">{{ $result->alternative->code }}</small>
                                                </td>
                                            @endif

                                            <td>
                                                <strong>{{ $detail->criteria->code }}</strong><br>
                                                <small class="text-muted">{{ $detail->criteria->name }}</small>
                                            </td>
                                            <td class="text-center">
                                                {{ number_format((float) $detail->evaluation_value, 4, ',', '.') }}
                                            </td>
                                            <td class="text-center">
                                                {{ number_format((float) $detail->weight, 2, ',', '.') }}
                                            </td>
                                            <td class="text-end">
                                                {{ number_format((float) $detail->weighted_value, 4, ',', '.') }}
                                            </td>

                                            @if ($loop->first)
                                                <td rowspan="{{ $detailCount }}" class="text-end fw-bold align-top">
                                                    {{ number_format((float) $result->weighted_score, 4, ',', '.') }}
                                                </td>
                                                <td rowspan="{{ $detailCount }}" class="align-top">
                                                    @if ($result->is_recommended)
                                                        <span class="badge bg-success">Rekomendasi</span>
                                                    @else
                                                        <span class="badge bg-info">Aktif</span>
                                                    @endif
                                                </td>
                                            @endif
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td class="text-center fw-bold">#{{ $result->rank }}</td>
                                        <td>
                                            <strong>{{ $result->alternative->name }}</strong><br>
                                            <small class="text-muted">{{ $result->alternative->code }}</small>
                                        </td>
                                        <td colspan="4" class="text-center text-muted">Tidak ada detail perhitungan</td>
                                        <td class="text-end fw-bold">
                                            {{ number_format((float) $result->weighted_score, 4, ',', '.') }}
                                        </td>
                                        <td>
                                            @if ($result->is_recommended)
                                                <span class="badge bg-success">Rekomendasi</span>
                                            @else
                                                <span class="badge bg-info">Aktif</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endif
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">Belum ada hasil perhitungan</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-12">
                    <a href="{{ route('admin.mfep.ranking', ['calculation_id' => $calculation->id]) }}"
                        class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Kembali ke Ranking
                    </a>
                </div>
            </div>
        </section>
    </div>

    @push('scripts')
        <script src="{{ asset('template/assets/extensions/sweetalert2/sweetalert2.all.min.js') }}"></script>
    @endpush
@endsection
