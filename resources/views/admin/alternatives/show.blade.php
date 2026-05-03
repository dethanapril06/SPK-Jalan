@extends('layouts.admin')

@section('title', 'Detail Alternatif')

@section('content')
    <div class="page-heading">
        <div class="page-title mb-3">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Detail Alternatif</h3>
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
                            <li class="breadcrumb-item active" aria-current="page">Detail Alternatif</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Informasi Alternatif Jalan</h4>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4 col-12">
                            <label class="form-label fw-bold">Kode Alternatif</label>
                            <div>{{ $alternative->code }}</div>
                        </div>

                        <div class="col-md-8 col-12">
                            <label class="form-label fw-bold">Nama Alternatif</label>
                            <div>{{ $alternative->name }}</div>
                        </div>

                        <div class="col-md-8 col-12">
                            <label class="form-label fw-bold">Lokasi</label>
                            <div>{{ $alternative->location ?: '-' }}</div>
                        </div>

                        <div class="col-md-4 col-12">
                            <label class="form-label fw-bold">Urutan</label>
                            <div>{{ $alternative->order }}</div>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-bold">Deskripsi</label>
                            <div>{{ $alternative->description ?: '-' }}</div>
                        </div>
                    </div>

                    <div class="mt-4 d-flex justify-content-end">
                        <a href="{{ route('admin.alternatives.index') }}"
                            class="btn btn-light-secondary me-1 mb-1">Kembali</a>
                        <a href="{{ route('admin.alternatives.edit', $alternative) }}"
                            class="btn btn-primary me-1 mb-1">Edit</a>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
