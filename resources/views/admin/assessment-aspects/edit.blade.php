@extends('layouts.admin')

@section('title', 'Edit Aspek Penilaian')

@section('content')
    <div class="page-heading">
        <div class="page-title mb-3">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Edit Aspek Penilaian</h3>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.assessment-aspects.index') }}">Aspek Penilaian</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">
                                Form Edit Aspek Penilaian
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Form Edit Aspek Penilaian</h4>
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
                            action="{{ route('admin.assessment-aspects.update', $assessmentAspect) }}">
                            @csrf
                            @method('PUT')
                            <div class="form-body">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label for="sub_criteria_id">Sub Kriteria</label>
                                            <select id="sub_criteria_id" name="sub_criteria_id"
                                                class="form-select @error('sub_criteria_id') is-invalid @enderror" required>
                                                <option value="">Pilih sub kriteria</option>
                                                @foreach ($subCriterias as $subCriteria)
                                                    <option value="{{ $subCriteria->id }}" @selected((int) old('sub_criteria_id', $assessmentAspect->sub_criteria_id) === $subCriteria->id)>
                                                        {{ $subCriteria->criteria?->code }} -
                                                        {{ $subCriteria->criteria?->name }} | {{ $subCriteria->code }} -
                                                        {{ $subCriteria->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('sub_criteria_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-8 col-12">
                                        <div class="form-group">
                                            <label for="name">Nama Aspek</label>
                                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                                placeholder="Contoh: >5%" id="name" name="name"
                                                value="{{ old('name', $assessmentAspect->name) }}" required>
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-4 col-12">
                                        <div class="form-group">
                                            <label for="value">Nilai</label>
                                            <input type="number" min="1"
                                                class="form-control @error('value') is-invalid @enderror"
                                                placeholder="Contoh: 4" id="value" name="value"
                                                value="{{ old('value', $assessmentAspect->value) }}" required>
                                            @error('value')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-4 col-12">
                                        <div class="form-group">
                                            <label for="order">Urutan</label>
                                            <input type="number" min="1"
                                                class="form-control @error('order') is-invalid @enderror"
                                                placeholder="Contoh: 1" id="order" name="order"
                                                value="{{ old('order', $assessmentAspect->order) }}" required>
                                            @error('order')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="form-group">
                                            <label for="description">Deskripsi</label>
                                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
                                                rows="3" placeholder="Masukkan deskripsi aspek (opsional)">{{ old('description', $assessmentAspect->description) }}</textarea>
                                            @error('description')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-12 d-flex justify-content-end">
                                        <a href="{{ route('admin.assessment-aspects.index') }}"
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
