@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')
@php($identity = ['username' => $username, 'role' => $role])
<div class="workspace-context"><span><i class="bi bi-building" aria-hidden="true"></i>Laboratorium Fakultas Ilmu Komputer</span><time datetime="{{ now('Asia/Jakarta')->toDateString() }}">{{ now('Asia/Jakarta')->translatedFormat('l, d F Y') }}</time></div>
<header class="page-header">
    <div><h1>Ringkasan laboratorium</h1><p>Selamat datang, <strong>{{ $username }}</strong>. Berikut gambaran fasilitas dan kegiatan lab.</p></div>
    <a href="{{ route('pengelolaan', $identity) }}" class="btn btn-primary"><i class="bi bi-boxes" aria-hidden="true"></i>Buka inventaris<i class="bi bi-arrow-up-right" aria-hidden="true"></i></a>
</header>

<section class="metrics-grid" aria-label="Statistik inventaris">
    <x-stat-card title="Total perangkat" :value="$stats['total_alat']" icon="boxes" stat="total_alat" :caption="$stats['total_jenis'].' jenis dalam '.$stats['total_kategori'].' kategori'" :emphasis="true" />
    <x-stat-card title="Siap digunakan" :value="$stats['siap_pakai']" icon="check2-circle" stat="siap_pakai" caption="Tersedia untuk kegiatan lab" />
    <x-stat-card title="Sedang dipinjam" :value="$stats['sedang_dipinjam']" icon="arrow-left-right" stat="sedang_dipinjam" caption="Dalam penggunaan" />
    <x-stat-card title="Pemeliharaan" :value="$stats['pemeliharaan']" icon="tools" stat="pemeliharaan" caption="Perlu penanganan teknisi" />
</section>

<div class="dashboard-grid">
    <div class="dashboard-main">
        <section class="panel availability-panel" aria-labelledby="availability-title">
            <div class="panel-heading"><div><h2 id="availability-title">Ketersediaan perangkat</h2><p>Distribusi unit dari seluruh inventaris laboratorium.</p></div><span class="section-meta">{{ $stats['total_alat'] }} unit</span></div>
            <div class="availability-layout">
                <div class="availability-chart">
                    <div class="availability-ring" style="--available: {{ $stats['siap_pakai'] / $stats['total_alat'] * 100 }}%; --borrowed: {{ ($stats['siap_pakai'] + $stats['sedang_dipinjam']) / $stats['total_alat'] * 100 }}%;" role="img" aria-label="{{ $stats['siap_pakai'] }} unit tersedia, {{ $stats['sedang_dipinjam'] }} dipinjam, {{ $stats['pemeliharaan'] }} dalam pemeliharaan">
                        <div><strong>{{ number_format($stats['persentase_tersedia'], 1, ',', '.') }}<span>%</span></strong><span>siap digunakan</span></div>
                    </div>
                    <div class="chart-legend"><span><i class="legend-swatch available-swatch" aria-hidden="true"></i>Tersedia <strong>{{ $stats['siap_pakai'] }}</strong></span><span><i class="legend-swatch borrowed-swatch" aria-hidden="true"></i>Dipinjam <strong>{{ $stats['sedang_dipinjam'] }}</strong></span><span><i class="legend-swatch maintenance-swatch" aria-hidden="true"></i>Pemeliharaan <strong>{{ $stats['pemeliharaan'] }}</strong></span></div>
                </div>
                <div class="category-summary">
                    <div class="category-summary-heading"><span>Kategori perangkat</span><span>Tersedia / total</span></div>
                    @foreach($categorySummary as $category)
                        <a class="category-summary-row" href="{{ route('pengelolaan', [...$identity, 'kategori' => $category['nama']]) }}">
                            <span class="category-summary-name"><i class="bi bi-{{ $category['icon'] }}" aria-hidden="true"></i>{{ $category['nama'] }}</span>
                            <span class="category-summary-count"><strong>{{ $category['tersedia'] }}</strong><span>/ {{ $category['unit'] }}</span><i class="bi bi-chevron-right" aria-hidden="true"></i></span>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="panel activity-panel" aria-labelledby="activity-title">
            <div class="panel-heading"><div><h2 id="activity-title">Aktivitas operasional</h2><p>Contoh alur peminjaman, pengembalian, dan perawatan.</p></div><span class="section-meta">Data simulasi</span></div>
            <div class="activity-list">
                @foreach($recentActivities as $activity)
                    <article class="activity-row">
                        <span class="activity-icon"><i class="bi bi-{{ $activity['icon'] }}" aria-hidden="true"></i></span>
                        <div class="activity-copy"><div class="activity-title"><h3>{{ $activity['aksi'] }}</h3><time>{{ $activity['waktu'] }} WIB</time></div><p class="activity-device" title="{{ $activity['item'] }}">{{ $activity['item'] }}</p><span class="activity-person">{{ $activity['user'] }}<span aria-hidden="true">·</span>{{ $activity['kode'] }}</span></div>
                        <x-status-badge :tone="$activity['tone']">{{ $activity['status'] }}</x-status-badge>
                    </article>
                @endforeach
            </div>
        </section>
    </div>

    <aside class="dashboard-aside" aria-label="Jadwal dan ruangan">
        <section class="panel schedule-preview" aria-labelledby="today-title">
            <div class="panel-heading"><div><h2 id="today-title">Agenda hari ini</h2><p>{{ count($todaySchedules) }} sesi terjadwal</p></div><span class="section-icon"><i class="bi bi-calendar3" aria-hidden="true"></i></span></div>
            <div class="agenda-list">
                @forelse(array_slice($todaySchedules, 0, 3) as $session)
                    <article class="agenda-item"><div class="agenda-time"><strong>{{ $session['mulai'] }}</strong><span>{{ $session['selesai'] }}</span></div><div><h3>{{ $session['nama'] }}</h3><p>{{ $session['ruangan'] }}</p><x-status-badge :tone="$session['tone']">{{ $session['status'] }}</x-status-badge></div></article>
                @empty
                    <div class="compact-empty"><i class="bi bi-calendar2-check" aria-hidden="true"></i><p>Belum ada kegiatan hari ini.</p><span>Lihat agenda pada hari lainnya.</span></div>
                @endforelse
            </div>
            <a class="panel-footer-link" href="{{ route('jadwal', $identity) }}">Lihat jadwal praktikum<i class="bi bi-arrow-right" aria-hidden="true"></i></a>
        </section>

        <section class="panel rooms-panel" aria-labelledby="rooms-title">
            <div class="panel-heading"><div><h2 id="rooms-title">Ruang laboratorium</h2><p>Status berdasarkan jadwal demo.</p></div><span class="count-label">{{ count($activeRooms) }}</span></div>
            <div class="room-list">
                @foreach($activeRooms as $room)
                    <a class="room-item" href="{{ route('jadwal', [...$identity, 'ruangan' => $room['id']]) }}"><span class="room-icon"><i class="bi bi-{{ $room['icon'] }}" aria-hidden="true"></i></span><div><h3>{{ $room['nama'] }}</h3><span class="room-capacity">Kapasitas {{ $room['kapasitas'] }} orang</span><x-status-badge :tone="$room['tone']">{{ $room['status'] }}</x-status-badge></div><i class="bi bi-chevron-right room-arrow" aria-hidden="true"></i></a>
                @endforeach
            </div>
        </section>
    </aside>
</div>
@endsection
