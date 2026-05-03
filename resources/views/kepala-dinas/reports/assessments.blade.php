@extends('layouts.kepala-dinas')

@section('title', 'Report Penilaian')

@section('content')
    <div class="page-heading">
        <div class="page-title mb-3">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Report Penilaian</h3>
                    <p class="text-muted mb-0">Semua penilaian surveyor terhadap seluruh alternatif.</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('kepala-dinas.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Report Penilaian</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="row g-3 mb-3">
                <div class="col-12 col-md-4">
                    <div class="card mb-0">
                        <div class="card-body">
                            <p class="text-muted mb-1">Total Penilaian</p>
                            <h4 class="mb-0">{{ $summary['total_assessments'] }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="card mb-0">
                        <div class="card-body">
                            <p class="text-muted mb-1">Surveyor Terlibat</p>
                            <h4 class="mb-0">{{ $summary['total_surveyors'] }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="card mb-0">
                        <div class="card-body">
                            <p class="text-muted mb-1">Alternatif Dinilai</p>
                            <h4 class="mb-0">{{ $summary['total_alternatives'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Filter Report</h4>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('kepala-dinas.reports.assessments') }}" class="row g-3">
                        <div class="col-12 col-md-3">
                            <label for="surveyor_id" class="form-label">Surveyor</label>
                            <select name="surveyor_id" id="surveyor_id" class="form-select">
                                <option value="">Semua Surveyor</option>
                                @foreach ($surveyors as $surveyor)
                                    <option value="{{ $surveyor->id }}" @selected((string) $filters['surveyor_id'] === (string) $surveyor->id)>
                                        {{ $surveyor->code }} - {{ $surveyor->user?->name ?? '-' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 col-md-3">
                            <label for="alternative_id" class="form-label">Alternatif</label>
                            <select name="alternative_id" id="alternative_id" class="form-select">
                                <option value="">Semua Alternatif</option>
                                @foreach ($alternatives as $alternative)
                                    <option value="{{ $alternative->id }}" @selected((string) $filters['alternative_id'] === (string) $alternative->id)>
                                        {{ $alternative->code }} - {{ $alternative->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 col-md-2">
                            <label for="date_from" class="form-label">Dari Tanggal</label>
                            <input type="date" name="date_from" id="date_from" class="form-control"
                                value="{{ $filters['date_from'] }}">
                        </div>

                        <div class="col-12 col-md-2">
                            <label for="date_to" class="form-label">Sampai Tanggal</label>
                            <input type="date" name="date_to" id="date_to" class="form-control"
                                value="{{ $filters['date_to'] }}">
                        </div>

                        <div class="col-12 col-md-2 d-flex align-items-end gap-2">
                            <button type="submit" class="btn btn-primary w-100">Terapkan</button>
                            <a href="{{ route('kepala-dinas.reports.assessments') }}"
                                class="btn btn-light-secondary w-100">Reset</a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <h4 class="card-title mb-0">Data Penilaian</h4>
                        <div class="d-flex gap-2">
                            <a href="{{ route('kepala-dinas.reports.assessments.excel', request()->query()) }}"
                                class="btn btn-success btn-sm">
                                <i class="bi bi-file-earmark-excel-fill"></i> Report Excel
                            </a>
                            <a href="{{ route('kepala-dinas.reports.assessments.pdf', request()->query()) }}"
                                class="btn btn-danger btn-sm">
                                <i class="bi bi-file-earmark-pdf-fill"></i> Report PDF
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if ($assessments->isEmpty())
                        <div class="alert alert-light-info mb-0">Tidak ada data penilaian untuk filter yang dipilih.</div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle" id="table1">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Surveyor</th>
                                        <th>Alternatif</th>
                                        <th>Kriteria</th>
                                        <th>Sub Kriteria</th>
                                        <th>Aspek</th>
                                        <th>Nilai</th>
                                        <th>Catatan</th>
                                        <th>Dinilai Pada</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($assessments as $assessment)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                {{ $assessment->surveyor?->code ?? '-' }}
                                                <div class="small text-muted">
                                                    {{ $assessment->surveyor?->user?->name ?? '-' }}</div>
                                            </td>
                                            <td>
                                                {{ $assessment->alternative?->code ?? '-' }}
                                                <div class="small text-muted">{{ $assessment->alternative?->name ?? '-' }}
                                                </div>
                                            </td>
                                            <td>{{ $assessment->subCriteria?->criteria?->name ?? '-' }}</td>
                                            <td>{{ $assessment->subCriteria?->name ?? '-' }}</td>
                                            <td>{{ $assessment->assessmentAspect?->name ?? '-' }}</td>
                                            <td>{{ $assessment->assessmentAspect?->value ?? '-' }}</td>
                                            <td>{{ $assessment->notes ?: '-' }}</td>
                                            <td>{{ $assessment->assessed_at?->format('d-m-Y H:i') ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </section>
    </div>
@endsection
