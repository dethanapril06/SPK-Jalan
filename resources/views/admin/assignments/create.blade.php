@extends('layouts.admin')

@section('title', 'Tambah Penugasan')

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .select2-container--default .select2-selection--multiple {
            border: 1px solid #dce7f1;
            border-radius: .25rem;
            min-height: 38px;
            padding: .2rem .35rem;
        }

        .select2-container--default.select2-container--focus .select2-selection--multiple {
            border-color: #435ebe;
            box-shadow: 0 0 0 .2rem rgba(67, 94, 190, .15);
        }

        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background-color: #eef2ff;
            border: 1px solid #c7d2fe;
            color: #25396f;
            padding: .15rem .5rem .15rem 1.25rem;
        }

        .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
            color: #435ebe;
            margin-left: .15rem;
        }

        .select2-container {
            width: 100% !important;
        }

        .is-invalid + .select2-container .select2-selection {
            border-color: #dc3545;
        }
    </style>
@endpush

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
                                <select name="alternative_ids[]" multiple
                                    class="form-select assignment-alternatives-select @error('alternative_ids') is-invalid @enderror @error('alternative_ids.*') is-invalid @enderror"
                                    data-placeholder="Pilih satu atau lebih alternatif">
                                    @php
                                        $selectedAlternatives = old('alternative_ids', []);
                                    @endphp
                                    @foreach ($alternatives as $alternative)
                                        <option value="{{ $alternative->id }}" @selected(in_array($alternative->id, $selectedAlternatives))>
                                            {{ $alternative->code }} - {{ $alternative->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="form-text">Cari lalu pilih satu atau lebih alternatif.</div>
                                @error('alternative_ids')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                @error('alternative_ids.*')
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

@push('scripts')
    <script src="{{ asset('template/assets/extensions/jquery/jquery.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(function() {
            $('.assignment-alternatives-select').select2({
                placeholder: $('.assignment-alternatives-select').data('placeholder'),
                allowClear: true,
                width: '100%'
            });
        });
    </script>
@endpush
