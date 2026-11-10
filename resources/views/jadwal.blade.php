@extends('layouts.app')
@section('title', 'Jadwal Praktikum')
@section('content')
@php($identity = ['username' => $username, 'role' => $role])
<div class="workspace-context"><span><a href="{{ route('dashboard', $identity) }}">Dashboard</a><i class="bi bi-chevron-right" aria-hidden="true"></i>Jadwal Praktikum</span><span>Zona waktu WIB</span></div>
<header class="page-header"><div><h1>Jadwal praktikum</h1><p>Rencanakan kegiatan dengan melihat agenda dan penggunaan ruang laboratorium.</p></div><a class="btn btn-secondary" href="{{ route('jadwal', $identity) }}"><i class="bi bi-calendar-event" aria-hidden="true"></i>Kembali ke hari ini</a></header>

<section class="panel schedule-panel" aria-labelledby="schedule-title">
    <div class="panel-heading"><div><h2 id="schedule-title">Agenda laboratorium</h2><p>Jadwal simulasi pekan {{ $scheduleWeek->translatedFormat('d M') }} sampai {{ $scheduleWeek->addDays(6)->translatedFormat('d M Y') }}.</p></div><span class="section-icon"><i class="bi bi-calendar3-week" aria-hidden="true"></i></span></div>
    <form action="{{ route('jadwal') }}" method="GET" class="schedule-toolbar" data-schedule-form>
        <input type="hidden" name="username" value="{{ $username }}"><input type="hidden" name="role" value="{{ $role }}">
        <div><label for="scheduleDate" class="form-label">Tanggal kegiatan</label><input id="scheduleDate" name="tanggal" type="date" class="form-control" value="{{ $selectedDate->toDateString() }}" required></div>
        <div><label for="scheduleRoom" class="form-label">Ruang laboratorium</label><select id="scheduleRoom" name="ruangan" class="form-select"><option value="">Semua ruangan</option>@foreach($rooms as $room)<option value="{{ $room['id'] }}" @selected($selectedRoom === $room['id'])>{{ $room['nama'] }}</option>@endforeach</select></div>
        <button type="submit" class="btn btn-primary"><i class="bi bi-funnel" aria-hidden="true"></i>Tampilkan</button>
    </form>
    <nav class="week-navigation" aria-label="Pilih hari kegiatan">
        @foreach($weekDates as $day)
            <a class="week-day {{ $day['date']->isSameDay($selectedDate) ? 'is-selected' : '' }} {{ $day['date']->isToday() ? 'is-today' : '' }}" href="{{ route('jadwal', [...$identity, 'tanggal' => $day['date']->toDateString(), 'ruangan' => $selectedRoom]) }}" @if($day['date']->isSameDay($selectedDate)) aria-current="date" @endif aria-label="{{ $day['date']->translatedFormat('l, d F Y') }}, {{ $day['count'] }} sesi"><span>{{ $day['date']->translatedFormat('D') }}</span><strong>{{ $day['date']->format('d') }}</strong><span>{{ $day['count'] }} sesi</span></a>
        @endforeach
    </nav>
    <div class="schedule-date-heading"><h3>{{ $selectedDate->translatedFormat('l, d F Y') }}</h3><span>{{ count($sessions) }} sesi<span class="footer-divider" aria-hidden="true">·</span>{{ count(array_unique(array_column($sessions, 'room_id'))) }} ruangan</span></div>
    <div class="schedule-list">
        @forelse($sessions as $session)
            <article class="schedule-entry" data-schedule-entry data-room="{{ $session['room_id'] }}"><div class="schedule-time"><strong>{{ $session['mulai'] }}</strong><span>{{ $session['selesai'] }} WIB</span></div><div class="schedule-entry-body"><div class="schedule-entry-heading"><span class="session-type">{{ $session['jenis'] }}</span><x-status-badge :tone="$session['tone']">{{ $session['status'] }}</x-status-badge></div><h4>{{ $session['nama'] }}</h4><div class="schedule-entry-meta"><span><i class="bi bi-geo-alt" aria-hidden="true"></i>{{ $session['ruangan'] }}</span><span><i class="bi bi-person" aria-hidden="true"></i>{{ $session['penanggung_jawab'] }}</span><span><i class="bi bi-people" aria-hidden="true"></i>{{ $session['peserta'] }} peserta</span></div></div></article>
        @empty
            <div class="empty-state"><span class="empty-state-icon"><i class="bi bi-calendar2-check" aria-hidden="true"></i></span><h3>Belum ada kegiatan terjadwal</h3><p>Tidak ada sesi untuk tanggal dan ruangan ini. Pilih hari lain pada pekan demo atau tampilkan semua ruangan.</p><a class="btn btn-secondary" href="{{ route('jadwal', [...$identity, 'tanggal' => $scheduleWeek->toDateString()]) }}">Lihat agenda pekan demo</a></div>
        @endforelse
    </div>
</section>
<div class="page-note"><i class="bi bi-info-circle" aria-hidden="true"></i><p>Jadwal ini merupakan data simulasi untuk pekan berjalan. Status sesi dihitung berdasarkan tanggal dan waktu WIB.</p></div>
@endsection
