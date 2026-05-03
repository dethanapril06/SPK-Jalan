@extends('layouts.admin')

@section('title', 'Tambah Surveyor')

@section('content')
    <div class="page-heading">
        <div class="page-title mb-3">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Tambah Surveyor</h3>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.surveyors.index') }}">Surveyor</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Form Tambah Surveyor</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Form Tambah Surveyor</h4>
                </div>
                <div class="card-content">
                    <div class="card-body">
                        <div class="alert alert-light-info color-info" role="alert">
                            Akun login surveyor akan otomatis dibuat dengan password default: <strong>password</strong>
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

                        <form class="form form-vertical" method="POST" action="{{ route('admin.surveyors.store') }}">
                            @csrf
                            <div class="form-body">
                                <div class="row">
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="code">Kode Surveyor</label>
                                            <input type="text" class="form-control @error('code') is-invalid @enderror"
                                                placeholder="Contoh: S4" id="code" name="code"
                                                value="{{ old('code') }}" maxlength="50" required>
                                            @error('code')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="name">Nama Surveyor</label>
                                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                                placeholder="Masukkan nama surveyor" id="name" name="name"
                                                value="{{ old('name') }}" required>
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="email">Email Login</label>
                                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                                placeholder="contoh@email.com" id="email" name="email"
                                                value="{{ old('email') }}" required>
                                            @error('email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="phone">Nomor Telepon</label>
                                            <input type="text" class="form-control @error('phone') is-invalid @enderror"
                                                placeholder="08xxxxxxxxxx" id="phone" name="phone"
                                                value="{{ old('phone') }}" maxlength="30">
                                            @error('phone')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" role="switch" id="is_active"
                                                name="is_active" value="1" @checked(old('is_active', 1))>
                                            <label class="form-check-label" for="is_active">Status Aktif</label>
                                        </div>
                                    </div>

                                    <div class="col-12 d-flex justify-content-end mt-3">
                                        <a href="{{ route('admin.surveyors.index') }}"
                                            class="btn btn-light-secondary me-1 mb-1">Batal</a>
                                        <button type="submit" class="btn btn-primary me-1 mb-1">Simpan</button>
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
