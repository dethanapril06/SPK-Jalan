@extends('layouts.surveyor')

@section('title', 'Input Penilaian - ' . $subCriteria->name)

@section('content')
    <div class="page-heading">
        <div class="page-title mb-3">
            <div class="row">
                <div class="col-12 col-md-8">
                    <h3>Input Penilaian</h3>
                    <p class="text-muted small mb-0">{{ $assignment->alternative->name }} - {{ $subCriteria->name }}</p>
                </div>
                <div class="col-12 col-md-4 text-md-end">
                    <a href="{{ route('surveyor.task.show', $assignment) }}" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="row g-3">
                <div class="col-12 col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">{{ $subCriteria->code ?? 'SC' }} - {{ $subCriteria->name }}</h4>
                        </div>
                        <div class="card-body">
                            @if (session('error'))
                                <div class="alert alert-light-danger alert-dismissible fade show" role="alert">
                                    {{ session('error') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            @endif

                            @if ($subCriteria->description)
                                <div class="mb-4">
                                    <label class="form-label fw-bold">Deskripsi Sub-Kriteria</label>
                                    <p class="mb-0">{{ $subCriteria->description }}</p>
                                </div>
                                <hr>
                            @endif

                            <form action="{{ route('surveyor.assessments.update', $assignment) }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                @method('PUT')

                                <input type="hidden" name="sub_criteria_id" value="{{ $subCriteria->id }}">

                                <div class="mb-4">
                                    <label class="form-label fw-bold">Pilih Nilai Penilaian</label>
                                    <div class="form-check">
                                        @foreach ($aspects as $aspect)
                                            <div class="mb-3 p-3 border rounded">
                                                <div class="form-check">
                                                    <input
                                                        class="form-check-input @error('assessment_aspect_id') is-invalid @enderror"
                                                        type="radio" name="assessment_aspect_id"
                                                        id="aspect_{{ $aspect->id }}" value="{{ $aspect->id }}"
                                                        @checked(optional($assessment)->assessment_aspect_id === $aspect->id)>
                                                    <label class="form-check-label cursor-pointer"
                                                        for="aspect_{{ $aspect->id }}">
                                                        <strong>{{ $aspect->name }}</strong>
                                                        <span class="badge bg-light-primary">{{ $aspect->value }}</span>
                                                    </label>
                                                </div>
                                                @if ($aspect->description)
                                                    <small
                                                        class="text-muted ms-4 d-block mt-1">{{ $aspect->description }}</small>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                    @error('assessment_aspect_id')
                                        <div class="text-danger small mt-2">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-4">
                                    <label class="form-label">Catatan (Opsional)</label>
                                    <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" rows="3"
                                        placeholder="Catatan atau alasan memilih nilai ini">{{ old('notes', $assessment?->notes) }}</textarea>
                                    @error('notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <hr>

                                <!-- Photo Upload Component -->
                                @include('components.assessment-photo-upload', [
                                    'fieldId' => 'assessment_photo',
                                    'assessment' => $assessment,
                                ])

                                <hr>

                                <div class="d-flex gap-2 justify-content-end">
                                    <a href="{{ route('surveyor.task.show', $assignment) }}"
                                        class="btn btn-light-secondary">
                                        Batal
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-check-lg"></i> Simpan Penilaian
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Panduan</h4>
                        </div>
                        <div class="card-body">
                            <p class="small mb-3">
                                <strong>Tujuan:</strong> Pilih satu nilai penilaian yang paling sesuai untuk sub-kriteria
                                ini berdasarkan kondisi alternatif yang Anda amati.
                            </p>

                            <div class="mb-3">
                                <strong class="small">Skala Penilaian:</strong>
                                <div class="small text-muted">
                                    @foreach ($aspects as $aspect)
                                        <div class="mb-1">
                                            <strong>{{ $aspect->value }}</strong> = {{ $aspect->name }}
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="alert alert-light-info small">
                                <i class="bi bi-info-circle me-1"></i>
                                Setiap penilaian hanya boleh dipilih satu. Jika ingin mengubah, pilih opsi yang berbeda dan
                                simpan ulang.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
