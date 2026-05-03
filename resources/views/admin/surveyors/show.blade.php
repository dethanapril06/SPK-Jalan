@extends('layouts.admin')

@section('title', 'Detail Surveyor')

@push('styles')
    <link rel="stylesheet" href="{{ asset('template/assets/extensions/sweetalert2/sweetalert2.min.css') }}">
@endpush

@section('content')
    <div class="page-heading">
        <div class="page-title mb-3">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Detail Surveyor</h3>
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
                            <li class="breadcrumb-item active" aria-current="page">Detail Surveyor</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <h4 class="card-title mb-0">Informasi Surveyor</h4>
                            <div class="d-flex gap-2 flex-wrap">
                                <a href="{{ route('admin.surveyors.edit', $surveyor) }}" class="btn btn-warning btn-sm">
                                    <i class="bi bi-pencil-square"></i> Edit Surveyor
                                </a>
                                <form action="{{ route('admin.surveyors.reset-password', $surveyor) }}" method="POST"
                                    class="reset-password-form d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-primary btn-sm">
                                        <i class="bi bi-key"></i> Reset Password
                                    </button>
                                </form>
                            </div>
                        </div>
                        <div class="card-body">
                            <small class="text-muted d-block mb-3">
                                Reset password akan mengembalikan password surveyor ke default: <strong>password</strong>.
                            </small>

                            <div class="row">
                                <div class="col-12 col-md-6 mb-3">
                                    <label class="form-label fw-bold">Kode Surveyor</label>
                                    <div>{{ $surveyor->code }}</div>
                                </div>

                                <div class="col-12 col-md-6 mb-3">
                                    <label class="form-label fw-bold">Nama</label>
                                    <div>{{ $surveyor->user?->name ?? '-' }}</div>
                                </div>

                                <div class="col-12 col-md-6 mb-3">
                                    <label class="form-label fw-bold">Email Login</label>
                                    <div>{{ $surveyor->user?->email ?? '-' }}</div>
                                </div>

                                <div class="col-12 col-md-6 mb-3">
                                    <label class="form-label fw-bold">Nomor Telepon</label>
                                    <div>{{ $surveyor->phone ?: '-' }}</div>
                                </div>

                                <div class="col-12 col-md-6 mb-3">
                                    <label class="form-label fw-bold">Status</label>
                                    <div>
                                        @if ($surveyor->is_active)
                                            <span class="badge bg-success">Aktif</span>
                                        @else
                                            <span class="badge bg-secondary">Nonaktif</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end">
                <a href="{{ route('admin.surveyors.index') }}" class="btn btn-light-secondary me-1 mb-1">Kembali</a>
            </div>
        </section>
    </div>

    @push('scripts')
        <script src="{{ asset('template/assets/extensions/sweetalert2/sweetalert2.min.js') }}"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                document.querySelectorAll('.reset-password-form').forEach(function(form) {
                    form.addEventListener('submit', function(event) {
                        event.preventDefault();

                        Swal.fire({
                            title: 'Reset password surveyor?',
                            text: 'Password akan direset ke default: password',
                            icon: 'question',
                            showCancelButton: true,
                            confirmButtonColor: '#435ebe',
                            cancelButtonColor: '#6c757d',
                            confirmButtonText: 'Ya, reset',
                            cancelButtonText: 'Batal'
                        }).then(function(result) {
                            if (result.isConfirmed) {
                                form.submit();
                            }
                        });
                    });
                });
            });
        </script>
    @endpush
@endsection