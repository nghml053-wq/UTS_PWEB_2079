@extends('layouts.app')
@section('title', 'Pengelolaan Alat')
@section('content')
@php($identity = ['username' => $username, 'role' => $role])
<div class="workspace-context"><span><a href="{{ route('dashboard', $identity) }}">Dashboard</a><i class="bi bi-chevron-right" aria-hidden="true"></i>Pengelolaan Alat</span><span>Inventaris laboratorium</span></div>
<header class="page-header">
    <div><h1>Pengelolaan alat</h1><p>Temukan perangkat, periksa spesifikasi, dan pantau ketersediaan setiap unit.</p></div>
    <a class="btn btn-primary" href="{{ route('pengelolaan.export', [...$identity, ...$filters]) }}" data-export-inventory data-export-url="{{ route('pengelolaan.export', $identity) }}"><i class="bi bi-download" aria-hidden="true"></i>Unduh rekap CSV</a>
</header>

<section class="inventory-overview" aria-label="Ringkasan inventaris">
    <div><span class="overview-icon"><i class="bi bi-boxes" aria-hidden="true"></i></span><p><strong>{{ $stats['total_jenis'] }}</strong><span>Jenis perangkat</span></p></div>
    <div><span class="overview-icon"><i class="bi bi-stack" aria-hidden="true"></i></span><p><strong>{{ $stats['total_alat'] }}</strong><span>Total unit</span></p></div>
    <div><span class="overview-icon"><i class="bi bi-check2-circle" aria-hidden="true"></i></span><p><strong>{{ $stats['siap_pakai'] }}</strong><span>Unit tersedia</span></p></div>
    <div><span class="overview-icon"><i class="bi bi-collection" aria-hidden="true"></i></span><p><strong>{{ $stats['total_kategori'] }}</strong><span>Kategori</span></p></div>
</section>

<section class="panel inventory-panel" aria-labelledby="inventory-title">
    <div class="panel-heading"><div><h2 id="inventory-title">Daftar perangkat <span class="count-label" data-result-count>{{ $filteredStats['total_jenis'] }}</span></h2><p>Data inventaris simulasi, disajikan langsung dari satu sumber array.</p></div><span class="section-meta"><i class="bi bi-database" aria-hidden="true"></i>Data demo</span></div>
    <form action="{{ route('pengelolaan') }}" method="GET" class="inventory-toolbar" data-inventory-form>
        <input type="hidden" name="username" value="{{ $username }}"><input type="hidden" name="role" value="{{ $role }}">
        <div class="inventory-search"><label for="inventorySearch" class="visually-hidden">Cari perangkat</label><i class="bi bi-search" aria-hidden="true"></i><input type="search" name="q" id="inventorySearch" class="form-control" placeholder="Cari nama, kode, atau spesifikasi..." value="{{ $filters['q'] }}" maxlength="100" autocomplete="off"></div>
        <div class="category-filter"><label for="inventoryCategory" class="visually-hidden">Kategori perangkat</label><select id="inventoryCategory" name="kategori" class="form-select"><option value="">Semua kategori</option>@foreach($categories as $category)<option value="{{ $category }}" @selected($filters['kategori'] === $category)>{{ $category }}</option>@endforeach</select></div>
        <div class="status-filter"><label for="inventoryStatus" class="visually-hidden">Status perangkat</label><select id="inventoryStatus" name="status" class="form-select"><option value="">Semua status</option><option value="tersedia" @selected($filters['status'] === 'tersedia')>Tersedia penuh</option><option value="dipinjam" @selected($filters['status'] === 'dipinjam')>Sebagian dipinjam</option><option value="pemeliharaan" @selected($filters['status'] === 'pemeliharaan')>Pemeliharaan</option></select></div>
        <button type="submit" class="btn btn-secondary filter-submit" data-filter-submit><i class="bi bi-funnel" aria-hidden="true"></i>Terapkan</button>
        <a class="icon-button reset-filter" href="{{ route('pengelolaan', $identity) }}" data-reset-inventory aria-label="Reset pencarian dan filter" title="Reset filter"><i class="bi bi-arrow-counterclockwise" aria-hidden="true"></i></a>
    </form>
    <p class="table-scroll-hint"><i class="bi bi-arrow-left-right" aria-hidden="true"></i>Geser tabel untuk melihat status dan detail.</p>
    <div class="table-responsive inventory-table-wrap" tabindex="0" role="region" aria-label="Tabel inventaris perangkat, dapat digeser pada layar kecil">
        <table class="table inventory-table" id="inventarisTable">
            <caption class="visually-hidden">Daftar perangkat, kategori, lokasi, ketersediaan, dan status laboratorium.</caption>
            <thead><tr><th scope="col">Perangkat</th><th scope="col">Kategori & lokasi</th><th scope="col">Ketersediaan</th><th scope="col">Status</th><th scope="col" class="text-end"><span class="visually-hidden">Aksi</span></th></tr></thead>
            <tbody>
                @foreach($items as $item)
                    <tr data-inventory-row data-category="{{ $item['kategori'] }}" data-status="{{ $item['status_key'] }}" data-search="{{ $item['search'] }}" data-stock="{{ $item['stok'] }}" data-available="{{ $item['tersedia'] }}" @if(!in_array($item['id'], $visibleIds, true)) hidden @endif>
                        <th scope="row"><div class="inventory-device"><span class="device-icon"><i class="bi bi-{{ $item['icon'] }}" aria-hidden="true"></i></span><div><span class="inventory-code">{{ $item['kode'] }}</span><strong>{{ $item['nama'] }}</strong><span class="inventory-spec" title="{{ $item['spesifikasi'] }}">{{ $item['spesifikasi'] }}</span></div></div></th>
                        <td><span class="table-category">{{ $item['kategori'] }}</span><span class="table-location"><i class="bi bi-geo-alt" aria-hidden="true"></i>{{ $item['lokasi'] }}</span></td>
                        <td><span class="stock-count"><strong>{{ $item['tersedia'] }}</strong><span>/ {{ $item['stok'] }} unit</span></span><meter class="stock-meter" min="0" max="{{ $item['stok'] }}" value="{{ $item['tersedia'] }}" aria-label="{{ $item['tersedia'] }} dari {{ $item['stok'] }} unit tersedia">{{ $item['tersedia'] }}/{{ $item['stok'] }}</meter></td>
                        <td><x-status-badge :tone="$item['tone']">{{ $item['status'] }}</x-status-badge></td>
                        <td class="text-end"><button type="button" class="icon-button detail-button" data-bs-toggle="modal" data-bs-target="#detailModal{{ $item['id'] }}" aria-label="Lihat detail {{ $item['nama'] }}" title="Lihat detail"><i class="bi bi-arrow-up-right" aria-hidden="true"></i></button></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="empty-state" data-inventory-empty @if(count($visibleIds) > 0) hidden @endif><span class="empty-state-icon"><i class="bi bi-search" aria-hidden="true"></i></span><h3>Perangkat tidak ditemukan</h3><p>Coba kata kunci lain atau sesuaikan filter yang dipilih.</p><a class="btn btn-secondary" href="{{ route('pengelolaan', $identity) }}" data-reset-inventory>Reset pencarian</a></div>
    <div class="inventory-table-footer" aria-live="polite" aria-atomic="true"><span>Menampilkan <strong data-result-count>{{ $filteredStats['total_jenis'] }}</strong> dari {{ count($items) }} perangkat</span><span><strong data-result-stock>{{ $filteredStats['total_alat'] }}</strong> unit dalam hasil<span class="footer-divider" aria-hidden="true">·</span><strong data-result-available>{{ $filteredStats['siap_pakai'] }}</strong> tersedia</span></div>
