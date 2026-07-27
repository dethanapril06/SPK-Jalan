@extends('layouts.admin')

@section('title', 'Tambah Kriteria')

@section('content')
    <div class="page-heading">
        <div class="page-title mb-3">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Tambah Kriteria</h3>
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
                                Form Tambah Kriteria
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
        <section class="section">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Form Tambah Kriteria</h4>
                </div>
                <div class="card-content">
                    <div class="card-body">
                        <div class="alert alert-light-info color-info" role="alert">
                            Total bobot seluruh kriteria maksimal <strong>1.00</strong>.
                            <div class="mt-1 small">
                                Total Bobot Kriteria Lain: <span id="existingWeightText" class="fw-bold">{{ number_format($totalExistingWeight, 2) }}</span> | 
                                Sisa Bobot Tersedia: <span id="remainingWeightText" class="fw-bold">{{ number_format(max(0, 1.0 - $totalExistingWeight), 2) }}</span>
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

                        <form class="form form-vertical" method="POST" action="{{ route('admin.criteria.store') }}">
                            @csrf
                            <div class="form-body">
                                <div class="row">
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="code">Kode Kriteria (Otomatis)</label>
                                            <input type="text" class="form-control bg-light"
                                                id="code" name="code"
                                                value="{{ $nextCode }}" readonly>
                                            <small class="text-muted">Kode kriteria di-generate otomatis oleh sistem.</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="name">Nama Kriteria</label>
                                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                                placeholder="Masukkan nama kriteria" id="name" name="name"
                                                value="{{ old('name') }}" required>
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="weight">Bobot Kriteria</label>
                                            @php
                                                $defaultWeight = number_format(max(0, 1.0 - $totalExistingWeight), 2, '.', '');
                                            @endphp
                                            <input type="number" step="0.01" min="0" max="1"
                                                class="form-control @error('weight') is-invalid @enderror"
                                                placeholder="Contoh: 0.30" id="weight" name="weight"
                                                value="{{ old('weight', $defaultWeight) }}" required>
                                            <div id="weightFeedback" class="invalid-feedback"></div>
                                            <small id="weightHelpText" class="text-muted d-block mt-1">
                                                Estimasi Total Bobot Setelah Ditambah: <strong id="calculatedTotalText">1.00</strong>
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
                                                rows="3" placeholder="Masukkan deskripsi kriteria (opsional)">{{ old('description') }}</textarea>
                                            @error('description')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-12 d-flex justify-content-end">
                                        <a href="{{ route('admin.criteria.index') }}"
                                            class="btn btn-light-secondary me-1 mb-1">Batal</a>
                                        <button type="submit" id="btnSubmit" class="btn btn-primary me-1 mb-1">Simpan</button>
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
                const weightInput = document.getElementById('weight');
                const calculatedTotalText = document.getElementById('calculatedTotalText');
                const weightFeedback = document.getElementById('weightFeedback');
                const btnSubmit = document.getElementById('btnSubmit');

                function validateWeight() {
                    const inputVal = parseFloat(weightInput.value) || 0;
                    const totalWeight = existingWeight + inputVal;
                    
                    calculatedTotalText.textContent = totalWeight.toFixed(2);

                    if (totalWeight > 1.0001) {
                        weightInput.classList.add('is-invalid');
                        weightFeedback.textContent = `Total bobot akan menjadi ${totalWeight.toFixed(2)} (melebihi batas maksimal 1.00). Silakan kurangi bobot!`;
                        btnSubmit.disabled = true;
                    } else {
                        weightInput.classList.remove('is-invalid');
                        weightFeedback.textContent = '';
                        btnSubmit.disabled = false;
                    }
                }

                weightInput.addEventListener('input', validateWeight);
                weightInput.addEventListener('change', validateWeight);
                validateWeight();
            });
        </script>
    @endpush
@endsection
