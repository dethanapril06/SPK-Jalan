@extends('layouts.admin')

@section('title', 'Tambah Penugasan')

@section('content')
    <div class="page-heading">
        <div class="page-title mb-3">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Tambah Penugasan</h3>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.assignments.index') }}">Penugasan</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Tambah</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="card">
                <div class="card-body">
                    @if (isset($activePeriod) && $activePeriod)
                        <div class="alert alert-light-info mb-3">
                            <h6 class="mb-1"><i class="bi bi-calendar-event"></i> Periode Aktif</h6>
                            <div class="small">
                                <strong>{{ $activePeriod->name }}</strong> ({{ $activePeriod->year }})
                                @if ($activePeriod->status === 'active')
                                    <span class="badge bg-light-success ms-2">Aktif</span>
                                @elseif ($activePeriod->status === 'closed')
                                    <span class="badge bg-light-danger ms-2">Ditutup</span>
                                @endif
                            </div>
                            @if ($activePeriod->start_date || $activePeriod->end_date)
                                <div class="mt-1 small text-muted">{{ $activePeriod->start_date?->format('d M Y') ?? '-' }}
                                    — {{ $activePeriod->end_date?->format('d M Y') ?? '-' }}</div>
                            @endif
                        </div>
                    @else
                        <div class="alert alert-light-warning mb-3">Belum ada periode aktif.</div>
                    @endif

                    <form action="{{ route('admin.assignments.store') }}" method="POST">
                        @csrf

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Surveyor</label>
                                <select name="surveyor_id" class="form-select @error('surveyor_id') is-invalid @enderror">
                                    <option value="">Pilih surveyor</option>
                                    @foreach ($surveyors as $surveyor)
                                        <option value="{{ $surveyor->id }}" @selected(old('surveyor_id') == $surveyor->id)>
                                            {{ $surveyor->code }} - {{ $surveyor->user?->name ?? '-' }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('surveyor_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Alternatif</label>
                                <select name="alternative_id"
                                    class="form-select @error('alternative_id') is-invalid @enderror">
                                    <option value="">Pilih alternatif</option>
                                    @foreach ($alternatives as $alternative)
                                        <option value="{{ $alternative->id }}" @selected(old('alternative_id') == $alternative->id)>
                                            {{ $alternative->code }} - {{ $alternative->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('alternative_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select @error('status') is-invalid @enderror">
                                    @php
                                        $statuses = [
                                            'assigned' => 'Assigned',
                                            'in_progress' => 'In Progress',
                                            'submitted' => 'Submitted',
                                            'reviewed' => 'Reviewed',
                                        ];
                                    @endphp
                                    @foreach ($statuses as $value => $label)
                                        <option value="{{ $value }}" @selected(old('status', 'assigned') === $value)>
                                            {{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Jatuh Tempo</label>
                                <input type="date" name="due_date" value="{{ old('due_date') }}"
                                    class="form-control @error('due_date') is-invalid @enderror">
                                @error('due_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Catatan</label>
                                <input type="text" name="notes" value="{{ old('notes') }}"
                                    class="form-control @error('notes') is-invalid @enderror" placeholder="Opsional">
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-4 d-flex justify-content-end">
                            <a href="{{ route('admin.assignments.index') }}"
                                class="btn btn-light-secondary me-1 mb-1">Batal</a>
                            <button type="submit" class="btn btn-primary me-1 mb-1">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
@endsection
