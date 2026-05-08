@extends('layouts.admin')

@section('title', 'Edit Periode Penilaian')

@section('content')
    <div class="page-heading">
        <div class="page-title mb-3">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Edit Periode Penilaian</h3>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.assessment-periods.index') }}">Periode Penilaian</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Form Edit Periode</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Form Edit Periode Penilaian</h4>
                </div>
                <div class="card-content">
                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-light-danger color-danger alert-dismissible fade show" role="alert">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif

                        <form class="form form-vertical" method="POST"
                            action="{{ route('admin.assessment-periods.update', $assessmentPeriod) }}">
                            @csrf
                            @method('PUT')
                            <div class="form-body">
                                <div class="row">
                                    <div class="col-md-4 col-12">
                                        <div class="form-group">
                                            <label for="code">Kode Periode</label>
                                            <input type="text" class="form-control @error('code') is-invalid @enderror"
                                                placeholder="Contoh: P001" id="code" name="code"
                                                value="{{ old('code', $assessmentPeriod->code) }}" maxlength="50" required>
                                            @error('code')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-8 col-12">
                                        <div class="form-group">
                                            <label for="name">Nama Periode</label>
                                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                                placeholder="Contoh: Periode Penilaian Tahun 2024" id="name"
                                                name="name" value="{{ old('name', $assessmentPeriod->name) }}" required>
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-4 col-12">
                                        <div class="form-group">
                                            <label for="year">Tahun</label>
                                            <input type="number" class="form-control @error('year') is-invalid @enderror"
                                                placeholder="Contoh: 2024" id="year" name="year"
                                                value="{{ old('year', $assessmentPeriod->year) }}" min="2000"
                                                max="2100" required>
                                            @error('year')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-4 col-12">
                                        <div class="form-group">
                                            <label for="start_date">Tanggal Mulai</label>
                                            <input type="date"
                                                class="form-control @error('start_date') is-invalid @enderror"
                                                id="start_date" name="start_date"
                                                value="{{ old('start_date', $assessmentPeriod->start_date?->format('Y-m-d')) }}">
                                            @error('start_date')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-4 col-12">
                                        <div class="form-group">
                                            <label for="end_date">Tanggal Selesai</label>
                                            <input type="date"
                                                class="form-control @error('end_date') is-invalid @enderror" id="end_date"
                                                name="end_date"
                                                value="{{ old('end_date', $assessmentPeriod->end_date?->format('Y-m-d')) }}">
                                            @error('end_date')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-4 col-12">
                                        <div class="form-group">
                                            <label for="status">Status</label>
                                            <select class="form-select @error('status') is-invalid @enderror" id="status"
                                                name="status" required>
                                                <option value="draft" @selected(old('status', $assessmentPeriod->status) === 'draft')>Draft</option>
                                                <option value="active" @selected(old('status', $assessmentPeriod->status) === 'active')>Aktif</option>
                                                <option value="closed" disabled @selected($assessmentPeriod->status === 'closed')>
                                                    Ditutup (ubah via tombol status)
                                                </option>
                                            </select>
                                            @error('status')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="form-group">
                                            <label for="description">Deskripsi</label>
                                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
                                                rows="3" placeholder="Masukkan deskripsi periode penilaian (opsional)">{{ old('description', $assessmentPeriod->description) }}</textarea>
                                            @error('description')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-12 d-flex justify-content-end">
                                        <a href="{{ route('admin.assessment-periods.index') }}"
                                            class="btn btn-light-secondary me-1 mb-1">Batal</a>
                                        <button type="submit" class="btn btn-primary me-1 mb-1">Simpan Perubahan</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
