@extends('layouts.surveyor')

@section('title', 'Dashboard Surveyor')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12">
                    <h3>Dashboard Surveyor</h3>
                    <p class="text-muted mb-0">Kelola penilaian jalan yang sudah ditugaskan untuk Anda</p>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="row g-3 mb-3">
                <div class="col-12 col-md-3">
                    <div class="card">
                        <div class="card-body px-4 py-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <p class="text-muted small">Total Tugas</p>
                                    <h4 class="mb-0">{{ $totalAssignments }}</h4>
                                </div>
                                <i class="bi bi-clipboard-check-fill" style="font-size: 2rem; color: #ccc;"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-3">
                    <div class="card">
                        <div class="card-body px-4 py-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <p class="text-muted small">Belum Dimulai</p>
                                    <h4 class="mb-0">{{ $statusCounts->get('assigned', 0) }}</h4>
                                </div>
                                <i class="bi bi-circle" style="font-size: 2rem; color: #6c757d;"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-3">
                    <div class="card">
                        <div class="card-body px-4 py-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <p class="text-muted small">Sedang Berlangsung</p>
                                    <h4 class="mb-0">{{ $statusCounts->get('in_progress', 0) }}</h4>
                                </div>
                                <i class="bi bi-arrow-repeat" style="font-size: 2rem; color: #ffc107;"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-3">
                    <div class="card">
                        <div class="card-body px-4 py-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <p class="text-muted small">Selesai</p>
                                    <h4 class="mb-0">{{ $statusCounts->get('submitted', 0) }}</h4>
                                </div>
                                <i class="bi bi-check-circle-fill" style="font-size: 2rem; color: #28a745;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Daftar Tugas Penilaian</h4>
                </div>
                <div class="card-body">
                    @if ($assignments->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th>Alternatif (Jalan)</th>
                                        <th>Status</th>
                                        <th>Progress</th>
                                        <th>Jatuh Tempo</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($assignments as $assignment)
                                        <tr>
                                            <td>
                                                <div class="fw-bold">{{ $assignment->alternative->name }}</div>
                                                <small class="text-muted">{{ $assignment->alternative->code }}</small>
                                            </td>
                                            <td>
                                                @php
                                                    $statusBadge = match ($assignment->status) {
                                                        'assigned' => 'secondary',
                                                        'in_progress' => 'warning',
                                                        'submitted' => 'info',
                                                        'reviewed' => 'success',
                                                        default => 'dark',
                                                    };
                                                    $statusLabel = ucfirst(str_replace('_', ' ', $assignment->status));
                                                @endphp
                                                <span class="badge bg-{{ $statusBadge }}">{{ $statusLabel }}</span>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="progress" style="width: 100px; height: 6px;">
                                                        <div class="progress-bar" role="progressbar"
                                                            style="width: {{ $assignment->progress_percent }}%"></div>
                                                    </div>
                                                    <small>{{ $assignment->progress_percent }}%</small>
                                                </div>
                                            </td>
                                            <td>{{ $assignment->due_date?->format('d-m-Y') ?? '-' }}</td>
                                            <td>
                                                <a href="{{ route('surveyor.task.show', $assignment) }}"
                                                    class="btn btn-sm btn-primary">
                                                    <i class="bi bi-arrow-right"></i> Mulai
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-light-info alert-dismissible fade show" role="alert">
                            <i class="bi bi-info-circle me-2"></i>
                            <strong>Belum ada tugas</strong> - Admin akan menugaskan alternatif (jalan) untuk Anda
                            setelah Anda login.
                        </div>
                    @endif
                </div>
            </div>
        </section>
    </div>
@endsection
