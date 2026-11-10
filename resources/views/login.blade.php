@extends('layouts.app')
@section('title', 'Masuk')

@section('content')
<div class="login-layout">
    <section class="login-introduction" aria-labelledby="welcome-title">
        <div class="institution-label"><span class="institution-line" aria-hidden="true"></span>Fakultas Ilmu Komputer</div>
        <h1 id="welcome-title">Ruang kerja untuk<br><span>laboratorium yang<br>lebih teratur.</span></h1>
        <p class="login-description">Pantau perangkat, lihat jadwal praktikum, dan temukan informasi laboratorium dalam satu tempat.</p>

        <div class="login-inventory-preview">
            <div class="preview-heading"><span>Inventaris laboratorium</span><span class="preview-counter">{{ $stats['total_jenis'] }} jenis perangkat</span></div>
            @foreach($previewItems as $item)
                <div class="preview-device">
                    <span class="device-icon"><i class="bi bi-{{ $item['icon'] }}" aria-hidden="true"></i></span>
                    <div class="preview-device-copy"><strong>{{ $item['nama'] }}</strong><span>{{ $item['kode'] }}</span></div>
                    <span class="preview-availability">{{ $item['tersedia'] }}<span>/{{ $item['stok'] }}</span></span>
                </div>
            @endforeach
            <div class="preview-footer"><span><i class="bi bi-check2-circle" aria-hidden="true"></i>{{ $stats['siap_pakai'] }} unit siap digunakan</span><span>dari {{ $stats['total_alat'] }} unit</span></div>
        </div>
        <p class="login-institution"><i class="bi bi-building" aria-hidden="true"></i>Universitas Jember<span>Data inventaris simulasi untuk pembelajaran.</span></p>
    </section>

    <section class="login-form-panel" aria-labelledby="login-title">
        <div class="login-form-heading"><span class="login-symbol"><i class="bi bi-door-open" aria-hidden="true"></i></span><h2 id="login-title">Selamat datang kembali</h2><p>Masuk untuk membuka ruang kerja SIM-LAB.</p></div>
        <form action="{{ route('login.process') }}" method="POST" data-login-form>
            @csrf
            <div class="form-field">
                <label for="usernameInput" class="form-label">Nama pengguna</label>
                <div class="input-with-icon"><i class="bi bi-person" aria-hidden="true"></i>
                    <input type="text" name="username" id="usernameInput" class="form-control @error('username') is-invalid @enderror" value="{{ is_string(old('username', 'naghamal.islam')) ? old('username', 'naghamal.islam') : '' }}" placeholder="Contoh: naghamal.islam" minlength="3" maxlength="30" autocomplete="username" required aria-describedby="usernameHint{{ $errors->has('username') ? ' usernameError' : '' }}" @error('username') aria-invalid="true" @enderror>
                </div>
                <span id="usernameHint" class="field-hint">Gunakan 3 sampai 30 karakter.</span>
                @error('username')<span id="usernameError" class="field-error">{{ $message }}</span>@enderror
            </div>
            <div class="form-field">
                <label for="roleInput" class="form-label">Peran akun</label>
                <select name="role" id="roleInput" class="form-select @error('role') is-invalid @enderror" required @error('role') aria-invalid="true" aria-describedby="roleError" @enderror>
                    @foreach($roles as $availableRole)<option value="{{ $availableRole }}" @selected(old('role', $roles[0]) === $availableRole)>{{ $availableRole }}</option>@endforeach
                </select>
                @error('role')<span id="roleError" class="field-error">{{ $message }}</span>@enderror
            </div>
            <div class="form-field">
                <label for="passwordInput" class="form-label">Kata sandi simulasi <span class="optional-label">Opsional</span></label>
                <div class="password-field">
                    <input type="password" name="password" id="passwordInput" class="form-control @error('password') is-invalid @enderror" placeholder="Minimal 6 karakter jika diisi" minlength="6" maxlength="72" autocomplete="off" aria-describedby="passwordHint{{ $errors->has('password') ? ' passwordError' : '' }}" @error('password') aria-invalid="true" @enderror>
                    <button type="button" class="password-toggle" data-password-toggle aria-controls="passwordInput" aria-label="Tampilkan kata sandi" aria-pressed="false" hidden><i class="bi bi-eye" aria-hidden="true"></i></button>
                </div>
                <span id="passwordHint" class="field-hint">Hanya untuk mencoba validasi form pada demo.</span>
                @error('password')<span id="passwordError" class="field-error">{{ $message }}</span>@enderror
            </div>
            <button type="submit" class="btn btn-primary login-submit" data-login-submit><span>Masuk ke SIM-LAB</span><i class="bi bi-arrow-right" aria-hidden="true"></i></button>
        </form>

        <div class="login-presets" data-presets hidden>
            <p>Atau coba salah satu peran</p>
            <div class="preset-options">
                <button type="button" class="preset-button" data-preset-username="naghamal.islam" data-preset-role="Administrator Lab"><i class="bi bi-sliders2" aria-hidden="true"></i>Admin</button>
                <button type="button" class="preset-button" data-preset-username="asisten.labkom" data-preset-role="Asisten Praktikum"><i class="bi bi-person-workspace" aria-hidden="true"></i>Asisten</button>
                <button type="button" class="preset-button" data-preset-username="dosen.multimedia" data-preset-role="Dosen Pembina Lab"><i class="bi bi-mortarboard" aria-hidden="true"></i>Dosen</button>
            </div>
            <span class="visually-hidden" data-preset-feedback aria-live="polite"></span>
        </div>
        <details class="implementation-note login-demo-note">
            <summary><i class="bi bi-info-circle" aria-hidden="true"></i>Tentang mode demonstrasi</summary>
            <p>Demo UTS Pemrograman Web menggunakan Laravel MVC dan Blade. Nama pengguna serta peran diteruskan melalui request parameter. Kata sandi tidak dicocokkan dengan akun. Seluruh inventaris dan jadwal menggunakan data array simulasi.</p>
        </details>
    </section>
</div>
@endsection
