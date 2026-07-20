@extends('layouts.admin')

@section('title', 'Report Penilaian')

@section('content')
    <div class="page-heading">
        <div class="page-title mb-3">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Report Penilaian</h3>
                    <p class="text-muted mb-0">Semua penilaian surveyor terhadap seluruh alternatif.</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Report Penilaian</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="row g-3 mb-3">
                <div class="col-12 col-md-4">
                    <div class="card mb-0">
                        <div class="card-body">
                            <p class="text-muted mb-1">Total Penilaian</p>
                            <h4 class="mb-0">{{ $summary['total_assessments'] }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="card mb-0">
                        <div class="card-body">
                            <p class="text-muted mb-1">Surveyor Terlibat</p>
                            <h4 class="mb-0">{{ $summary['total_surveyors'] }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="card mb-0">
                        <div class="card-body">
                            <p class="text-muted mb-1">Alternatif Dinilai</p>
                            <h4 class="mb-0">{{ $summary['total_alternatives'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Filter Report</h4>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.reports.assessments') }}" class="row g-3">
                        <div class="col-12 col-md-4">
                            <label for="period_id" class="form-label">Periode Penilaian</label>
                            <select name="period_id" id="period_id" class="form-select">
                                <option value="">Semua Periode</option>
                                @foreach ($periods as $period)
                                    <option value="{{ $period->id }}" @selected((string) $filters['period_id'] === (string) $period->id)>
                                        {{ $period->name }} ({{ $period->year }}) - {{ ucfirst($period->status) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 col-md-4">
                            <label for="surveyor_id" class="form-label">Surveyor</label>
                            <select name="surveyor_id" id="surveyor_id" class="form-select">
                                <option value="">Semua Surveyor</option>
                                @foreach ($surveyors as $surveyor)
                                    <option value="{{ $surveyor->id }}" @selected((string) $filters['surveyor_id'] === (string) $surveyor->id)>
                                        {{ $surveyor->code }} - {{ $surveyor->user?->name ?? '-' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 col-md-4">
                            <label for="alternative_id" class="form-label">Alternatif</label>
                            <select name="alternative_id" id="alternative_id" class="form-select">
                                <option value="">Semua Alternatif</option>
                                @foreach ($alternatives as $alternative)
                                    <option value="{{ $alternative->id }}" @selected((string) $filters['alternative_id'] === (string) $alternative->id)>
                                        {{ $alternative->code }} - {{ $alternative->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 col-md-3">
                            <label for="criteria_id" class="form-label">Kriteria</label>
                            <select name="criteria_id" id="criteria_id" class="form-select">
                                <option value="">Semua Kriteria</option>
                                @foreach ($criterias as $criteria)
                                    <option value="{{ $criteria->id }}" @selected((string) $filters['criteria_id'] === (string) $criteria->id)>
                                        {{ $criteria->code }} - {{ $criteria->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 col-md-3">
                            <label for="sub_criteria_id" class="form-label">Sub Kriteria</label>
                            <select name="sub_criteria_id" id="sub_criteria_id" class="form-select">
                                <option value="">Semua Sub Kriteria</option>
                                @foreach ($subCriterias as $sub)
                                    <option value="{{ $sub->id }}" data-criteria-id="{{ $sub->criteria_id }}" @selected((string) $filters['sub_criteria_id'] === (string) $sub->id)>
                                        {{ $sub->criteria?->code ?? '' }} - {{ $sub->code }} : {{ $sub->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 col-md-2">
                            <label for="date_from" class="form-label">Dari Tanggal</label>
                            <input type="date" name="date_from" id="date_from" class="form-control"
                                value="{{ $filters['date_from'] }}">
                        </div>

                        <div class="col-12 col-md-2">
                            <label for="date_to" class="form-label">Sampai Tanggal</label>
                            <input type="date" name="date_to" id="date_to" class="form-control"
                                value="{{ $filters['date_to'] }}">
                        </div>

                        <div class="col-12 col-md-2 d-flex align-items-end gap-2">
                            <button type="submit" class="btn btn-primary w-100">Terapkan</button>
                            <a href="{{ route('admin.reports.assessments') }}"
                                class="btn btn-light-secondary w-100">Reset</a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <h4 class="card-title mb-0">Data Penilaian</h4>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.reports.assessments.excel', request()->query()) }}"
                                class="btn btn-success btn-sm">
                                <i class="bi bi-file-earmark-excel-fill"></i> Report Excel
                            </a>
                            <a href="{{ route('admin.reports.assessments.pdf', request()->query()) }}"
                                class="btn btn-danger btn-sm">
                                <i class="bi bi-file-earmark-pdf-fill"></i> Report PDF
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if ($assessments->isEmpty())
                        <div class="alert alert-light-info mb-0">Tidak ada data penilaian untuk filter yang dipilih.</div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle" id="table1">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Periode</th>
                                        <th>Surveyor</th>
                                        <th>Alternatif</th>
                                        <th>Kriteria</th>
                                        <th>Sub Kriteria</th>
                                        <th>Aspek</th>
                                        <th>Dokumentasi</th>
                                        <th>Nilai</th>
                                        <th>Catatan</th>
                                        <th>Dinilai Pada</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($assessments as $assessment)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                <span class="fw-semibold">{{ $assessment->period?->name ?? '-' }}</span>
                                                <div class="small text-muted">Tahun {{ $assessment->period?->year ?? '-' }}</div>
                                            </td>
                                            <td>
                                                {{ $assessment->surveyor?->code ?? '-' }}
                                                <div class="small text-muted">
                                                    {{ $assessment->surveyor?->user?->name ?? '-' }}</div>
                                            </td>
                                            <td>
                                                {{ $assessment->alternative?->code ?? '-' }}
                                                <div class="small text-muted">{{ $assessment->alternative?->name ?? '-' }}
                                                </div>
                                            </td>
                                            <td>{{ $assessment->subCriteria?->criteria?->name ?? '-' }}</td>
                                            <td>{{ $assessment->subCriteria?->name ?? '-' }}</td>
                                            <td>{{ $assessment->assessmentAspect?->name ?? '-' }}</td>
                                            <td>
                                                @if ($assessment->photo_path)
                                                    @php
                                                        $photos = is_string($assessment->photo_path)
                                                            ? json_decode($assessment->photo_path, true)
                                                            : $assessment->photo_path;

                                                        if (!is_array($photos)) {
                                                            $photos = [$assessment->photo_path];
                                                        }
                                                    @endphp

                                                    <div class="d-flex flex-wrap gap-1">
                                                        @foreach (array_filter($photos) as $photo)
                                                            <a href="{{ asset('storage/' . $photo) }}" target="_blank"
                                                                rel="noopener noreferrer" class="d-inline-block"
                                                                title="Klik untuk lihat ukuran penuh">
                                                                <img src="{{ asset('storage/' . $photo) }}"
                                                                    alt="Foto penilaian {{ $assessment->alternative?->name ?? '' }}"
                                                                    class="rounded border"
                                                                    style="width: 50px; height: 50px; object-fit: cover;">
                                                            </a>
                                                        @endforeach
                                                    </div>
                                                    <div class="small text-muted mt-1">{{ count(array_filter($photos)) }}
                                                        Foto dilampirkan</div>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>{{ $assessment->assessmentAspect?->value ?? '-' }}</td>
                                            <td>{{ $assessment->notes ?: '-' }}</td>
                                            <td>{{ $assessment->assessed_at?->format('d-m-Y H:i') ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const criteriaSelect = document.getElementById('criteria_id');
        const subCriteriaSelect = document.getElementById('sub_criteria_id');

        if (!criteriaSelect || !subCriteriaSelect) return;

        function filterSubCriteria() {
            const selectedCriteriaId = criteriaSelect.value;
            const options = subCriteriaSelect.querySelectorAll('option[data-criteria-id]');
            let hasVisibleSelected = false;

            options.forEach(option => {
                if (!selectedCriteriaId || option.dataset.criteriaId === selectedCriteriaId) {
                    option.hidden = false;
                    option.style.display = '';
                    if (option.selected) hasVisibleSelected = true;
                } else {
                    option.hidden = true;
                    option.style.display = 'none';
                    if (option.selected) option.selected = false;
                }
            });

            if (!hasVisibleSelected && subCriteriaSelect.value !== '') {
                subCriteriaSelect.value = '';
            }
        }

        criteriaSelect.addEventListener('change', filterSubCriteria);
        filterSubCriteria();
    });
</script>
@endpush
