@extends('layouts.admin')

@section('title', 'Edit Alternatif')

@section('content')
    <div class="page-heading">
        <div class="page-title mb-3">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Edit Alternatif</h3>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.alternatives.index') }}">Alternatif</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Form Edit Alternatif</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Form Edit Alternatif Jalan</h4>
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
                            action="{{ route('admin.alternatives.update', $alternative) }}">
                            @csrf
                            @method('PUT')
                            <div class="form-body">
                                <div class="row">
                                    <div class="col-md-4 col-12">
                                        <div class="form-group">
                                            <label for="code">Kode Alternatif</label>
                                            <input type="text" class="form-control @error('code') is-invalid @enderror"
                                                placeholder="Contoh: A6" id="code" name="code"
                                                value="{{ old('code', $alternative->code) }}" maxlength="50" required>
                                            @error('code')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-8 col-12">
                                        <div class="form-group">
                                            <label for="name">Nama Alternatif</label>
                                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                                placeholder="Masukkan nama ruas jalan" id="name" name="name"
                                                value="{{ old('name', $alternative->name) }}" required>
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-8 col-12">
                                        <div class="form-group">
                                            <label for="location">Lokasi</label>
                                            <input type="text"
                                                class="form-control @error('location') is-invalid @enderror"
                                                placeholder="Contoh: Kecamatan Utara" id="location" name="location"
                                                value="{{ old('location', $alternative->location) }}">
                                            @error('location')
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
                                                value="{{ old('order', $alternative->order) }}" required>
                                            @error('order')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="form-group">
                                            <label for="description">Deskripsi</label>
                                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
                                                rows="3" placeholder="Masukkan deskripsi alternatif (opsional)">{{ old('description', $alternative->description) }}</textarea>
                                            @error('description')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-12 d-flex justify-content-end">
                                        <a href="{{ route('admin.alternatives.index') }}"
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
