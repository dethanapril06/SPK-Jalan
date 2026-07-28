@extends('layouts.admin')

@section('title', 'Tambah Sub Kriteria')

@section('content')
    <div class="page-heading">
        <div class="page-title mb-3">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Tambah Sub Kriteria</h3>
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
                                Form Tambah Sub Kriteria
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
        <section class="section">
            <div class="row">
                {{-- Form Tambah Sub Kriteria --}}
                <div class="col-lg-6 col-12">
                    <div class="card h-100">
                        <div class="card-header">
                            <h4 class="card-title">Form Tambah Sub Kriteria</h4>
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

                                <form class="form form-vertical" method="POST" action="{{ route('admin.sub-criteria.store') }}">
                                    @csrf
                                    <div class="form-body">
                                        <div class="row">
                                            <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                    <label for="criteria_id">Kriteria Induk</label>
                                                    <select id="criteria_id" name="criteria_id"
                                                        class="form-select @error('criteria_id') is-invalid @enderror" required>
                                                        <option value="">Pilih kriteria</option>
                                                        @foreach ($criterias as $criteria)
                                                            <option value="{{ $criteria->id }}" @selected((int) old('criteria_id') === $criteria->id)>
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
                                                    <label for="code">Kode Sub Kriteria (Otomatis)</label>
                                                    <input type="text" class="form-control bg-light @error('code') is-invalid @enderror"
                                                        placeholder="Pilih kriteria terlebih dahulu" id="code" name="code"
                                                        value="{{ old('code') }}" readonly>
                                                    <small class="text-muted">Kode sub kriteria di-generate otomatis sesuai kriteria induk (contoh: K1.1).</small>
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
                                                        value="{{ old('name') }}" required autocomplete="off">
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
                                                        rows="3" placeholder="Masukkan deskripsi sub kriteria (opsional)">{{ old('description') }}</textarea>
                                                    @error('description')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-12 d-flex justify-content-end">
                                                <a href="{{ route('admin.sub-criteria.index') }}"
                                                    class="btn btn-light-secondary me-1 mb-1">Batal</a>
                                                <button type="submit" class="btn btn-primary me-1 mb-1">Simpan</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Tabel Sub Kriteria Eksisting --}}
                <div class="col-lg-6 col-12 mt-4 mt-lg-0">
                    <div class="card h-100">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="card-title mb-0">Daftar Sub Kriteria Eksisting</h4>
                            <span class="badge bg-light-primary text-primary fw-bold">{{ $existingSubCriterias->count() }} Sub Kriteria</span>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-sm" id="table1">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Kode</th>
                                            <th>Kriteria Induk</th>
                                            <th>Nama Sub Kriteria</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($existingSubCriterias as $item)
                                            <tr class="sub-criteria-row" data-criteria-id="{{ $item->criteria_id }}">
                                                <td>{{ $loop->iteration }}</td>
                                                <td><span class="badge bg-light-info text-info fw-bold">{{ $item->code }}</span></td>
                                                <td><span class="badge bg-light-primary text-primary">{{ $item->criteria?->code }}</span> {{ $item->criteria?->name }}</td>
                                                <td>{{ $item->name }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center text-muted">Belum ada data sub kriteria.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const nextCodes = @json($nextCodes);
                const existingNamesMap = @json($existingNamesMap);
                const criteriaSelect = document.getElementById('criteria_id');
                const codeInput = document.getElementById('code');
                const nameInput = document.getElementById('name');
                const nameFeedback = document.getElementById('nameFeedback');
                const btnSubmit = document.querySelector('button[type="submit"]');
                const subCriteriaRows = document.querySelectorAll('.sub-criteria-row');

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
                        return true;
                    }

                    if (selectedCriteriaId && existingNames.includes(typedName)) {
                        nameInput.classList.add('is-invalid');
                        nameInput.classList.remove('is-valid');
                        if (nameFeedback) {
                            nameFeedback.className = 'invalid-feedback d-block';
                            nameFeedback.textContent = `⚠️ Nama sub kriteria "${val}" sudah ada pada kriteria induk ini!`;
                        }
                        return false;
                    } else {
                        nameInput.classList.remove('is-invalid');
                        nameInput.classList.add('is-valid');
                        if (nameFeedback) {
                            nameFeedback.className = 'valid-feedback d-block';
                            nameFeedback.textContent = selectedCriteriaId ? `✓ Nama sub kriteria "${val}" tersedia.` : `✓ Nama sub kriteria diisi.`;
                        }
                        return true;
                    }
                }

                function validateForm() {
                    const selectedCriteriaId = criteriaSelect.value;

                    if (selectedCriteriaId && nextCodes[selectedCriteriaId]) {
                        codeInput.value = nextCodes[selectedCriteriaId];
                    } else {
                        codeInput.value = '';
                    }

                    // Highlight rows matching selected criteria
                    subCriteriaRows.forEach(row => {
                        if (selectedCriteriaId && row.dataset.criteriaId === selectedCriteriaId) {
                            row.classList.add('table-warning');
                        } else {
                            row.classList.remove('table-warning');
                        }
                    });

                    const isNameValid = validateName();
                    if (btnSubmit) btnSubmit.disabled = !isNameValid;
                }

                ['input', 'keyup', 'change', 'paste'].forEach(evt => {
                    nameInput.addEventListener(evt, validateForm);
                });
                criteriaSelect.addEventListener('change', validateForm);

                validateForm();
            });
        </script>
    @endpush
@endsection
