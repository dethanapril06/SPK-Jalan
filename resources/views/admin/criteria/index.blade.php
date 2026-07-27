@extends('layouts.admin')

@section('title', 'Daftar Kriteria')

@push('styles')
    <link rel="stylesheet" href="{{ asset('template/assets/extensions/sweetalert2/sweetalert2.min.css') }}">
@endpush

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Daftar Kriteria</h3>
                    <a href="{{ route('admin.criteria.create') }}" class="btn btn-sm btn-primary mb-2">
                        <i class="bi bi-plus-lg"></i> Tambah Kriteria
                    </a>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">
                                Daftar Kriteria
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
        <section class="section">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h5 class="card-title mb-0">Data Kriteria</h5>
                    @php
                        $totalCurrentWeight = (float) $criterias->sum('weight');
                    @endphp
                    <div id="totalWeightStatus" class="alert alert-light-info color-info py-2 px-3 mb-0 fs-6">
                        Total Bobot: <span id="totalWeightBadge" class="badge {{ $totalCurrentWeight > 1.0 ? 'bg-danger' : ($totalCurrentWeight == 1.0 ? 'bg-success' : 'bg-primary') }} fs-6">{{ number_format($totalCurrentWeight, 2) }} / 1.00</span>
                        <span id="totalWeightNotice" class="ms-2 small text-muted">
                            @if ($totalCurrentWeight > 1.0)
                                (Melebihi 1.00!)
                            @elseif ($totalCurrentWeight == 1.0)
                                (Sempurna)
                            @else
                                (Sisa: {{ number_format(1.0 - $totalCurrentWeight, 2) }})
                            @endif
                        </span>
                    </div>
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

                    <div class="table-responsive">
                        <table class="table table-striped" id="table1">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Kode</th>
                                    <th>Nama Kriteria</th>
                                    <th style="min-width: 170px;">Bobot (Edit Langsung)</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($criterias as $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td><span class="badge bg-light-primary text-primary fw-bold">{{ $item->code }}</span></td>
                                        <td>{{ $item->name }}</td>
                                        <td>
                                            <div class="d-flex align-items-center gap-1">
                                                <input type="number" step="0.01" min="0" max="1"
                                                    class="form-control form-control-sm inline-weight-input"
                                                    style="width: 100px;"
                                                    data-id="{{ $item->id }}"
                                                    data-code="{{ $item->code }}"
                                                    data-url="{{ route('admin.criteria.update-weight', $item) }}"
                                                    data-original="{{ number_format((float) $item->weight, 2, '.', '') }}"
                                                    value="{{ number_format((float) $item->weight, 2, '.', '') }}">
                                                <button type="button"
                                                    class="btn btn-sm btn-success btn-save-inline-weight d-none"
                                                    data-id="{{ $item->id }}" title="Simpan Bobot">
                                                    <i class="bi bi-check-lg"></i>
                                                </button>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                <a href="{{ route('admin.criteria.show', $item) }}"
                                                    class="btn btn-sm btn-outline-info" title="Detail">
                                                    <i class="bi bi-eye"></i>
                                                </a>

                                                <a href="{{ route('admin.criteria.edit', $item) }}"
                                                    class="btn btn-sm btn-outline-warning" title="Edit">
                                                    <i class="bi bi-pencil-square"></i>
                                                </a>

                                                <form action="{{ route('admin.criteria.destroy', $item) }}" method="POST"
                                                    class="delete-criteria-form">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="btn btn-sm btn-outline-danger btn-delete-criteria"
                                                        title="Hapus">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">Belum ada data kriteria.</td>
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
                // Delete confirmation
                document.querySelectorAll('.delete-criteria-form').forEach(function(form) {
                    form.addEventListener('submit', function(event) {
                        event.preventDefault();

                        Swal.fire({
                            title: 'Yakin ingin menghapus data ini?',
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

                // Inline Weight Editing
                const inputs = document.querySelectorAll('.inline-weight-input');
                const totalWeightBadge = document.getElementById('totalWeightBadge');
                const totalWeightNotice = document.getElementById('totalWeightNotice');

                function calculateTotal() {
                    let total = 0;
                    inputs.forEach(input => {
                        const val = parseFloat(input.value) || 0;
                        total += val;
                    });

                    totalWeightBadge.textContent = total.toFixed(2) + ' / 1.00';

                    if (total > 1.0001) {
                        totalWeightBadge.className = 'badge bg-danger fs-6';
                        totalWeightNotice.textContent = '(Melebihi 1.00!)';
                    } else if (Math.abs(total - 1.00) < 0.001) {
                        totalWeightBadge.className = 'badge bg-success fs-6';
                        totalWeightNotice.textContent = '(Sempurna)';
                    } else {
                        totalWeightBadge.className = 'badge bg-primary fs-6';
                        const sisa = (1.00 - total).toFixed(2);
                        totalWeightNotice.textContent = `(Sisa: ${sisa})`;
                    }

                    // Check validation per input
                    inputs.forEach(input => {
                        const btnSave = input.nextElementSibling;
                        const originalVal = parseFloat(input.dataset.original) || 0;
                        const currentVal = parseFloat(input.value) || 0;

                        if (total > 1.0001) {
                            if (currentVal !== originalVal) {
                                input.classList.add('is-invalid');
                            }
                            if (btnSave) btnSave.disabled = true;
                        } else {
                            input.classList.remove('is-invalid');
                            if (btnSave) btnSave.disabled = false;
                        }

                        if (btnSave) {
                            if (Math.abs(currentVal - originalVal) > 0.0001 && total <= 1.0001) {
                                btnSave.classList.remove('d-none');
                            } else {
                                btnSave.classList.add('d-none');
                            }
                        }
                    });
                }

                inputs.forEach(input => {
                    input.addEventListener('input', calculateTotal);
                    input.addEventListener('change', calculateTotal);

                    input.addEventListener('keydown', function(e) {
                        if (e.key === 'Enter') {
                            e.preventDefault();
                            const btnSave = this.nextElementSibling;
                            if (btnSave && !btnSave.classList.contains('d-none') && !btnSave.disabled) {
                                btnSave.click();
                            }
                        }
                    });
                });

                document.querySelectorAll('.btn-save-inline-weight').forEach(button => {
                    button.addEventListener('click', function() {
                        const input = this.previousElementSibling;
                        const url = input.dataset.url;
                        const newWeight = input.value;
                        const originalVal = input.dataset.original;

                        this.disabled = true;
                        this.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span>';

                        fetch(url, {
                            method: 'PATCH',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ weight: newWeight })
                        })
                        .then(response => response.json())
                        .then(data => {
                            this.disabled = false;
                            this.innerHTML = '<i class="bi bi-check-lg"></i>';

                            if (data.success) {
                                input.dataset.original = parseFloat(newWeight).toFixed(2);
                                input.value = parseFloat(newWeight).toFixed(2);
                                this.classList.add('d-none');

                                calculateTotal();

                                const Toast = Swal.mixin({
                                    toast: true,
                                    position: 'top-end',
                                    showConfirmButton: false,
                                    timer: 2500,
                                    timerProgressBar: true
                                });
                                Toast.fire({
                                    icon: 'success',
                                    title: data.message
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal Menyimpan',
                                    text: data.message || 'Terjadi kesalahan.'
                                });
                                calculateTotal();
                            }
                        })
                        .catch(error => {
                            this.disabled = false;
                            this.innerHTML = '<i class="bi bi-check-lg"></i>';
                            Swal.fire({
                                icon: 'error',
                                title: 'Kesalahan Sistem',
                                text: 'Gagal terhubung ke server.'
                            });
                        });
                    });
                });

                calculateTotal();
            });
        </script>
    @endpush
@endsection
