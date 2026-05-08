@extends('layouts.admin')

@section('title', 'Detail Periode Penilaian')

@push('styles')
    <link rel="stylesheet" href="{{ asset('template/assets/extensions/sweetalert2/sweetalert2.min.css') }}">
@endpush

@section('content')
    <div class="page-heading">
        <div class="page-title mb-3">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Detail Periode Penilaian</h3>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.assessment-periods.index') }}">Periode Penilaian</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Detail Periode</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="row">
                <!-- Detail Informasi -->
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Informasi Periode Penilaian</h4>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-4 col-12">
                                    <label class="form-label fw-bold">Kode Periode</label>
                                    <div>{{ $assessmentPeriod->code }}</div>
                                </div>

                                <div class="col-md-8 col-12">
                                    <label class="form-label fw-bold">Nama Periode</label>
                                    <div>{{ $assessmentPeriod->name }}</div>
                                </div>

                                <div class="col-md-4 col-12">
                                    <label class="form-label fw-bold">Tahun</label>
                                    <div>{{ $assessmentPeriod->year }}</div>
                                </div>

                                <div class="col-md-4 col-12">
                                    <label class="form-label fw-bold">Tanggal Mulai</label>
                                    <div>{{ $assessmentPeriod->start_date?->format('d/m/Y') ?: '-' }}</div>
                                </div>

                                <div class="col-md-4 col-12">
                                    <label class="form-label fw-bold">Tanggal Selesai</label>
                                    <div>{{ $assessmentPeriod->end_date?->format('d/m/Y') ?: '-' }}</div>
                                </div>

                                <div class="col-md-4 col-12">
                                    <label class="form-label fw-bold">Status</label>
                                    <div>
                                        @if ($assessmentPeriod->status === 'draft')
                                            <span class="badge bg-light-secondary">Draft</span>
                                        @elseif ($assessmentPeriod->status === 'active')
                                            <span class="badge bg-light-success">Aktif</span>
                                        @elseif ($assessmentPeriod->status === 'closed')
                                            <span class="badge bg-light-danger">Ditutup</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="col-md-8 col-12">
                                    <label class="form-label fw-bold">Dibuat oleh</label>
                                    <div>{{ $assessmentPeriod->createdBy?->name ?: '-' }}</div>
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-bold">Deskripsi</label>
                                    <div>{{ $assessmentPeriod->description ?: '-' }}</div>
                                </div>
                            </div>

                            <div class="mt-4 d-flex justify-content-end gap-2">
                                <a href="{{ route('admin.assessment-periods.index') }}"
                                    class="btn btn-light-secondary">Kembali</a>
                                @if (!$assessmentPeriod->isClosed())
                                    <a href="{{ route('admin.assessment-periods.edit', $assessmentPeriod) }}"
                                        class="btn btn-primary">Edit</a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Ringkasan Data -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Ringkasan Data</h4>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label fw-bold d-block">Penguji yang Ditugaskan</label>
                                <div class="display-4 text-primary">
                                    {{ $assessmentPeriod->surveyor_assignments_count ?? 0 }}</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold d-block">Penilaian</label>
                                <div class="display-4 text-warning">{{ $assessmentPeriod->assessments_count ?? 0 }}</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold d-block">Kalkulasi MFEP</label>
                                <div class="display-4 text-info">{{ $assessmentPeriod->mfep_calculations_count ?? 0 }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Status Management (hanya untuk draft dan active) -->
                    @if (!$assessmentPeriod->isClosed())
                        <div class="card mt-3">
                            <div class="card-header">
                                <h4 class="card-title">Ubah Status</h4>
                            </div>
                            <div class="card-body">
                                <form id="status-form" method="POST"
                                    action="{{ route('admin.assessment-periods.update-status', $assessmentPeriod) }}">
                                    @csrf
                                    @method('PATCH')
                                    <div class="mb-3">
                                        <select name="status" class="form-select" id="status-select">
                                            <option value="draft" @selected($assessmentPeriod->status === 'draft')>Draft</option>
                                            <option value="active" @selected($assessmentPeriod->status === 'active')>Aktif</option>
                                            @if ($assessmentPeriod->status === 'active')
                                                <option value="closed">Ditutup</option>
                                            @endif
                                        </select>
                                    </div>
                                    <button type="submit" class="btn btn-sm btn-primary w-100">Perbarui Status</button>
                                </form>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </section>
    </div>

    @push('scripts')
        <script src="{{ asset('template/assets/extensions/sweetalert2/sweetalert2.min.js') }}"></script>
        <script>
            document.getElementById('status-form')?.addEventListener('submit', function(e) {
                e.preventDefault();

                const newStatus = document.getElementById('status-select').value;
                const statusLabel = {
                    'active': 'diaktifkan',
                    'closed': 'ditutup',
                    'draft': 'dikembalikan ke draft'
                };

                Swal.fire({
                    title: 'Ubah Status Periode?',
                    text: `Periode akan ${statusLabel[newStatus]}.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#435ebe',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, ubah status',
                    cancelButtonText: 'Batal'
                }).then(function(result) {
                    if (result.isConfirmed) {
                        document.getElementById('status-form').submit();
                    }
                });
            });
        </script>
    @endpush
@endsection