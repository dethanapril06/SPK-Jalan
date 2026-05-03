@extends('layouts.admin')

@section('title', 'Detail Sub Kriteria')

@section('content')
    <div class="page-heading">
        <div class="page-title mb-3">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Detail Sub Kriteria</h3>
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
                                Detail Sub Kriteria
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            {{-- Card: Info Sub Kriteria & Kriteria Induk --}}
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Informasi Sub Kriteria</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 col-12 mb-3">
                            <label class="form-label fw-bold">Kode Sub Kriteria</label>
                            <div>{{ $subCriteria->code }}</div>
                        </div>
                        <div class="col-md-6 col-12 mb-3">
                            <label class="form-label fw-bold">Nama Sub Kriteria</label>
                            <div>{{ $subCriteria->name }}</div>
                        </div>
                        <div class="col-md-6 col-12 mb-3">
                            <label class="form-label fw-bold">Kode Kriteria Induk</label>
                            <div>{{ $subCriteria->criteria?->code ?? '-' }}</div>
                        </div>
                        <div class="col-md-6 col-12 mb-3">
                            <label class="form-label fw-bold">Nama Kriteria Induk</label>
                            <div>{{ $subCriteria->criteria?->name ?? '-' }}</div>
                        </div>
                        <div class="col-md-6 col-12 mb-3">
                            <label class="form-label fw-bold">Urutan</label>
                            <div>{{ $subCriteria->order ?? '-' }}</div>
                        </div>
                        <div class="col-md-6 col-12 mb-3">
                            <label class="form-label fw-bold">Deskripsi</label>
                            <div>{{ $subCriteria->description ?: '-' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Card: Daftar Aspek Penilaian --}}
            <div class="card mt-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Daftar Aspek Penilaian</h4>
                    <a href="{{ route('admin.assessment-aspects.create', ['sub_criteria_id' => $subCriteria->id]) }}"
                        class="btn btn-sm btn-primary">
                        <i class="bi bi-plus-lg"></i> Tambah Aspek
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Aspek</th>
                                    <th>Nilai</th>
                                    <th>Urutan</th>
                                    <th>Deskripsi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($subCriteria->assessmentAspects as $aspect)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $aspect->name }}</td>
                                        <td>{{ $aspect->value }}</td>
                                        <td>{{ $aspect->order ?? '-' }}</td>
                                        <td>{{ $aspect->description ?: '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">
                                            Belum ada aspek penilaian untuk sub kriteria ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="mt-3 d-flex justify-content-end">
                <a href="{{ route('admin.sub-criteria.index') }}" class="btn btn-light-secondary me-1 mb-1">Kembali</a>
                <a href="{{ route('admin.sub-criteria.edit', $subCriteria) }}" class="btn btn-primary me-1 mb-1">Edit</a>
            </div>
        </section>
    </div>
@endsection
