@extends('layouts.kepala-dinas')

@section('title', 'Ranking Alternatif MFEP')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Ranking Alternatif MFEP</h3>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('kepala-dinas.dashboard') }}">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Ranking MFEP</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="row">
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Riwayat Perhitungan</h5>
                        </div>
                        <div class="card-body">
                            @forelse ($calculations as $calc)
                                <a href="{{ route('kepala-dinas.mfep.ranking', ['calculation_id' => $calc->id]) }}"
                                    class="text-decoration-none">
                                    <div
                                        class="p-3 mb-2 border rounded {{ $selectedCalculation?->id === $calc->id ? 'bg-light-primary' : '' }}">
                                        <h6 class="mb-1">{{ $calc->name }}</h6>
                                        <small class="text-muted">{{ $calc->finalized_at?->format('d M Y H:i') }}</small>
                                    </div>
                                </a>
                            @empty
                                <p class="text-muted mb-0">Belum ada perhitungan MFEP.</p>
                            @endforelse

                            @if ($calculations->count() > 0)
                                {{ $calculations->links() }}
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-lg-8">
                    @if ($selectedCalculation && $results->count() > 0)
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Hasil Perhitungan - {{ $selectedCalculation->name }}</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead>
                                            <tr>
                                                <th>Rank</th>
                                                <th>Alternatif</th>
                                                <th>Skor Akhir</th>
                                                <th>Status</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($results as $result)
                                                <tr class="{{ $result->is_recommended ? 'table-success' : '' }}">
                                                    <td>#{{ $result->rank }}</td>
                                                    <td>
                                                        <strong>{{ $result->alternative->name }}</strong><br>
                                                        <small class="text-muted">{{ $result->alternative->code }}</small>
                                                    </td>
                                                    <td>{{ number_format((float) $result->weighted_score, 4, ',', '.') }}
                                                    </td>
                                                    <td>
                                                        @if ($result->is_recommended)
                                                            <span class="badge bg-success">Rekomendasi</span>
                                                        @else
                                                            <span class="badge bg-info">Aktif</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <a href="{{ route('kepala-dinas.mfep.show', $selectedCalculation->id) }}"
                                                            class="btn btn-sm btn-info">
                                                            <i class="bi bi-eye"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @elseif ($selectedCalculation)
                        <div class="card">
                            <div class="card-body text-center text-muted py-5">Tidak ada hasil perhitungan.</div>
                        </div>
                    @else
                        <div class="card">
                            <div class="card-body text-center text-muted py-5">Pilih perhitungan dari panel kiri.</div>
                        </div>
                    @endif
                </div>
            </div>
        </section>
    </div>
@endsection
