@extends('layouts.guest')

@section('content')
    <style>
        #auth #auth-right.auth-media-panel {
            align-items: center;
            background: url("{{ asset('images/bg-auth.jpeg') }}") center / cover no-repeat;
            display: flex;
            justify-content: center;
            min-height: 100vh;
            padding: 3rem;
            position: relative;
        }

        #auth #auth-right.auth-media-panel::before {
            background: rgba(12, 25, 45, 0.28);
            content: "";
            inset: 0;
            position: absolute;
        }

        .auth-glass-card {
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
            background: rgba(255, 255, 255, 0.22);
            border: 1px solid rgba(255, 255, 255, 0.42);
            border-radius: 16px;
            box-shadow: 0 24px 80px rgba(0, 0, 0, 0.24);
            color: #fff;
            display: flex;
            flex-direction: column;
            justify-content: center;
            min-height: 450px;
            max-width: 560px;
            padding: 3rem 2.25rem;
            margin-top: 5rem;
            position: relative;
            text-align: center;
            text-shadow: 0 2px 12px rgba(0, 0, 0, 0.36);
            width: 100%;
        }

        .auth-logo-row {
            align-items: center;
            display: flex;
            gap: 1.25rem;
            justify-content: center;
            margin-bottom: 2rem;
        }

        .auth-logo-row img {
            filter: drop-shadow(0 8px 18px rgba(0, 0, 0, 0.22));
            height: 86px;
            object-fit: contain;
            width: 86px;
        }

        .auth-glass-card h2 {
            color: #fff;
            font-size: 2rem;
            font-weight: 800;
            letter-spacing: 0;
            line-height: 1.25;
            margin-bottom: 1.25rem;
            text-transform: uppercase;
        }

        .auth-glass-card p {
            font-size: 1.15rem;
            font-weight: 600;
            line-height: 1.65;
            margin-bottom: 0;
        }
    </style>

    <div class="row h-100">
        <div class="col-lg-5 col-12">
            <div id="auth-left">
                <div class="auth-logo">
                    <a href="{{ route('login') }}">
                        <span class="fw-bold fs-1 text-primary">SPK Jalan</span>
                    </a>
                </div>
                <h1 class="auth-title fs-1">Daftar Akun</h1>
                <p class="auth-subtitle mb-5">Lengkapi data berikut untuk membuat akun baru.</p>

                <form method="POST" action="{{ route('register') }}">
                    @csrf
                    <div class="form-group position-relative has-icon-left mb-4">
                        <input type="text" name="name" id="name"
                            class="form-control form-control-xl @error('name') is-invalid @enderror" placeholder="Nama"
                            value="{{ old('name') }}" required autofocus autocomplete="name">
                        <div class="form-control-icon">
                            <i class="bi bi-person"></i>
                        </div>
                        @error('name')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group position-relative has-icon-left mb-4">
                        <input type="email" name="email" id="email"
                            class="form-control form-control-xl @error('email') is-invalid @enderror" placeholder="Email"
                            value="{{ old('email') }}" required autocomplete="username">
                        <div class="form-control-icon">
                            <i class="bi bi-envelope"></i>
                        </div>
                        @error('email')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group position-relative has-icon-left mb-4">
                        <input type="password" name="password" id="password"
                            class="form-control form-control-xl @error('password') is-invalid @enderror"
                            placeholder="Kata Sandi" required autocomplete="new-password">
                        <div class="form-control-icon">
                            <i class="bi bi-shield-lock"></i>
                        </div>
                        @error('password')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group position-relative has-icon-left mb-4">
                        <input type="password" name="password_confirmation" id="password_confirmation"
                            class="form-control form-control-xl @error('password_confirmation') is-invalid @enderror"
                            placeholder="Konfirmasi Kata Sandi" required autocomplete="new-password">
                        <div class="form-control-icon">
                            <i class="bi bi-shield-lock"></i>
                        </div>
                        @error('password_confirmation')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-primary btn-block btn-lg shadow-lg mt-5">Daftar</button>
                </form>
                <div class="text-center mt-5 text-lg fs-4">
                    <p class='text-muted'>Sudah punya akun? <a href="{{ route('login') }}" class="fw-bold">Masuk</a>.</p>
                </div>
            </div>
        </div>
        <div class="col-lg-7 d-none d-lg-block">
            <div id="auth-right" class="auth-media-panel">
                <div class="auth-glass-card">
                    <div class="auth-logo-row">
                        <img src="{{ asset('images/logo-kabupaten.png') }}" alt="Logo Kabupaten Timor Tengah Utara">
                        <img src="{{ asset('images/pekerjaan-umum.png') }}" alt="Logo Pekerjaan Umum">
                    </div>
                    <h2>DINAS PEKERJAAN UMUM<br>KABUPATEN TIMOR TENGAH UTARA</h2>
                    <p>(Sistem Pendukung Keputusan Prioritas Perbaikan Jalan)</p>
                </div>
            </div>
        </div>
    </div>
@endsection
