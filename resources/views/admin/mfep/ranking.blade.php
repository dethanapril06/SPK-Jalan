@extends('layouts.admin')

@section('title', 'Ranking Alternatif MFEP')

@push('styles')
    <link rel="stylesheet" href="{{ asset('template/assets/extensions/apexcharts/apexcharts.css') }}">
    <link rel="stylesheet" href="{{ asset('template/assets/extensions/sweetalert2/sweetalert2.min.css') }}">
@endpush

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Ranking Alternatif MFEP</h3>
                    <a href="{{ route('admin.mfep.create') }}" class="btn btn-sm btn-primary mb-2">
                        <i class="bi bi-calculator"></i> Hitung Skor Baru
                    </a>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">
                                Ranking MFEP
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            @if (session('success'))
                <div class="alert alert-light-success color-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-light-danger color-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="row">
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Riwayat Perhitungan</h5>
                        </div>
                        <div class="card-body">
                            @forelse ($calculations as $calc)
                                <a href="{{ route('admin.mfep.ranking', ['calculation_id' => $calc->id]) }}"
                                    class="text-decoration-none">
                                    <div
                                        class="calculation-item p-3 mb-2 border rounded {{ $selectedCalculation?->id === $calc->id ? 'bg-light-primary' : '' }}">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <h6 class="mb-1">{{ $calc->name }}</h6>
                                                <small class="text-muted">
                                                    {{ $calc->finalized_at->format('d M Y H:i') }}
                                                </small>
                                                <div class="mt-2">
                                                    <span
                                                        class="badge bg-{{ $calc->status === 'finalized' ? 'success' : 'warning' }}">
                                                        {{ ucfirst($calc->status) }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="btn-group-vertical gap-1">
                                                <form action="{{ route('admin.mfep.destroy', $calc->id) }}" method="POST"
                                                    class="d-inline delete-form" data-name="{{ $calc->name }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-sm btn-danger delete-btn">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            @empty
                                <div class="text-center text-muted py-4">
                                    <p>Belum ada perhitungan MFEP</p>
                                    <a href="{{ route('admin.mfep.create') }}" class="btn btn-primary btn-sm">
                                        Buat Perhitungan Pertama
                                    </a>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="col-lg-8">
                    @if ($selectedCalculation && $results->count() > 0)
                        <!-- Chart Card -->
                        <div class="card mb-3">
                            <div class="card-header">
                                <h5 class="card-title">Grafik Skor Alternatif</h5>
                            </div>
                            <div class="card-body">
                                <div id="chart-ranking"></div>
                            </div>
                        </div>

                        <!-- Results Table -->
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-start">
                                <div>
                                    <h5 class="card-title">Hasil Perhitungan - {{ $selectedCalculation->name }}</h5>
                                    <p class="text-muted small">
                                        Tanggal: {{ $selectedCalculation->finalized_at->format('d M Y H:i') }}
                                    </p>
                                </div>
                                <a href="{{ route('admin.mfep.pdf', $selectedCalculation->id) }}" target="_blank"
                                    class="btn btn-sm btn-success">
                                    <i class="bi bi-file-pdf"></i> Export PDF
                                </a>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover" id="table1">
                                        <thead>
                                            <tr>
                                                <th>Rank</th>
                                                <th>Alternatif</th>
                                                <th>Skor Akhir Σ(E×W)</th>
                                                <th>Status</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($results as $result)
                                                <tr class="{{ $result->is_recommended ? 'table-success' : '' }}">
                                                    <td>
                                                        <h6 class="mb-0">
                                                            <span
                                                                class="badge bg-{{ $loop->first ? 'success' : 'secondary' }}">
                                                                #{{ $result->rank }}
                                                            </span>
                                                        </h6>
                                                    </td>
                                                    <td>
                                                        <strong>{{ $result->alternative->name }}</strong>
                                                        <br>
                                                        <small class="text-muted">{{ $result->alternative->code }}</small>
                                                    </td>
                                                    <td>
                                                        <strong class="text-primary">
                                                            {{ number_format((float) $result->weighted_score, 4, ',', '.') }}
                                                        </strong>
                                                    </td>
                                                    <td>
                                                        @if ($result->is_recommended)
                                                            <span class="badge bg-success">
                                                                <i class="bi bi-check-circle"></i> Rekomendasi
                                                            </span>
                                                        @else
                                                            <span class="badge bg-info">Aktif</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <a href="{{ route('admin.mfep.show', $selectedCalculation->id) }}"
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
                            <div class="card-body text-center text-muted py-5">
                                <p>Tidak ada hasil perhitungan untuk menampilkan</p>
                            </div>
                        </div>
                    @else
                        <div class="card">
                            <div class="card-body text-center text-muted py-5">
                                <i class="bi bi-info-circle" style="font-size: 3rem; opacity: 0.3;"></i>
                                <p class="mt-3">Pilih perhitungan dari riwayat untuk melihat hasil</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </section>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                @if ($selectedCalculation && $results->count() > 0)
                    const options = {
                        series: [{
                            name: 'Skor Akhir Σ(E×W)',
                            data: [
                                @foreach ($results as $result)
                                    {{ (float) $result->weighted_score }},
                                @endforeach
                            ]
                        }],
                        chart: {
                            type: 'bar',
                            height: 350,
                            fontFamily: 'inherit',
                            toolbar: {
                                show: true
                            }
                        },
                        plotOptions: {
                            bar: {
                                horizontal: false,
                                columnWidth: '55%',
                                borderRadius: 4,
                            }
                        },
                        dataLabels: {
                            enabled: false
                        },
                        stroke: {
                            show: true,
                            width: 2,
                            colors: ['transparent']
                        },
                        colors: ['#0d6efd'],
                        xaxis: {
                            categories: [
                                @foreach ($results as $result)
                                    '{{ $result->alternative->name }}',
                                @endforeach
                            ]
                        },
                        yaxis: {
                            title: {
                                text: 'Skor Akhir Σ(E×W)'
                            }
                        },
                        fill: {
                            opacity: 1
                        },
                        tooltip: {
                            y: {
                                formatter: function(val) {
                                    return val.toFixed(4);
                                }
                            }
                        }
                    };

                    const chart = new ApexCharts(document.querySelector("#chart-ranking"), options);
                    chart.render();
                @endif
            });
        </script>
    @endpush
@endsection
