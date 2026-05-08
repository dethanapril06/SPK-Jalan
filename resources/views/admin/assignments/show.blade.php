@extends('layouts.admin')

@section('title', 'Detail Penugasan')

@section('content')
    <div class="page-heading">
        <div class="page-title mb-3">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Detail Penugasan</h3>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.assignments.index') }}">Penugasan</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Detail</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Informasi Penugasan</h4>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6 col-12">
                            <label class="form-label fw-bold">Surveyor</label>
                            <div>{{ $assignment->surveyor?->user?->name ?? '-' }}
                                ({{ $assignment->surveyor?->code ?? '-' }})</div>
                        </div>

                        <div class="col-md-6 col-12">
                            <label class="form-label fw-bold">Alternatif</label>
                            <div>{{ $assignment->alternative?->name ?? '-' }} ({{ $assignment->alternative?->code ?? '-' }})
                            </div>
                        </div>

                        <div class="col-md-6 col-12">
                            <label class="form-label fw-bold">Periode</label>
                            <div>
                                @if ($assignment->period)
                                    <strong>{{ $assignment->period->name }}</strong>
                                    <div class="small text-muted">{{ $assignment->period->year }}</div>
                                    <div class="mt-1">
                                        @if ($assignment->period->status === 'active')
                                            <span class="badge bg-light-success">Aktif</span>
                                        @elseif ($assignment->period->status === 'closed')
                                            <span class="badge bg-light-danger">Ditutup</span>
                                        @else
                                            <span class="badge bg-light-secondary">Draft</span>
                                        @endif
                                    </div>
                                @else
                                    -
                                @endif
                            </div>
                        </div>

                        <div class="col-md-4 col-12">
                            <label class="form-label fw-bold">Status</label>
                            <div>{{ strtoupper(str_replace('_', ' ', $assignment->status)) }}</div>
                        </div>

                        <div class="col-md-4 col-12">
                            <label class="form-label fw-bold">Jatuh Tempo</label>
                            <div>{{ $assignment->due_date?->format('d-m-Y') ?? '-' }}</div>
                        </div>

                        <div class="col-md-4 col-12">
                            <label class="form-label fw-bold">Ditetapkan Pada</label>
                            <div>{{ $assignment->assigned_at?->format('d-m-Y H:i') ?? '-' }}</div>
                        </div>

                        <div class="col-md-6 col-12">
                            <label class="form-label fw-bold">Ditetapkan Oleh</label>
                            <div>{{ $assignment->assignedByUser?->name ?? '-' }}</div>
                        </div>

                        <div class="col-md-6 col-12">
                            <label class="form-label fw-bold">Dimulai Pada</label>
                            <div>{{ $assignment->started_at?->format('d-m-Y H:i') ?? '-' }}</div>
                        </div>

                        <div class="col-md-6 col-12">
                            <label class="form-label fw-bold">Dikumpulkan Pada</label>
                            <div>{{ $assignment->submitted_at?->format('d-m-Y H:i') ?? '-' }}</div>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-bold">Catatan</label>
                            <div>{{ $assignment->notes ?: '-' }}</div>
                        </div>
                    </div>

                    <div class="mt-4 d-flex justify-content-end">
                        <a href="{{ route('admin.assignments.index') }}"
                            class="btn btn-light-secondary me-1 mb-1">Kembali</a>
                        <a href="{{ route('admin.assignments.edit', $assignment) }}"
                            class="btn btn-primary me-1 mb-1">Edit</a>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Penilaian Surveyor</h4>
                </div>
                <div class="card-body">
                    <div class="row g-3 mb-3">
                        <div class="col-md-4 col-12">
                            <label class="form-label fw-bold">Progress Penilaian</label>
                            <div>{{ $completedSubCriteria }}/{{ $totalSubCriteria }} sub-kriteria
                                ({{ $progressPercent }}%)</div>
                        </div>
                        <div class="col-md-8 col-12">
                            <label class="form-label fw-bold">Progress Bar</label>
                            <div class="progress" style="height: 10px;">
                                <div class="progress-bar" role="progressbar" style="width: {{ $progressPercent }}%;"
                                    aria-valuenow="{{ $progressPercent }}" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                    </div>

                    @if ($assessments->isEmpty())
                        <div class="alert alert-light-info mb-0">
                            Belum ada penilaian yang diinput oleh surveyor untuk penugasan ini.
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th style="width: 48px;">No</th>
                                        <th>Kriteria</th>
                                        <th>Sub Kriteria</th>
                                        <th>Aspek Dipilih</th>
                                        <th>Nilai</th>
                                        <th>Catatan</th>
                                        <th>Dinilai Pada</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($assessments as $index => $assessment)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $assessment->subCriteria?->criteria?->name ?? '-' }}</td>
                                            <td>
                                                {{ $assessment->subCriteria?->name ?? '-' }}
                                                @if ($assessment->subCriteria?->code)
                                                    <div class="small text-muted">{{ $assessment->subCriteria->code }}
                                                    </div>
                                                @endif
                                            </td>
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
