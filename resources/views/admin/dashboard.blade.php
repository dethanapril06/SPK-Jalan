@extends('layouts.admin')

@section('title', 'Dashboard Admin')

@section('content')
    @php
        $statusMeta = [
            'assigned' => ['label' => 'Assigned', 'badge' => 'secondary'],
            'in_progress' => ['label' => 'In Progress', 'badge' => 'warning'],
            'submitted' => ['label' => 'Submitted', 'badge' => 'info'],
            'reviewed' => ['label' => 'Reviewed', 'badge' => 'success'],
        ];

        $assignmentBase = max(1, $assignmentsTotal);
    @endphp

    <div class="page-heading">
        <div class="page-title">
            <div class="row align-items-center">
                <div class="col-12 col-lg-8 order-last order-lg-first">
                    <h3>Dashboard Admin</h3>
                    <p class="text-muted mb-0">Ringkasan master data dan penugasan surveyor untuk memantau proses penilaian
                        jalan.</p>
                </div>
                <div class="col-12 col-lg-4 order-first order-lg-last text-lg-end">
                    <div class="d-flex flex-wrap justify-content-lg-end gap-2">
                        <a href="{{ route('admin.assignments.index') }}" class="btn btn-primary btn-sm">Lihat Penugasan</a>
                        <a href="{{ route('admin.alternatives.index') }}"
                            class="btn btn-outline-primary btn-sm">Alternatif</a>
                    </div>
                </div>
            </div>
        </div>

        <section class="section">
            {{-- Baris 1: 3 kartu --}}
            <div class="row g-3">
                <div class="col-12 col-md-4">
                    <div class="card">
                        <div class="card-body px-4 py-4-5">
                            <div class="row">
                                <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                                    <div class="stats-icon purple mb-2">
                                        <i class="bi bi-diagram-3-fill"></i>
                                    </div>
                                </div>
                                <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                    <h6 class="text-muted font-semibold">Kriteria</h6>
                                    <h6 class="font-extrabold mb-0">{{ $criteriaCount }}</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-4">
                    <div class="card">
                        <div class="card-body px-4 py-4-5">
                            <div class="row">
                                <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                                    <div class="stats-icon blue mb-2">
                                        <i class="bi bi-list-check"></i>
                                    </div>
                                </div>
                                <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                    <h6 class="text-muted font-semibold">Sub Kriteria</h6>
                                    <h6 class="font-extrabold mb-0">{{ $subCriteriaCount }}</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-4">
                    <div class="card">
                        <div class="card-body px-4 py-4-5">
                            <div class="row">
                                <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                                    <div class="stats-icon green mb-2">
                                        <i class="bi bi-card-checklist"></i>
                                    </div>
                                </div>
                                <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                    <h6 class="text-muted font-semibold">Aspek</h6>
                                    <h6 class="font-extrabold mb-0">{{ $assessmentAspectCount }}</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Baris 2: 3 kartu --}}
            <div class="row g-3 mt-1">
                <div class="col-12 col-md-4">
                    <div class="card">
                        <div class="card-body px-4 py-4-5">
                            <div class="row">
                                <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                                    <div class="stats-icon red mb-2">
                                        <i class="bi bi-signpost-split-fill"></i>
                                    </div>
                                </div>
                                <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                    <h6 class="text-muted font-semibold">Alternatif</h6>
                                    <h6 class="font-extrabold mb-0">{{ $alternativeCount }}</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-4">
                    <div class="card">
                        <div class="card-body px-4 py-4-5">
                            <div class="row">
                                <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                                    <div class="stats-icon orange mb-2">
                                        <i class="bi bi-people-fill"></i>
                                    </div>
                                </div>
                                <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                    <h6 class="text-muted font-semibold">Surveyor Aktif</h6>
                                    <h6 class="font-extrabold mb-0">{{ $activeSurveyorCount }}</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-4">
                    <div class="card">
                        <div class="card-body px-4 py-4-5">
                            <div class="row">
                                <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                                    <div class="stats-icon yellow mb-2">
                                        <i class="bi bi-clipboard-check-fill"></i>
                                    </div>
                                </div>
                                <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                    <h6 class="text-muted font-semibold">Penugasan</h6>
                                    <h6 class="font-extrabold mb-0">{{ $assignmentsTotal }}</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-3 mt-1">
                <div class="col-12 col-xl-6">
                    <div class="card h-100">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Ringkasan Penilaian</h4>
                            <small class="text-muted">Progres assessment dari surveyor</small>
                        </div>
                        <div class="card-body">
                            <div class="mb-4">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="fw-bold">Total Assessment</span>
                                    <span class="badge bg-primary">{{ $assessmentCount }}</span>
                                </div>
                                <p class="text-muted small mb-0">Jumlah penilaian per sub-kriteria yang sudah diisi surveyor
                                </p>
                            </div>

                            <div class="mb-4">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="fw-bold">Alternatif Belum Lengkap</span>
                                    <span class="badge bg-warning">{{ $incompleteAlternatives->count() }}</span>
                                </div>
                                <p class="text-muted small mb-0">Alternatif yang masih kurang penilaian sub-kriteria</p>
                            </div>

                            @if ($incompleteAlternatives->count() > 0)
                                <div class="border-top pt-3">
                                    <p class="fw-bold small mb-2">Daftar Belum Lengkap:</p>
                                    <div class="list-group list-group-sm">
                                        @foreach ($incompleteAlternatives as $alt)
                                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                                <div>
                                                    <small class="fw-bold">{{ $alt->code }} -
                                                        {{ $alt->name }}</small>
                                                    <div class="small text-muted">
                                                        {{ $alt->assessed_sub_criteria }}/{{ $totalSubCriteriaPerAlternative }}
                                                        sub-kriteria</div>
                                                </div>
                                                <div class="progress" style="width: 100px; height: 6px;">
                                                    <div class="progress-bar" role="progressbar"
                                                        style="width: {{ round(($alt->assessed_sub_criteria / max(1, $totalSubCriteriaPerAlternative)) * 100, 1) }}%">
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @else
                                <div class="alert alert-light-success mb-0">
                                    <i class="bi bi-check-circle me-2"></i>Semua alternatif sudah dinilai lengkap!
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-12 col-xl-6">
                    <div class="card h-100">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Surveyor Terproduksi</h4>
                            <small class="text-muted">Top 5 pemberi penilaian</small>
                        </div>
                        <div class="card-body">
                            @forelse ($topSurveyors as $surveyor)
                                <div class="mb-3 pb-2 border-bottom">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <div class="fw-bold small">{{ $surveyor->name }}</div>
                                            <small class="text-muted">{{ $surveyor->code }}</small>
                                        </div>
                                        <span class="badge bg-info">{{ $surveyor->total_assessments }}</span>
                                    </div>
                                </div>
                            @empty
                                <p class="text-muted text-center small">Belum ada penilaian</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-3 mt-1">
                <div class="col-12 col-xl-8">
                    <div class="card h-100">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="card-title mb-0">Penugasan Terbaru</h4>
                                <small class="text-muted">Status assignment surveyor ke alternatif jalan</small>
                            </div>
                            <a href="{{ route('admin.assignments.index') }}" class="btn btn-sm btn-light-secondary">Semua
                                Penugasan</a>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead>
                                        <tr>
                                            <th>Surveyor</th>
                                            <th>Alternatif</th>
                                            <th>Status</th>
                                            <th>Jatuh Tempo</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($recentAssignments as $assignment)
                                            <tr>
                                                <td>
                                                    <div class="fw-bold">{{ $assignment->surveyor?->user?->name ?? '-' }}
                                                    </div>
                                                    <small
                                                        class="text-muted">{{ $assignment->surveyor?->code ?? '-' }}</small>
                                                </td>
                                                <td>
                                                    <div class="fw-bold">{{ $assignment->alternative?->name ?? '-' }}
                                                    </div>
                                                    <small
                                                        class="text-muted">{{ $assignment->alternative?->code ?? '-' }}</small>
                                                </td>
                                                <td>
                                                    @php $meta = $statusMeta[$assignment->status] ?? ['label' => strtoupper($assignment->status), 'badge' => 'dark']; @endphp
                                                    <span
                                                        class="badge bg-{{ $meta['badge'] }}">{{ $meta['label'] }}</span>
                                                </td>
                                                <td>{{ $assignment->due_date?->format('d-m-Y') ?? '-' }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center text-muted py-4">Belum ada
                                                    penugasan.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-xl-4">
                    <div class="card h-100">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Distribusi Status Penugasan</h4>
                            <small class="text-muted">Gambaran cepat progres pekerjaan admin</small>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-1">
                                    <span>Assigned</span>
                                    <span>{{ $assignedCount }}</span>
                                </div>
                                <div class="progress progress-primary" style="height: 8px;">
                                    <div class="progress-bar bg-secondary" role="progressbar"
                                        style="width: {{ round(($assignedCount / $assignmentBase) * 100, 1) }}%"></div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-1">
                                    <span>In Progress</span>
                                    <span>{{ $inProgressCount }}</span>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-warning" role="progressbar"
                                        style="width: {{ round(($inProgressCount / $assignmentBase) * 100, 1) }}%"></div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-1">
                                    <span>Submitted</span>
                                    <span>{{ $submittedCount }}</span>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-info" role="progressbar"
                                        style="width: {{ round(($submittedCount / $assignmentBase) * 100, 1) }}%"></div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <div class="d-flex justify-content-between mb-1">
                                    <span>Reviewed</span>
                                    <span>{{ $reviewedCount }}</span>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-success" role="progressbar"
                                        style="width: {{ round(($reviewedCount / $assignmentBase) * 100, 1) }}%"></div>
                                </div>
                            </div>

                            <div class="border rounded-3 p-3 bg-light">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Admin users</span>
                                    <strong>{{ $adminUserCount }}</strong>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Total surveyor</span>
                                    <strong>{{ $surveyorCount }}</strong>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted">Total penugasan</span>
                                    <strong>{{ $assignmentsTotal }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- MFEP Calculation Section --}}
            <div class="row g-3 mt-1">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="card-title mb-0">Perhitungan MFEP</h4>
                                <small class="text-muted">Analisis multi-kriteria dan ranking alternatif</small>
                            </div>
                            <div class="d-flex gap-2">
                                <a href="{{ route('admin.mfep.create') }}" class="btn btn-sm btn-primary">
                                    <i class="bi bi-calculator"></i> Hitung Skor
                                </a>
                                <a href="{{ route('admin.mfep.ranking') }}" class="btn btn-sm btn-outline-primary">
                                    Lihat Ranking
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <p class="text-muted mb-2">
                                <i class="bi bi-info-circle me-2"></i>
                                Lakukan perhitungan MFEP untuk mendapatkan skor dan ranking alternatif berdasarkan penilaian
                                yang telah dikumpulkan dari surveyor.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
