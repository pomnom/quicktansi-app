@extends('layouts.app')

@section('title', 'Profil Saya')

@section('content')

<!-- Hero Banner -->
<div class="dashboard-hero mb-4">
    <div class="d-flex align-items-center justify-content-between" style="position:relative;z-index:1;">
        <div>
            <div class="hero-badge">
                <i class="fas fa-circle" style="font-size:7px;color:#1cc88a;"></i> Akun Saya
            </div>
            <div class="hero-title">Profil Saya</div>
            <p class="hero-sub">Lihat dan perbarui informasi akun Anda sendiri.</p>
            <div class="hero-date">
                <i class="fas fa-calendar-alt mr-1"></i>
                {{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}
            </div>
        </div>
        <div class="hero-icon d-none d-md-flex">
            <i class="fas fa-user-circle"></i>
        </div>
    </div>
    <div class="hero-stats-strip">
        <div class="hs-item">
            <i class="fas fa-id-badge hs-icon"></i>
            <div>
                <div class="hs-label">NIP</div>
                <div class="hs-value" style="font-size:14px;">{{ $user->nip }}</div>
            </div>
        </div>
        <div class="hs-item">
            <i class="fas fa-building hs-icon"></i>
            <div>
                <div class="hs-label">Instansi</div>
                <div class="hs-value" style="font-size:13px;">{{ $user->instansi ?? '-' }}</div>
            </div>
        </div>
        <div class="hs-item">
            <i class="fas fa-shield-alt hs-icon"></i>
            <div>
                <div class="hs-label">Peran</div>
                <div class="hs-value" style="font-size:13px;">{{ $user->is_superadmin ? 'Superadmin' : 'Operator' }}</div>
            </div>
        </div>
        <div class="hs-item">
            <i class="fas fa-clock hs-icon"></i>
            <div>
                <div class="hs-label">Bergabung</div>
                <div class="hs-value" style="font-size:13px;">{{ $user->created_at ? $user->created_at->locale('id')->isoFormat('MMM YYYY') : '-' }}</div>
            </div>
        </div>
    </div>
</div>

<div class="row">

    {{-- Kolom Kiri: Info + Edit Profil --}}
    <div class="col-lg-4 mb-4">

        {{-- Kartu Avatar & Info --}}
        <div class="card recent-card mb-4">
            <div class="card-body text-center py-4">
                <div class="mb-3" style="position:relative;display:inline-block;">
                    <div style="width:100px;height:100px;border-radius:50%;background:linear-gradient(135deg,#4e73df,#1cc88a);display:flex;align-items:center;justify-content:center;margin:0 auto;font-size:40px;color:#fff;font-weight:700;">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                </div>
                <h5 class="font-weight-bold mb-1">{{ $user->name }}</h5>
                <p class="text-muted small mb-2">{{ $user->email }}</p>
                <div class="mb-2">
                    @if($user->is_superadmin)
                        <span class="badge badge-warning text-white px-3 py-1" style="border-radius:20px;font-size:11px;">
                            <i class="fas fa-crown mr-1"></i>Superadmin
                        </span>
                    @else
                        <span class="badge badge-primary px-3 py-1" style="border-radius:20px;font-size:11px;">
                            <i class="fas fa-user mr-1"></i>Operator
                        </span>
                    @endif
                </div>
                <hr>
                <div class="text-left px-2">
                    <div class="d-flex align-items-center mb-2">
                        <i class="fas fa-id-card text-primary mr-2" style="width:18px;"></i>
                        <div>
                            <div class="text-muted" style="font-size:11px;">NIP</div>
                            <div class="font-weight-bold small">{{ $user->nip }}</div>
                        </div>
                    </div>
                    <div class="d-flex align-items-center mb-2">
                        <i class="fas fa-building text-success mr-2" style="width:18px;"></i>
                        <div>
                            <div class="text-muted" style="font-size:11px;">Instansi</div>
                            <div class="font-weight-bold small">{{ $user->instansi ?? '-' }}</div>
                        </div>
                    </div>
                    <div class="d-flex align-items-center mb-2">
                        <i class="fas fa-phone text-info mr-2" style="width:18px;"></i>
                        <div>
                            <div class="text-muted" style="font-size:11px;">No. Telepon</div>
                            <div class="font-weight-bold small">{{ $user->no_telp ?? '-' }}</div>
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                        <i class="fas fa-calendar-alt text-warning mr-2" style="width:18px;"></i>
                        <div>
                            <div class="text-muted" style="font-size:11px;">Bergabung</div>
                            <div class="font-weight-bold small">
                                {{ $user->created_at ? $user->created_at->locale('id')->isoFormat('D MMMM YYYY') : '-' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- Kolom Kanan: Form Edit --}}
    <div class="col-lg-8">

        {{-- Form Edit Profil --}}
        <div class="card recent-card mb-4">
            <div class="card-header">
                <div class="header-icon"><i class="fas fa-user-edit"></i></div>
                <h6>Edit Informasi Profil</h6>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('profile.update') }}">
                    @csrf
                    @method('PUT')

                    {{-- NIP & Instansi (read-only) --}}
                    <div class="form-section-label d-flex align-items-center mb-3">
                        <span class="form-section-icon bg-secondary text-white"><i class="fas fa-lock"></i></span>
                        <span class="font-weight-bold ml-2 text-secondary" style="font-size:12px;text-transform:uppercase;letter-spacing:.5px;">Informasi Tetap (dikelola admin)</span>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold small">NIP</label>
                                <input type="text" class="form-control form-control-sm" value="{{ $user->nip }}" readonly
                                    style="background:#f8f9fc;cursor:not-allowed;">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold small">Instansi</label>
                                <input type="text" class="form-control form-control-sm" value="{{ $user->instansi ?? '-' }}" readonly
                                    style="background:#f8f9fc;cursor:not-allowed;">
                            </div>
                        </div>
                    </div>

                    <hr class="my-3">

                    {{-- Informasi yang bisa diedit --}}
                    <div class="form-section-label d-flex align-items-center mb-3">
                        <span class="form-section-icon bg-primary text-white"><i class="fas fa-pencil-alt"></i></span>
                        <span class="font-weight-bold ml-2 text-primary" style="font-size:12px;text-transform:uppercase;letter-spacing:.5px;">Informasi yang Dapat Diubah</span>
                    </div>

                    <div class="form-group">
                        <label for="name" class="font-weight-bold small">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                            id="name" name="name" value="{{ old('name', $user->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="email" class="font-weight-bold small">Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror"
                            id="email" name="email" value="{{ old('email', $user->email) }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="no_telp" class="font-weight-bold small">No. Telepon</label>
                        <input type="text" class="form-control @error('no_telp') is-invalid @enderror"
                            id="no_telp" name="no_telp" value="{{ old('no_telp', $user->no_telp) }}"
                            placeholder="Contoh: 08123456789">
                        @error('no_telp')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="text-right mt-3">
                        <button type="submit" class="btn btn-primary" style="border-radius:8px;min-width:140px;">
                            <i class="fas fa-save mr-1"></i>Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Form Ganti Password --}}
        <div class="card recent-card mb-4">
            <div class="card-header">
                <div class="header-icon bg-warning"><i class="fas fa-key"></i></div>
                <h6>Ganti Password</h6>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('profile.updatePassword') }}">
                    @csrf
                    @method('PUT')

                    <div class="alert alert-warning py-2 px-3 mb-3" style="border-radius:8px;font-size:13px;">
                        <i class="fas fa-info-circle mr-1"></i>
                        Password minimal 8 karakter. Setelah diganti, gunakan password baru untuk login berikutnya.
                    </div>

                    <div class="form-group">
                        <label for="current_password" class="font-weight-bold small">Password Saat Ini <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="password" class="form-control @error('current_password') is-invalid @enderror"
                                id="current_password" name="current_password" required autocomplete="current-password">
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary toggle-pw" type="button" data-target="current_password">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            @error('current_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="password" class="font-weight-bold small">Password Baru <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="password" class="form-control @error('password') is-invalid @enderror"
                                id="password" name="password" required autocomplete="new-password">
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary toggle-pw" type="button" data-target="password">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation" class="font-weight-bold small">Konfirmasi Password Baru <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="password" class="form-control"
                                id="password_confirmation" name="password_confirmation" required autocomplete="new-password">
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary toggle-pw" type="button" data-target="password_confirmation">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="text-right mt-3">
                        <button type="submit" class="btn btn-warning text-white" style="border-radius:8px;min-width:140px;">
                            <i class="fas fa-key mr-1"></i>Ganti Password
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>

@endsection

@push('scripts')
<script>
    // Toggle show/hide password
    document.querySelectorAll('.toggle-pw').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const targetId = this.dataset.target;
            const input = document.getElementById(targetId);
            const icon = this.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        });
    });
</script>
@endpush
