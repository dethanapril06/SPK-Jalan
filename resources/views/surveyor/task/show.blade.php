@extends('layouts.surveyor')

@section('title', 'Detail Tugas Penilaian')

@section('content')
    <div class="page-heading">
        <div class="page-title mb-3">
            <div class="row">
                <div class="col-12 col-md-6">
                    <h3>Detail Tugas Penilaian</h3>
                </div>
                <div class="col-12 col-md-6 text-md-end">
                    <a href="{{ route('surveyor.dashboard') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="row g-3">
                <div class="col-12 col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">{{ $assignment->alternative->name }}</h4>
                            <small class="text-muted">{{ $assignment->alternative->code }}</small>
                        </div>
                        <div class="card-body">
                            @if (session('success'))
                                <div class="alert alert-light-success alert-dismissible fade show" role="alert">
                                    {{ session('success') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            @endif

                            @if ($assignment->alternative->description)
                                <div class="mb-4">
                                    <label class="form-label fw-bold">Deskripsi Alternatif</label>
                                    <p class="mb-0">{{ $assignment->alternative->description }}</p>
                                </div>
                            @endif

                            @if ($assignment->alternative->location)
                                <div class="mb-4">
                                    <label class="form-label fw-bold">Lokasi</label>
                                    <p class="mb-0">{{ $assignment->alternative->location }}</p>
                                </div>
                            @endif

                            <div class="mb-4">
                                <label class="form-label fw-bold">Status Penilaian</label>
                                <div class="progress mb-2" style="height: 25px;">
                                    <div class="progress-bar bg-success" role="progressbar"
                                        style="width: {{ ($subCriteria->where('is_completed', true)->count() / $subCriteria->count()) * 100 }}%"
                                        aria-valuenow="{{ $subCriteria->where('is_completed', true)->count() }}"
                                        aria-valuemin="0" aria-valuemax="{{ $subCriteria->count() }}">
                                        {{ $subCriteria->where('is_completed', true)->count() }}/{{ $subCriteria->count() }}
                                        Sub-Kriteria
                                    </div>
                                </div>
                            </div>

                            <hr>

                            <h5 class="mb-3">Daftar Sub-Kriteria untuk Dinilai</h5>

                            @php
                                $groupedByCriteria = $subCriteria->groupBy('criteria_id');
                            @endphp

                            @foreach ($groupedByCriteria as $criteriaId => $items)
                                <div class="mb-4">
                                    @php
                                        $firstItem = $items->first();
                                        $completedInCriteria = $items->where('is_completed', true)->count();
                                    @endphp
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <h6 class="mb-0">{{ $firstItem->criteria->name ?? 'Kriteria' }}</h6>
                                        <small
                                            class="badge bg-light-secondary">{{ $completedInCriteria }}/{{ $items->count() }}</small>
                                    </div>

                                    <div class="list-group list-group-sm">
                                        @foreach ($items as $item)
                                            @if ($assignment->period && $assignment->period->status === 'closed')
                                                <div
                                                    class="list-group-item d-flex justify-content-between align-items-center">
                                                    <div class="flex-grow-1">
                                                        <strong>{{ $item->code ?? 'K' }}</strong> - {{ $item->name }}
                                                    </div>
                                                    <span class="badge bg-light-danger">Periode Ditutup</span>
                                                </div>
                                            @else
                                                <a href="{{ route('surveyor.assessments.edit', $assignment) }}?sub_criteria_id={{ $item->id }}"
                                                    class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                                    <div class="flex-grow-1">
                                                        <strong>{{ $item->code ?? 'K' }}</strong> - {{ $item->name }}
                                                    </div>
                                                    @if ($item->is_completed)
                                                        <span class="badge bg-success">
                                                            <i class="bi bi-check"></i> Selesai
                                                        </span>
                                                    @else
                                                        <span class="badge bg-light">
                                                            <i class="bi bi-pencil"></i> Kerjakan
                                                        </span>
                                                    @endif
                                                </a>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Informasi Tugas</h4>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label small text-muted">Status</label>
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
                                <p class="mb-0"><span class="badge bg-{{ $statusBadge }}">{{ $statusLabel }}</span>
                                </p>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small text-muted">Periode</label>
                                @if ($assignment->period)
                                    <p class="mb-0 fw-bold">{{ $assignment->period->name }}
                                        ({{ $assignment->period->year }})</p>
                                    <div class="mt-1">
                                        @if ($assignment->period->status === 'closed')
                                            <span class="badge bg-light-danger">Ditutup</span>
                                        @elseif ($assignment->period->status === 'active')
                                            <span class="badge bg-light-success">Aktif</span>
                                        @else
                                            <span class="badge bg-light-secondary">Draft</span>
                                        @endif
                                    </div>
                                @else
                                    <p class="mb-0 text-muted">-</p>
                                @endif
                            </div>

                            <div class="mb-3">
                                <label class="form-label small text-muted">Jatuh Tempo</label>
                                <p class="mb-0 fw-bold">{{ $assignment->due_date?->format('d-m-Y') ?? 'Tidak ada' }}</p>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small text-muted">Ditetapkan Pada</label>
                                <p class="mb-0 fw-bold">{{ $assignment->assigned_at?->format('d-m-Y H:i') ?? '-' }}</p>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small text-muted">Dimulai Pada</label>
                                <p class="mb-0 fw-bold">{{ $assignment->started_at?->format('d-m-Y H:i') ?? '-' }}</p>
                            </div>

                            <hr>

                            @if ($assignment->notes)
                                <div>
                                    <label class="form-label small text-muted">Catatan Admin</label>
                                    <p class="small">{{ $assignment->notes }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
