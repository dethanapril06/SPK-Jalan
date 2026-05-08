@extends('layouts.admin')

@section('title', 'Hitung Skor MFEP')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Hitung Skor MFEP Baru</h3>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.mfep.ranking') }}">Ranking MFEP</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">
                                Hitung Skor Baru
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="row">
                <div class="col-lg-8 mx-auto">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Form Perhitungan MFEP</h5>
                            <p class="text-muted small">
                                Sistem akan menghitung skor untuk semua alternatif berdasarkan data penilaian yang sudah ada
                            </p>
                        </div>
                        <div class="card-body">
                            @if ($errors->any())
                                <div class="alert alert-light-danger color-danger alert-dismissible fade show"
                                    role="alert">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"
                                        aria-label="Close"></button>
                                </div>
                            @endif

                            <form action="{{ route('admin.mfep.store') }}" method="POST">
                                @csrf

                                @if (isset($activePeriod) && $activePeriod)
                                    <div class="alert alert-light-info mb-4">
                                        <h6 class="mb-1"><i class="bi bi-calendar-event"></i> Periode Aktif</h6>
                                        <div class="small">
                                            <strong>{{ $activePeriod->name }}</strong> ({{ $activePeriod->year }})
                                            <span class="ms-2 badge bg-light-success">Aktif</span>
                                        </div>
                                        <div class="small text-muted mt-1">
                                            {{ $activePeriod->start_date?->format('d M Y') ?? '-' }} —
                                            {{ $activePeriod->end_date?->format('d M Y') ?? '-' }}</div>
                                    </div>
                                @else
                                    <div class="alert alert-light-danger mb-4">
                                        <i class="bi bi-exclamation-triangle"></i>
                                        Tidak ada periode aktif. Aktifkan periode terlebih dahulu sebelum melakukan
                                        perhitungan.
                                    </div>
                                @endif

                                <div class="mb-4">
                                    <label for="name" class="form-label">
                                        <span class="text-danger">*</span> Nama Perhitungan
                                    </label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                        id="name" name="name"
                                        placeholder="Contoh: Perhitungan MFEP Bulan April 2026"
                                        value="{{ old('name', 'MFEP Calculation - ' . now()->format('d M Y')) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-4">
                                    <label for="description" class="form-label">Deskripsi (Opsional)</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
                                        rows="4" placeholder="Masukkan catatan atau keterangan perhitungan...">{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="alert alert-light-info color-info mb-4" role="alert">
                                    <h6 class="mb-2">
                                        <i class="bi bi-info-circle"></i> Informasi Perhitungan
                                    </h6>
                                    <ul class="mb-0 small">
                                        <li>Sistem akan menggunakan semua data penilaian yang tersedia</li>
                                        <li>Untuk setiap alternatif, skor dihitung dengan merata-ratakan nilai sub-kriteria
                                        </li>
                                        <li>Skor akhir dihitung dengan menerapkan bobot kriteria</li>
                                        <li>Alternatif akan di-ranking berdasarkan skor akhir tertinggi</li>
                                    </ul>
                                </div>

                                @if ($lastCalculation)
                                    <div class="alert alert-light-secondary color-secondary mb-4" role="alert">
                                        <h6 class="mb-2">
                                            <i class="bi bi-clock-history"></i> Perhitungan Terakhir
                                        </h6>
                                        <p class="mb-0 small">
                                            <strong>{{ $lastCalculation->name }}</strong><br>
                                            {{ $lastCalculation->finalized_at?->format('d M Y H:i') ?? 'Belum selesai' }}
                                        </p>
                                    </div>
                                @endif

                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary"
                                        @if (!isset($activePeriod) || !$activePeriod) disabled @endif>
                                        <i class="bi bi-calculator"></i> Mulai Perhitungan
                                    </button>
                                    <a href="{{ route('admin.mfep.ranking') }}" class="btn btn-secondary">
                                        <i class="bi bi-arrow-left"></i> Kembali
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
