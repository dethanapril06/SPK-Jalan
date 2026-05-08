@extends('layouts.admin')

@section('title', 'Daftar Periode Penilaian')

@push('styles')
    <link rel="stylesheet" href="{{ asset('template/assets/extensions/sweetalert2/sweetalert2.min.css') }}">
@endpush

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Daftar Periode Penilaian</h3>
                    <a href="{{ route('admin.assessment-periods.create') }}" class="btn btn-sm btn-primary mb-2">
                        <i class="bi bi-plus-lg"></i> Tambah Periode
                    </a>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Daftar Periode Penilaian</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Data Periode Penilaian</h5>
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

                    <!-- Filter -->
                    <div class="mb-3 d-flex gap-2 flex-wrap">
                        <form method="GET" action="{{ route('admin.assessment-periods.index') }}"
                            class="d-flex gap-2 flex-wrap">
                            <div>
                                <input type="number" name="year" class="form-control form-control-sm"
                                    placeholder="Filter Tahun" value="{{ request('year') }}" min="2000" max="2100">
                            </div>
                            <div>
                                <select name="status" class="form-select form-select-sm">
                                    <option value="">-- Semua Status --</option>
                                    <option value="draft" @selected(request('status') === 'draft')>Draft</option>
                                    <option value="active" @selected(request('status') === 'active')>Aktif</option>
                                    <option value="closed" @selected(request('status') === 'closed')>Ditutup</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-funnel"></i> Filter
                            </button>
                            <a href="{{ route('admin.assessment-periods.index') }}"
                                class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-arrow-counterclockwise"></i> Reset
                            </a>
                        </form>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped" id="table1">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Kode</th>
                                    <th>Nama Periode</th>
                                    <th>Tahun</th>
                                    <th>Tanggal Mulai</th>
                                    <th>Tanggal Selesai</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($periods as $item)
                                    <tr>
                                        <td>{{ $periods->firstItem() + $loop->index }}</td>
                                        <td>{{ $item->code }}</td>
                                        <td>{{ $item->name }}</td>
                                        <td>{{ $item->year }}</td>
                                        <td>{{ $item->start_date?->format('d/m/Y') ?: '-' }}</td>
                                        <td>{{ $item->end_date?->format('d/m/Y') ?: '-' }}</td>
                                        <td>
                                            @if ($item->status === 'draft')
                                                <span class="badge bg-light-secondary">Draft</span>
                                            @elseif ($item->status === 'active')
                                                <span class="badge bg-light-success">Aktif</span>
                                            @elseif ($item->status === 'closed')
                                                <span class="badge bg-light-danger">Ditutup</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex gap-2 flex-wrap">
                                                <a href="{{ route('admin.assessment-periods.show', $item) }}"
                                                    class="btn btn-sm btn-outline-info" title="Detail">
                                                    <i class="bi bi-eye"></i>
                                                </a>

                                                @if (!$item->isClosed())
                                                    <a href="{{ route('admin.assessment-periods.edit', $item) }}"
                                                        class="btn btn-sm btn-outline-warning" title="Edit">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </a>
                                                @endif

                                                {{-- Tombol Aktifkan (hanya untuk draft) --}}
                                                @if ($item->isDraft())
                                                    <form method="POST"
                                                        action="{{ route('admin.assessment-periods.update-status', $item) }}"
                                                        class="status-form"
                                                        data-label="diaktifkan"
                                                        data-name="{{ $item->name }}">
                                                        @csrf
                                                        @method('PATCH')
                                                        <input type="hidden" name="status" value="active">
                                                        <button type="submit" class="btn btn-sm btn-outline-success"
                                                            title="Aktifkan">
                                                            <i class="bi bi-play-circle"></i>
                                                        </button>
                                                    </form>
                                                @endif

                                                {{-- Tombol Tutup (hanya untuk active) --}}
                                                @if ($item->isActive())
                                                    <form method="POST"
                                                        action="{{ route('admin.assessment-periods.update-status', $item) }}"
                                                        class="status-form"
                                                        data-label="ditutup"
                                                        data-name="{{ $item->name }}">
                                                        @csrf
                                                        @method('PATCH')
                                                        <input type="hidden" name="status" value="closed">
                                                        <button type="submit" class="btn btn-sm btn-outline-secondary"
                                                            title="Tutup Periode">
                                                            <i class="bi bi-stop-circle"></i>
                                                        </button>
                                                    </form>
                                                @endif

                                                @if (!$item->isClosed())
                                                    <form action="{{ route('admin.assessment-periods.destroy', $item) }}"
                                                        method="POST" class="delete-period-form">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger"
                                                            title="Hapus">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="11" class="text-center">Belum ada data periode penilaian.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-3">
                        {{ $periods->links() }}
                    </div>
                </div>
            </div>
        </section>
    </div>

    @push('scripts')
        <script src="{{ asset('template/assets/extensions/sweetalert2/sweetalert2.min.js') }}"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Konfirmasi hapus
                document.querySelectorAll('.delete-period-form').forEach(function(form) {
                    form.addEventListener('submit', function(event) {
                        event.preventDefault();

                        Swal.fire({
                            title: 'Yakin ingin menghapus periode ini?',
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

                // Konfirmasi ubah status (aktifkan / tutup)
                document.querySelectorAll('.status-form').forEach(function(form) {
                    form.addEventListener('submit', function(event) {
                        event.preventDefault();

                        const label = form.dataset.label;
                        const name  = form.dataset.name;

                        Swal.fire({
                            title: 'Ubah Status Periode?',
                            text: `Periode "${name}" akan ${label}.`,
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#435ebe',
                            cancelButtonColor: '#6c757d',
                            confirmButtonText: 'Ya, lanjutkan',
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