</section>
<div class="page-note"><i class="bi bi-info-circle" aria-hidden="true"></i><p>Rekap CSV mengikuti pencarian dan filter aktif. Buka detail untuk melihat spesifikasi serta pembagian unit perangkat.</p></div>
<details class="implementation-note"><summary><i class="bi bi-code-square" aria-hidden="true"></i>Alur data demo</summary><p>Data array dari <code>LabData</code> diteruskan melalui <code>PageController</code> dan dirender menggunakan direktif <code>@@foreach</code>. Ringkasan dashboard, tabel, dan rekap CSV menggunakan sumber data yang sama.</p></details>

@foreach($items as $item)
    <div class="modal fade" id="detailModal{{ $item['id'] }}" tabindex="-1" aria-labelledby="detailModalLabel{{ $item['id'] }}" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg"><div class="modal-content device-modal">
            <div class="modal-header"><div><span class="inventory-code">{{ $item['kode'] }}</span><h2 class="modal-title" id="detailModalLabel{{ $item['id'] }}">Detail perangkat</h2></div><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup detail perangkat"></button></div>
            <div class="modal-body"><div class="device-detail-heading"><span class="device-icon device-icon-lg"><i class="bi bi-{{ $item['icon'] }}" aria-hidden="true"></i></span><div><h3>{{ $item['nama'] }}</h3><span>{{ $item['kategori'] }}</span></div></div>
                <div class="device-stock-grid"><div><strong>{{ $item['stok'] }}</strong><span>Total unit</span></div><div><strong>{{ $item['tersedia'] }}</strong><span>Tersedia</span></div><div><strong>{{ $item['dipinjam'] }}</strong><span>Dipinjam</span></div><div><strong>{{ $item['pemeliharaan'] }}</strong><span>Pemeliharaan</span></div></div>
                <dl class="device-details"><div><dt>Spesifikasi teknis</dt><dd>{{ $item['spesifikasi'] }}</dd></div><div><dt>Lokasi penyimpanan</dt><dd>{{ $item['lokasi'] }}</dd></div><div><dt>Kondisi perangkat</dt><dd>{{ $item['kondisi'] }}</dd></div><div><dt>Status</dt><dd><x-status-badge :tone="$item['tone']">{{ $item['status'] }}</x-status-badge></dd></div></dl>
            </div><div class="modal-footer"><span>Informasi inventaris simulasi</span><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button></div>
        </div></div>
    </div>
@endforeach
@endsection
