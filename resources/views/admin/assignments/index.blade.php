@extends('layouts.admin')

@section('title', 'Penugasan Surveyor')

@push('styles')
    <link rel="stylesheet" href="{{ asset('template/assets/extensions/sweetalert2/sweetalert2.min.css') }}">
@endpush

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Penugasan Surveyor</h3>
                    <a href="{{ route('admin.assignments.create') }}" class="btn btn-sm btn-primary mb-2">
                        <i class="bi bi-plus-lg"></i> Tambah Penugasan
                    </a>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Penugasan Surveyor</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Data Penugasan</h5>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-light-success color-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-light-danger color-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="mb-3 d-flex gap-2 flex-wrap">
                        <form method="GET" action="{{ route('admin.assignments.index') }}"
                            class="d-flex gap-2 align-items-center">
                            <div>
                                <select name="period_id" class="form-select form-select-sm">
                                    <option value="">-- Semua Periode --</option>
                                    @foreach ($periods as $period)
                                        <option value="{{ $period->id }}" @selected(request('period_id') == $period->id)>
                                            {{ $period->name }} ({{ $period->year }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="btn btn-sm btn-outline-primary">Filter</button>
                            <a href="{{ route('admin.assignments.index') }}"
                                class="btn btn-sm btn-outline-secondary">Reset</a>
                        </form>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped" id="table1">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Surveyor</th>
                                    <th>Alternatif</th>
                                    <th>Periode</th>
                                    <th>Status</th>
                                    <th>Jatuh Tempo</th>
                                    <th>Ditetapkan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($assignments as $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            <div class="fw-bold">{{ $item->surveyor?->user?->name ?? '-' }}</div>
                                            <small class="text-muted">{{ $item->surveyor?->code ?? '-' }}</small>
                                        </td>
                                        <td>
                                            <div class="fw-bold">{{ $item->alternative?->name ?? '-' }}</div>
                                            <small class="text-muted">{{ $item->alternative?->code ?? '-' }}</small>
                                        </td>

                                        <td>
                                            @if ($item->period)
                                                <div class="fw-bold">{{ $item->period->name }}</div>
                                                <small class="text-muted">{{ $item->period->year }}</small>
                                                <div class="mt-1">
                                                    @if ($item->period->status === 'active')
                                                        <span class="badge bg-light-success">Aktif</span>
                                                    @elseif ($item->period->status === 'closed')
                                                        <span class="badge bg-light-danger">Ditutup</span>
                                                    @else
                                                        <span class="badge bg-light-secondary">Draft</span>
                                                    @endif
                                                </div>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                                $badge = match ($item->status) {
                                                    'assigned' => 'secondary',
                                                    'in_progress' => 'warning',
                                                    'submitted' => 'info',
                                                    'reviewed' => 'success',
                                                    default => 'dark',
                                                };
                                            @endphp
                                            <span
                                                class="badge bg-{{ $badge }}">{{ strtoupper(str_replace('_', ' ', $item->status)) }}</span>
                                        </td>
                                        <td>{{ $item->due_date?->format('d-m-Y') ?? '-' }}</td>
                                        <td>{{ $item->assigned_at?->format('d-m-Y H:i') ?? '-' }}</td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                <a href="{{ route('admin.assignments.show', $item) }}"
                                                    class="btn btn-sm btn-outline-info" title="Detail">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.assignments.edit', $item) }}"
                                                    class="btn btn-sm btn-outline-warning" title="Edit">
                                                    <i class="bi bi-pencil-square"></i>
                                                </a>
                                                <form action="{{ route('admin.assignments.destroy', $item) }}"
                                                    method="POST" class="delete-assignment-form">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="btn btn-sm btn-outline-danger btn-delete-assignment"
                                                        title="Hapus">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">Belum ada data penugasan.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </div>

    @push('scripts')
        <script src="{{ asset('template/assets/extensions/sweetalert2/sweetalert2.min.js') }}"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                document.querySelectorAll('.delete-assignment-form').forEach(function(form) {
                    form.addEventListener('submit', function(event) {
                        event.preventDefault();

                        Swal.fire({
                            title: 'Yakin ingin menghapus penugasan ini?',
                            text: 'Data yang dihapus tidak bisa dikembalikan.',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#d33',
                            cancelButtonColor: '#6c757d',
                            confirmButtonText: 'Ya, hapus',
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
