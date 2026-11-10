@extends('layouts.app')
@section('title', 'Profil Akun')
@section('content')
@php($identity = ['username' => $username, 'role' => $role])
<div class="workspace-context"><span><a href="{{ route('dashboard', $identity) }}">Dashboard</a><i class="bi bi-chevron-right" aria-hidden="true"></i>Profil Akun</span><span>Identitas ruang kerja</span></div>
<header class="page-header"><div><h1>Profil akun</h1><p>Informasi identitas, peran, dan sesi demo yang sedang digunakan.</p></div><a class="btn btn-secondary" href="{{ route('dashboard', $identity) }}"><i class="bi bi-arrow-left" aria-hidden="true"></i>Kembali ke dashboard</a></header>

<div class="profile-layout">
    <aside class="profile-aside">
        <section class="panel profile-card" aria-labelledby="profile-name"><div class="profile-cover" aria-hidden="true"><span></span><span></span><span></span></div><div class="profile-card-content"><span class="avatar avatar-profile">{{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($username, 0, 1)) }}</span><span class="profile-account-label">Akun demonstrasi</span><h2 id="profile-name">{{ $profileData['nama_lengkap'] }}</h2><p class="profile-username">{{ $username }}</p><x-status-badge tone="info">{{ $role }}</x-status-badge><div class="profile-institution"><i class="bi bi-building" aria-hidden="true"></i><div><strong>Universitas Jember</strong><span>Fakultas Ilmu Komputer</span></div></div></div></section>
        <section class="panel role-panel" aria-labelledby="role-title"><div class="panel-heading"><div><h2 id="role-title">Cakupan peran</h2><p>Gambaran tanggung jawab dalam demo.</p></div></div><ul class="role-capabilities">@foreach($profileData['cakupan_peran'] as $capability)<li><i class="bi bi-check2" aria-hidden="true"></i>{{ $capability }}</li>@endforeach</ul></section>
    </aside>
    <div class="profile-main">
        <section class="panel" aria-labelledby="identity-title"><div class="panel-heading"><div><h2 id="identity-title">Informasi pengguna</h2><p>Identitas dari parameter dan profil contoh yang tersedia.</p></div><span class="section-icon"><i class="bi bi-person-vcard" aria-hidden="true"></i></span></div>
            <dl class="profile-details"><div><dt>Nama lengkap</dt><dd>{{ $profileData['nama_lengkap'] }}</dd></div><div><dt>Nama pengguna</dt><dd class="monospace-value">{{ $username }}</dd></div><div><dt>NIM / identitas</dt><dd>{{ $profileData['nomor_identitas'] ?? 'Belum diisi pada demo' }}</dd></div><div><dt>Email institusi</dt><dd>{{ $profileData['email'] ?? 'Belum diisi pada demo' }}</dd></div><div><dt>Peran akun</dt><dd>{{ $role }}</dd></div><div><dt>Institusi</dt><dd>Universitas Jember</dd></div></dl>
        </section>
        <section class="panel" aria-labelledby="session-title"><div class="panel-heading"><div><h2 id="session-title">Informasi sesi</h2><p>Waktu masuk dicatat saat form login berhasil dikirim.</p></div><span class="section-icon"><i class="bi bi-clock-history" aria-hidden="true"></i></span></div>
            <dl class="profile-details"><div><dt>Terakhir masuk</dt><dd data-login-time>{{ $profileData['terakhir_login']?->translatedFormat('d F Y, H:i') ?? 'Akses langsung tanpa form login' }}{{ $profileData['terakhir_login'] ? ' WIB' : '' }}</dd></div><div><dt>Zona waktu</dt><dd>Asia/Jakarta (WIB)</dd></div><div><dt>Mode aplikasi</dt><dd>Demonstrasi UTS</dd></div><div><dt>Sumber identitas</dt><dd>Request parameter (query string)</dd></div></dl>
        </section>
        <section class="panel parameter-panel" aria-labelledby="parameter-title"><div class="panel-heading"><div><h2 id="parameter-title">Alur request parameter</h2><p>Identitas berikut diteruskan saat berpindah halaman.</p></div><span class="section-icon"><i class="bi bi-braces" aria-hidden="true"></i></span></div><div class="parameter-values"><div><code>username</code><strong>{{ $username }}</strong></div><div><code>role</code><strong>{{ $role }}</strong></div></div><div class="parameter-url"><span>URL halaman profil</span><code>{{ route('profile', $identity, false) }}</code></div><p class="parameter-caption">Parameter divalidasi oleh Form Request sebelum diteruskan ke controller dan ditampilkan menggunakan Blade.</p></section>
    </div>
</div>
@endsection
