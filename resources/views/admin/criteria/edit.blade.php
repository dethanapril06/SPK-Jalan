@extends('layouts.admin')

@section('title', 'Edit Kriteria')

@section('content')
    <div class="page-heading">
        <div class="page-title mb-3">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Edit Kriteria</h3>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.criteria.index') }}">Kriteria</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">
                                Form Edit Kriteria
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
        <section class="section">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Form Edit Kriteria</h4>
                </div>
                <div class="card-content">
                    <div class="card-body">
                        <div class="alert alert-light-info color-info" role="alert">
                            Total bobot seluruh kriteria maksimal <strong>1.00</strong>.
                            <div class="mt-1 small">
                                Total Bobot Kriteria Lain: <span id="existingWeightText" class="fw-bold">{{ number_format($totalExistingWeight, 2) }}</span> | 
                                Batas Maksimal Bobot Kriteria Ini: <span id="remainingWeightText" class="fw-bold">{{ number_format(max(0, 1.0 - $totalExistingWeight), 2) }}</span>
                            </div>
                        </div>

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
                            action="{{ route('admin.criteria.update', $criteria) }}">
                            @csrf
                            @method('PUT')
                            <div class="form-body">
                                <div class="row">
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="code">Kode Kriteria</label>
                                            <input type="text" class="form-control bg-light" id="code" name="code"
                                                value="{{ $criteria->code }}" readonly>
                                            <small class="text-muted">Kode kriteria tidak dapat diubah.</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12">
                                         <div class="form-group">
                                             <label for="name">Nama Kriteria</label>
                                             <input type="text" class="form-control @error('name') is-invalid @enderror"
                                                 placeholder="Masukkan nama kriteria" id="name" name="name"
                                                 value="{{ old('name', $criteria->name) }}" required autocomplete="off">
                                             <div id="nameFeedback"></div>
                                             @error('name')
                                                 <div class="invalid-feedback d-block">{{ $message }}</div>
                                             @enderror
                                         </div>
                                     </div>
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="weight">Bobot Kriteria</label>
                                            <input type="number" step="0.01" min="0" max="1"
                                                class="form-control @error('weight') is-invalid @enderror"
                                                placeholder="Contoh: 0.30" id="weight" name="weight"
                                                value="{{ old('weight', $criteria->weight) }}" required>
                                            <div id="weightFeedback" class="invalid-feedback"></div>
                                            <small id="weightHelpText" class="text-muted d-block mt-1">
                                                Estimasi Total Bobot Setelah Diubah: <strong id="calculatedTotalText">1.00</strong>
                                            </small>
                                            @error('weight')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label for="description">Deskripsi</label>
                                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
                                                rows="3" placeholder="Masukkan deskripsi kriteria (opsional)">{{ old('description', $criteria->description) }}</textarea>
                                            @error('description')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-12 d-flex justify-content-end">
                                        <a href="{{ route('admin.criteria.index') }}"
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
                const existingWeight = {{ $totalExistingWeight }};
                const existingNames = @json($existingNames);
                const weightInput = document.getElementById('weight');
                const nameInput = document.getElementById('name');
                const calculatedTotalText = document.getElementById('calculatedTotalText');
                const weightFeedback = document.getElementById('weightFeedback');
                const nameFeedback = document.getElementById('nameFeedback');
                const btnSubmit = document.getElementById('btnSubmit');

                function validateName() {
                    const val = (nameInput.value || '').trim();
                    const typedName = val.toLowerCase();

                    if (!val) {
                        nameInput.classList.remove('is-invalid', 'is-valid');
                        if (nameFeedback) {
                            nameFeedback.className = '';
                            nameFeedback.textContent = '';
                        }
                        return true;
                    }

                    if (existingNames.includes(typedName)) {
                        nameInput.classList.add('is-invalid');
                        nameInput.classList.remove('is-valid');
                        if (nameFeedback) {
                            nameFeedback.className = 'invalid-feedback d-block';
                            nameFeedback.textContent = `⚠️ Nama kriteria "${val}" sudah digunakan kriteria lain!`;
                        }
                        return false;
                    } else {
                        nameInput.classList.remove('is-invalid');
                        nameInput.classList.add('is-valid');
                        if (nameFeedback) {
                            nameFeedback.className = 'valid-feedback d-block';
                            nameFeedback.textContent = `✓ Nama kriteria "${val}" tersedia.`;
                        }
                        return true;
                    }
                }

                function validateForm() {
                    const isNameValid = validateName();

                    let isWeightValid = true;
                    const inputVal = parseFloat(weightInput.value) || 0;
                    const totalWeight = existingWeight + inputVal;
                    calculatedTotalText.textContent = totalWeight.toFixed(2);

                    if (totalWeight > 1.0001) {
                        weightInput.classList.add('is-invalid');
                        weightFeedback.textContent = `Total bobot akan menjadi ${totalWeight.toFixed(2)} (melebihi batas maksimal 1.00). Silakan kurangi bobot!`;
                        isWeightValid = false;
                    } else {
                        weightInput.classList.remove('is-invalid');
                        weightFeedback.textContent = '';
                    }

                    btnSubmit.disabled = !(isNameValid && isWeightValid);
                }

                ['input', 'keyup', 'change', 'paste'].forEach(evt => {
                    nameInput.addEventListener(evt, validateForm);
                    weightInput.addEventListener(evt, validateForm);
                });

                validateForm();
            });
        </script>
    @endpush
@endsection
