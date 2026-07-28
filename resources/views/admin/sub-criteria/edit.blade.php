@extends('layouts.admin')

@section('title', 'Edit Sub Kriteria')

@section('content')
    <div class="page-heading">
        <div class="page-title mb-3">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Edit Sub Kriteria</h3>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.sub-criteria.index') }}">Sub Kriteria</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">
                                Form Edit Sub Kriteria
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
        <section class="section">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Form Edit Sub Kriteria</h4>
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
                            action="{{ route('admin.sub-criteria.update', $subCriteria) }}">
                            @csrf
                            @method('PUT')
                            <div class="form-body">
                                <div class="row">
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="criteria_id">Kriteria Induk</label>
                                            <select id="criteria_id" name="criteria_id"
                                                class="form-select @error('criteria_id') is-invalid @enderror" required>
                                                <option value="">Pilih kriteria</option>
                                                @foreach ($criterias as $criteria)
                                                    <option value="{{ $criteria->id }}" @selected((int) old('criteria_id', $subCriteria->criteria_id) === $criteria->id)>
                                                        {{ $criteria->code }} - {{ $criteria->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('criteria_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="code">Kode Sub Kriteria</label>
                                            <input type="text" class="form-control bg-light @error('code') is-invalid @enderror"
                                                id="code" name="code"
                                                value="{{ old('code', $subCriteria->code) }}" readonly>
                                            <small class="text-muted">Kode sub kriteria tidak dapat diubah.</small>
                                            @error('code')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-12">
                                         <div class="form-group">
                                             <label for="name">Nama Sub Kriteria</label>
                                             <input type="text" class="form-control @error('name') is-invalid @enderror"
                                                 placeholder="Masukkan nama sub kriteria" id="name" name="name"
                                                 value="{{ old('name', $subCriteria->name) }}" required autocomplete="off">
                                             <div id="nameFeedback"></div>
                                             @error('name')
                                                 <div class="invalid-feedback d-block">{{ $message }}</div>
                                             @enderror
                                         </div>
                                     </div>

                                    <div class="col-12">
                                        <div class="form-group">
                                            <label for="description">Deskripsi</label>
                                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
                                                rows="3" placeholder="Masukkan deskripsi sub kriteria (opsional)">{{ old('description', $subCriteria->description) }}</textarea>
                                            @error('description')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-12 d-flex justify-content-end">
                                        <a href="{{ route('admin.sub-criteria.index') }}"
                                            class="btn btn-light-secondary me-1 mb-1">Batal</a>
                                        <button type="submit" id="btnSubmit" class="btn btn-primary me-1 mb-1">Simpan Perubahan</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const existingNamesMap = @json($existingNamesMap);
                const criteriaSelect = document.getElementById('criteria_id');
                const nameInput = document.getElementById('name');
                const nameFeedback = document.getElementById('nameFeedback');
                const btnSubmit = document.getElementById('btnSubmit');

                function validateName() {
                    const selectedCriteriaId = criteriaSelect.value;
                    const val = (nameInput.value || '').trim();
                    const typedName = val.toLowerCase();
                    const existingNames = selectedCriteriaId ? (existingNamesMap[selectedCriteriaId] || []) : [];

                    if (!val) {
                        nameInput.classList.remove('is-invalid', 'is-valid');
                        if (nameFeedback) {
                            nameFeedback.className = '';
                            nameFeedback.textContent = '';
                        }
                        btnSubmit.disabled = false;
                        return true;
                    }

                    if (selectedCriteriaId && existingNames.includes(typedName)) {
                        nameInput.classList.add('is-invalid');
                        nameInput.classList.remove('is-valid');
                        if (nameFeedback) {
                            nameFeedback.className = 'invalid-feedback d-block';
                            nameFeedback.textContent = `⚠️ Nama sub kriteria "${val}" sudah digunakan pada kriteria ini!`;
                        }
                        btnSubmit.disabled = true;
                        return false;
                    } else {
                        nameInput.classList.remove('is-invalid');
                        nameInput.classList.add('is-valid');
                        if (nameFeedback) {
                            nameFeedback.className = 'valid-feedback d-block';
                            nameFeedback.textContent = `✓ Nama sub kriteria "${val}" tersedia.`;
                        }
                        btnSubmit.disabled = false;
                        return true;
                    }
                }

                ['input', 'keyup', 'change', 'paste'].forEach(evt => {
                    nameInput.addEventListener(evt, validateName);
                });
                criteriaSelect.addEventListener('change', validateName);
                validateName();
            });
        </script>
    @endpush
@endsection
