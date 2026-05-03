@extends('layouts.admin')

@section('title', 'Detail User')

@push('styles')
    <link rel="stylesheet" href="{{ asset('template/assets/extensions/sweetalert2/sweetalert2.min.css') }}">
@endpush

@section('content')
    <div class="page-heading">
        <div class="page-title mb-3">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Detail User</h3>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.users.index') }}">Users</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Detail User</li>
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
                            <h4 class="card-title">Informasi User</h4>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Nama</label>
                                <div>{{ $user->name }}</div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Email</label>
                                <div>{{ $user->email }}</div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Role</label>
                                <div>
                                    @if ($user->role === 'admin')
                                        <span class="badge bg-primary">ADMIN</span>
                                    @else
                                        <span class="badge bg-success">KEPALA DINAS</span>
                                    @endif
                                </div>
                            </div>

                            <div>
                                <label class="form-label fw-bold">Tanggal Dibuat</label>
                                <div>{{ $user->created_at?->format('d M Y H:i') ?? '-' }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-6">
                    <div class="card h-100">
                        <div class="card-header">
                            <h4 class="card-title">Aksi Cepat</h4>
                        </div>
                        <div class="card-body">
                            <p class="mb-3">Gunakan aksi berikut untuk mengelola akun user ini.</p>

                            <div class="d-flex gap-2 flex-wrap">
                                <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-warning">
                                    <i class="bi bi-pencil-square"></i> Edit User
                                </a>

                                <form action="{{ route('admin.users.reset-password', $user) }}" method="POST"
                                    class="reset-user-password-form d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-key"></i> Reset Password
                                    </button>
                                </form>
                            </div>

                            <small class="text-muted d-block mt-3">
                                Reset password akan mengembalikan password user ke default: password.
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-3 d-flex justify-content-end">
                <a href="{{ route('admin.users.index') }}" class="btn btn-light-secondary me-1 mb-1">Kembali</a>
                <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-primary me-1 mb-1">Edit</a>
            </div>
        </section>
    </div>

    @push('scripts')
        <script src="{{ asset('template/assets/extensions/sweetalert2/sweetalert2.min.js') }}"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                document.querySelectorAll('.reset-user-password-form').forEach(function(form) {
                    form.addEventListener('submit', function(event) {
                        event.preventDefault();

                        Swal.fire({
                            title: 'Reset password user?',
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
