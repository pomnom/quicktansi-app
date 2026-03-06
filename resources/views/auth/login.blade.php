<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Kuitansi App</title>
    <link href="{{ asset('admin/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('admin/css/sb-admin-2.min.css') }}" rel="stylesheet">
    <style>
        .bg-login-image {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-container {
            max-width: 500px;
            width: 100%;
            padding: 20px;
        }

        .login-card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
        }

        .login-card .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 10px 10px 0 0 !important;
            padding: 30px 20px;
            text-align: center;
        }

        .login-card .card-header h3 {
            color: white;
            font-weight: 600;
            margin: 0;
        }

        .login-card .card-body {
            padding: 30px;
        }

        .form-group label {
            font-weight: 500;
            margin-bottom: 8px;
            color: #333;
        }

        .form-control {
            border-radius: 5px;
            padding: 10px 15px;
            border: 1px solid #dee2e6;
            font-size: 14px;
        }

        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }

        .btn-login {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 5px;
            padding: 10px 20px;
            font-weight: 600;
            width: 100%;
            margin-top: 20px;
            transition: all 0.3s ease;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
            color: white;
        }

        .form-check {
            padding-left: 0;
        }

        .form-check-input {
            margin-top: 5px;
        }

        .form-check-label {
            margin-left: 8px;
            font-size: 14px;
            font-weight: 400;
        }

        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 20px;
            border: 1px solid #f5c6cb;
        }

        .is-invalid {
            border-color: #dc3545 !important;
        }

        .invalid-feedback {
            display: block;
            color: #dc3545;
            font-size: 13px;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="card login-card">
            <div class="card-header">
                <h3>Kuitansi System</h3>
            </div>
            <div class="card-body">
                @if ($errors->any())
                    <div class="error-message">
                        <strong>Login Gagal!</strong>
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                <form action="{{ route('auth.login') }}" method="POST" novalidate>
                    @csrf

                    <div class="form-group mb-3">
                        <label for="nip" class="form-label">NIP</label>
                        <input 
                            type="text" 
                            class="form-control @error('nip') is-invalid @enderror" 
                            id="nip" 
                            name="nip" 
                            placeholder="Masukkan NIP anda"
                            value="{{ old('nip') }}"
                            required
                            maxlength="18"
                        >
                        @error('nip')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input 
                            type="password" 
                            class="form-control @error('password') is-invalid @enderror" 
                            id="password" 
                            name="password" 
                            placeholder="Masukkan password anda"
                            required
                        >
                        @error('password')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-check mb-3">
                        <input 
                            type="checkbox" 
                            class="form-check-input" 
                            id="remember" 
                            name="remember" 
                            {{ old('remember') ? 'checked' : '' }}
                        >
                        <label class="form-check-label" for="remember">
                            Ingat saya
                        </label>
                    </div>

                    <button type="submit" class="btn btn-primary btn-login">
                        <span>Masuk</span>
                    </button>
                </form>

                <div style="text-align: center; margin-top: 20px; color: #666; font-size: 13px;">
                    <p><small>Username & Password default adalah <strong>NIP</strong> anda</small></p>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('admin/js/bootstrap.bundle.min.js') }}"></script>
</body>
</html>
