@extends('layouts.kepala-dinas')

@section('title', 'Dashboard Kepala Dinas')

@section('content')
    <div class="page-heading">
        <div class="page-title mb-3">
            <div class="row">
                <div class="col-12 col-md-8 order-md-1 order-last">
                    <h3>Dashboard Kepala Dinas</h3>
                    <p class="text-muted mb-0">Ringkasan hasil perhitungan MFEP untuk pengambilan keputusan.</p>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="row g-3">
                <div class="col-12 col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="text-muted">Total Alternatif</h6>
                            <h4 class="mb-0">{{ $alternativeCount }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="text-muted">Perhitungan Final</h6>
                            <h4 class="mb-0">{{ $finalizedCalculationsCount }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="text-muted">Rekomendasi Terakhir</h6>
                            <h6 class="mb-0">
                                {{ $recommendedResult?->alternative?->name ?? '-' }}
                            </h6>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title mb-0">Hasil Perhitungan Terbaru</h5>
                        <small class="text-muted">
                            @if ($latestCalculation)
                                {{ $latestCalculation->name }}
                                ({{ $latestCalculation->finalized_at?->format('d M Y H:i') }})
                            @else
                                Belum ada perhitungan final.
                            @endif
                        </small>
                    </div>
                    <a href="{{ route('kepala-dinas.mfep.ranking') }}" class="btn btn-sm btn-primary">
                        Lihat Ranking Lengkap
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped mb-0">
                            <thead>
                                <tr>
                                    <th>Rank</th>
                                    <th>Alternatif</th>
                                    <th>Skor Akhir</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($latestResults as $result)
                                    <tr>
                                        <td>#{{ $result->rank }}</td>
                                        <td>
                                            <strong>{{ $result->alternative->name }}</strong><br>
                                            <small class="text-muted">{{ $result->alternative->code }}</small>
                                        </td>
                                        <td>{{ number_format((float) $result->weighted_score, 4, ',', '.') }}</td>
                                        <td>
                                            @if ($result->is_recommended)
                                                <span class="badge bg-success">Rekomendasi</span>
                                            @else
                                                <span class="badge bg-info">Aktif</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">Belum ada data hasil
                                            perhitungan</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
