@extends('layouts.admin')

@section('title', 'Detail Aspek Penilaian')

@section('content')
    <div class="page-heading">
        <div class="page-title mb-3">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Detail Aspek Penilaian</h3>
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
                                Detail Aspek Penilaian
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="row">
                <div class="col-12 col-lg-6">
                    <div class="card h-100">
                        <div class="card-header">
                            <h4 class="card-title">Informasi Aspek</h4>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Nama Aspek</label>
                                <div>{{ $assessmentAspect->name }}</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Nilai</label>
                                <div>{{ $assessmentAspect->value }}</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Urutan</label>
                                <div>{{ $assessmentAspect->order ?? '-' }}</div>
                            </div>
                            <div>
                                <label class="form-label fw-bold">Deskripsi</label>
                                <div>{{ $assessmentAspect->description ?: '-' }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-6">
                    <div class="card h-100">
                        <div class="card-header">
                            <h4 class="card-title">Sub Kriteria Terkait</h4>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Kriteria</label>
                                <div>
                                    {{ $assessmentAspect->subCriteria?->criteria?->code ?? '-' }} -
                                    {{ $assessmentAspect->subCriteria?->criteria?->name ?? '-' }}
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Sub Kriteria</label>
                                <div>
                                    {{ $assessmentAspect->subCriteria?->code ?? '-' }} -
                                    {{ $assessmentAspect->subCriteria?->name ?? '-' }}
                                </div>
                            </div>
                            <div>
                                <label class="form-label fw-bold">Deskripsi Sub Kriteria</label>
                                <div>{{ $assessmentAspect->subCriteria?->description ?: '-' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-3 d-flex justify-content-end">
                <a href="{{ route('admin.assessment-aspects.index') }}"
                    class="btn btn-light-secondary me-1 mb-1">Kembali</a>
                <a href="{{ route('admin.assessment-aspects.edit', $assessmentAspect) }}"
                    class="btn btn-primary me-1 mb-1">Edit</a>
            </div>
        </section>
    </div>
@endsection
