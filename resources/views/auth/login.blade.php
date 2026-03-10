<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Kuitansi App</title>
    <link href="{{ asset('admin/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('admin/css/sb-admin-2.min.css') }}" rel="stylesheet">
    <link href="{{ asset('admin/css/all.min.css') }}" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #4e73df 0%, #224abe 60%, #1cc88a 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Nunito', sans-serif;
        }

        /* Animated blobs */
        body::before, body::after {
            content: '';
            position: fixed;
            border-radius: 50%;
            opacity: 0.08;
            animation: float 8s ease-in-out infinite;
        }
        body::before {
            width: 500px; height: 500px;
            background: white;
            top: -150px; right: -150px;
            animation-delay: 0s;
        }
        body::after {
            width: 350px; height: 350px;
            background: white;
            bottom: -100px; left: -100px;
            animation-delay: 4s;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0) scale(1); }
            50%       { transform: translateY(30px) scale(1.05); }
        }

        .login-wrapper {
            width: 100%;
            max-width: 960px;
            padding: 20px;
            position: relative;
            z-index: 1;
        }

        /* ---- Card ---- */
        .login-card {
            border: none;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            display: flex;
            min-height: 520px;
        }

        /* ---- Left panel ---- */
        .login-brand {
            background: linear-gradient(160deg, #4e73df 0%, #224abe 100%);
            flex: 0 0 42%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 40px 30px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        .login-brand::before {
            content: '';
            position: absolute;
            width: 300px; height: 300px;
            border: 40px solid rgba(255,255,255,0.07);
            border-radius: 50%;
            top: -80px; right: -80px;
        }
        .login-brand::after {
            content: '';
            position: absolute;
            width: 200px; height: 200px;
            border: 30px solid rgba(255,255,255,0.07);
            border-radius: 50%;
            bottom: -60px; left: -60px;
        }
        .brand-icon {
            width: 80px; height: 80px;
            background: rgba(255,255,255,0.15);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 20px;
            backdrop-filter: blur(10px);
            border: 2px solid rgba(255,255,255,0.25);
        }
        .brand-icon i { font-size: 36px; color: white; }
        .brand-title {
            color: white;
            font-size: 22px;
            font-weight: 800;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }
        .brand-subtitle {
            color: rgba(255,255,255,0.75);
            font-size: 13px;
            line-height: 1.6;
        }
        .brand-divider {
            width: 50px; height: 3px;
            background: rgba(255,255,255,0.4);
            border-radius: 2px;
            margin: 20px auto;
        }
        .brand-features {
            list-style: none;
            padding: 0; margin: 0;
            text-align: left;
        }
        .brand-features li {
            color: rgba(255,255,255,0.8);
            font-size: 12px;
            padding: 5px 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .brand-features li i { color: #1cc88a; font-size: 11px; }

        /* ---- Right panel (form) ---- */
        .login-form-panel {
            flex: 1;
            background: white;
            padding: 48px 44px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .login-form-panel h4 {
            font-size: 20px;
            font-weight: 800;
            color: #2d3748;
            margin-bottom: 4px;
        }
        .login-form-panel .lead-text {
            color: #a0aec0;
            font-size: 13px;
            margin-bottom: 28px;
        }

        /* Input groups */
        .input-group .input-group-text {
            background: #f8f9fc;
            border-color: #d1d3e2;
            color: #b7b9cc;
            min-width: 44px;
            justify-content: center;
        }
        .input-group .form-control {
            border-left: 0;
            padding: 0.6rem 1rem;
            border-color: #d1d3e2;
            color: #6e707e;
            font-size: 14px;
        }
        .input-group .form-control:focus {
            border-color: #bac8ff;
            box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.15);
            outline: none;
        }
        .input-group .form-control:focus + .input-group-text,
        .input-group-text + .form-control:focus {
            border-color: #bac8ff;
        }
        .input-group:focus-within .input-group-text {
            border-color: #bac8ff;
            color: #4e73df;
        }
        .input-group .form-control.is-invalid {
            border-color: #e74a3b !important;
        }
        .invalid-feedback { font-size: 12px; }

        .form-label {
            font-weight: 600;
            font-size: 13px;
            color: #4a5568;
            margin-bottom: 6px;
        }

        /* Remember me */
        .form-check-label {
            font-size: 13px;
            color: #6e707e;
        }

        /* Submit button */
        .btn-login {
            background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
            border: none;
            border-radius: 8px;
            padding: 11px;
            font-weight: 700;
            font-size: 14px;
            letter-spacing: 0.5px;
            width: 100%;
            transition: all 0.25s ease;
            box-shadow: 0 4px 14px rgba(78, 115, 223, 0.4);
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(78, 115, 223, 0.55);
            background: linear-gradient(135deg, #5a7ee5 0%, #2a57cc 100%);
        }
        .btn-login:active { transform: translateY(0); }

        /* Error alert */
        .alert-login-error {
            background: #fff5f5;
            border: 1px solid #fed7d7;
            border-left: 4px solid #e74a3b;
            color: #c53030;
            border-radius: 8px;
            padding: 12px 16px;
            font-size: 13px;
            margin-bottom: 20px;
        }

        /* Footer note */
        .login-note {
            text-align: center;
            margin-top: 20px;
            color: #a0aec0;
            font-size: 12px;
        }

        /* Responsive */
        @media (max-width: 640px) {
            .login-brand { display: none; }
            .login-form-panel { padding: 36px 28px; }
            .login-card { border-radius: 12px; }
        }
    </style>
</head>
<body>
    <div class="login-wrapper">
        <div class="login-card">

            {{-- Left brand panel --}}
            <div class="login-brand">
                <div class="brand-icon">
                    <i class="fas fa-receipt"></i>
                </div>
                <div class="brand-title">QuickTansi</div>
                <div class="brand-subtitle">Sistem Manajemen Kuitansi<br>Pemerintah Daerah</div>
                <div class="brand-divider"></div>
                <ul class="brand-features">
                    <li><i class="fas fa-check-circle"></i> Kelola kuitansi dengan mudah</li>
                    <li><i class="fas fa-check-circle"></i> Hitung PPH 22 &amp; 23 otomatis</li>
                    <li><i class="fas fa-check-circle"></i> Cetak &amp; ekspor dokumen</li>
                    <li><i class="fas fa-check-circle"></i> Multi instansi</li>
                </ul>
            </div>

            {{-- Right form panel --}}
            <div class="login-form-panel">
                <h4>Selamat Datang!</h4>
                <p class="lead-text">Masuk untuk melanjutkan ke sistem</p>

                @if ($errors->any())
                    <div class="alert-login-error">
                        <i class="fas fa-exclamation-circle mr-1"></i>
                        <strong>Login Gagal!</strong>
                        @foreach ($errors->all() as $error)
                            <div class="mt-1">{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                <form action="{{ route('auth.login') }}" method="POST" novalidate>
                    @csrf

                    <div class="form-group mb-3">
                        <label for="nip" class="form-label">Nomor Induk Pegawai (NIP)</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-id-badge fa-sm"></i></span>
                            </div>
                            <input
                                type="text"
                                class="form-control @error('nip') is-invalid @enderror"
                                id="nip"
                                name="nip"
                                placeholder="Masukkan NIP Anda"
                                value="{{ old('nip') }}"
                                required
                                maxlength="18"
                                autocomplete="username"
                            >
                            @error('nip')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-lock fa-sm"></i></span>
                            </div>
                            <input
                                type="password"
                                class="form-control @error('password') is-invalid @enderror"
                                id="password"
                                name="password"
                                placeholder="Masukkan password Anda"
                                required
                                autocomplete="current-password"
                            >
                            @error('password')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <div class="form-check">
                            <input
                                type="checkbox"
                                class="form-check-input"
                                id="remember"
                                name="remember"
                                {{ old('remember') ? 'checked' : '' }}
                            >
                            <label class="form-check-label" for="remember">Ingat saya</label>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-login text-white">
                        <i class="fas fa-sign-in-alt mr-2"></i>Masuk
                    </button>
                </form>

                <div class="login-note">
                    <i class="fas fa-info-circle mr-1"></i>
                    Password default sama dengan <strong>NIP</strong> Anda
                </div>
            </div>

        </div>
    </div>

    <script src="{{ asset('admin/js/bootstrap.bundle.min.js') }}"></script>
</body>
</html>
