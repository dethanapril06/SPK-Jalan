@extends('layouts.admin')

@section('title', 'Edit Penugasan')

@section('content')
    <div class="page-heading">
        <div class="page-title mb-3">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Edit Penugasan</h3>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.assignments.index') }}">Penugasan</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Edit</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.assignments.update', $assignment) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Surveyor</label>
                                <select name="surveyor_id" class="form-select @error('surveyor_id') is-invalid @enderror">
                                    <option value="">Pilih surveyor</option>
                                    @foreach ($surveyors as $surveyor)
                                        <option value="{{ $surveyor->id }}" @selected(old('surveyor_id', $assignment->surveyor_id) == $surveyor->id)>
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
                                        <option value="{{ $alternative->id }}" @selected(old('alternative_id', $assignment->alternative_id) == $alternative->id)>
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
                                        <option value="{{ $value }}" @selected(old('status', $assignment->status) === $value)>
                                            {{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Jatuh Tempo</label>
                                <input type="date" name="due_date"
                                    value="{{ old('due_date', optional($assignment->due_date)->format('Y-m-d')) }}"
                                    class="form-control @error('due_date') is-invalid @enderror">
                                @error('due_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Catatan</label>
                                <input type="text" name="notes" value="{{ old('notes', $assignment->notes) }}"
                                    class="form-control @error('notes') is-invalid @enderror" placeholder="Opsional">
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-4 d-flex justify-content-end">
                            <a href="{{ route('admin.assignments.index') }}"
                                class="btn btn-light-secondary me-1 mb-1">Batal</a>
                            <button type="submit" class="btn btn-primary me-1 mb-1">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
@endsection
