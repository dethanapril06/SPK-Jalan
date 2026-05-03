@extends('layouts.surveyor')

@section('title', 'Notifications Surveyor')

@section('content')
    <div class="page-heading">
        <div class="page-title mb-3">
            <div class="row">
                <div class="col-12 col-md-8">
                    <h3>Notifications</h3>
                    <p class="text-muted small mb-0">Kelola notifikasi yang sudah ditugaskan untuk Anda</p>
                </div>
                <div class="col-12 col-md-4 text-md-end">
                    <a href="{{ route('surveyor.dashboard') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="row g-3">
                <div class="col-12 col-lg-8">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="card-title mb-0">Semua Notifikasi</h4>
                            @if ($notifications->total() > 0)
                                <span class="badge bg-primary">{{ $notifications->total() }} total</span>
                            @endif
                        </div>
                        <div class="card-body p-0">
                            @forelse ($notifications as $notif)
                                <div
                                    class="d-flex align-items-start p-3 border-bottom {{ is_null($notif->read_at) ? 'bg-light-primary' : '' }}">
                                    {{-- Icon --}}
                                    <div class="notification-icon bg-primary me-3 flex-shrink-0"
                                        style="width:40px; height:40px; border-radius:50%; display:flex; align-items:center; justify-content:center;">
                                        <i class="bi bi-clipboard-check text-white"></i>
                                    </div>

                                    {{-- Konten --}}
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <p class="mb-1 fw-bold">
                                                Tugas Baru
                                                @if (is_null($notif->read_at))
                                                    <span class="badge bg-danger ms-1" style="font-size:10px;">Baru</span>
                                                @endif
                                            </p>
                                            <small class="text-muted text-nowrap ms-2">
                                                {{ $notif->created_at->diffForHumans() }}
                                            </small>
                                        </div>
                                        <p class="mb-0 text-muted small">{{ $notif->data['pesan'] }}</p>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-5">
                                    <i class="bi bi-bell-slash fs-1 text-muted"></i>
                                    <p class="text-muted mt-2">Belum ada notifikasi</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- Sidebar info --}}
                <div class="col-12 col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Ringkasan</h4>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="small">Total Notifikasi</span>
                                    <strong>{{ $notifications->total() }}</strong>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="small">Belum Dibaca</span>
                                    <strong class="text-danger">{{ auth()->user()->unreadNotifications->count() }}</strong>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span class="small">Sudah Dibaca</span>
                                    <strong class="text-success">
                                        {{ $notifications->total() - auth()->user()->unreadNotifications->count() }}
                                    </strong>
                                </div>
                            </div>

                            <div class="alert alert-light-info small mb-0">
                                <i class="bi bi-info-circle me-1"></i>
                                Notifikasi dengan latar <strong>biru</strong> berarti belum dibaca.
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </section>
    </div>
@endsection
