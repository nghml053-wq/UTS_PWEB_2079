@props(['username' => null, 'role' => null])
@php($identity = ['username' => $username, 'role' => $role])

<nav class="navbar navbar-expand-xl app-navbar" aria-label="Navigasi utama">
    <div class="app-container navbar-inner">
        <a class="brand" href="{{ $username ? route('dashboard', $identity) : route('login') }}" aria-label="SIM-LAB, {{ $username ? 'dashboard' : 'halaman masuk' }}">
            <span class="brand-mark"><i class="bi bi-cpu" aria-hidden="true"></i></span>
            <span class="brand-copy"><strong>SIM-LAB<span class="brand-period">.</span></strong><span>Universitas Jember</span></span>
        </a>

        @if($username)
            <div class="collapse navbar-collapse" id="navbarMainContent">
                <ul class="navbar-nav main-navigation">
                    @foreach([
                        ['route' => 'dashboard', 'label' => 'Dashboard', 'icon' => 'grid-1x2'],
                        ['route' => 'pengelolaan', 'label' => 'Pengelolaan Alat', 'icon' => 'boxes'],
                        ['route' => 'jadwal', 'label' => 'Jadwal Praktikum', 'icon' => 'calendar3'],
                        ['route' => 'profile', 'label' => 'Profil Akun', 'icon' => 'person-circle'],
                    ] as $link)
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs($link['route']) ? 'active' : '' }}"
                               href="{{ route($link['route'], $identity) }}"
                               @if(request()->routeIs($link['route'])) aria-current="page" @endif>
                                <i class="bi bi-{{ $link['icon'] }}" aria-hidden="true"></i>{{ $link['label'] }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="navbar-tools">
            <span class="demo-label"><i class="bi bi-window" aria-hidden="true"></i>Mode demo</span>
            <x-theme-menu />
            @if($username)
                <div class="dropdown account-dropdown">
                    <button class="account-button" type="button" data-bs-toggle="dropdown" data-account-menu aria-expanded="false" aria-label="Menu akun {{ $username }}">
                        <span class="avatar avatar-sm">{{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($username, 0, 1)) }}</span>
                        <span class="account-copy"><strong>{{ $username }}</strong><span>{{ $role }}</span></span>
                        <i class="bi bi-chevron-down account-chevron" aria-hidden="true"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li class="dropdown-identity"><strong>{{ $username }}</strong><span>{{ $role }}</span></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="{{ route('profile', $identity) }}"><i class="bi bi-person" aria-hidden="true"></i>Profil Akun</a></li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button class="dropdown-item logout-action" type="submit"><i class="bi bi-box-arrow-right" aria-hidden="true"></i>Keluar</button>
                            </form>
                        </li>
                    </ul>
                </div>
                <button class="navbar-toggler icon-button" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMainContent" data-mobile-navigation aria-controls="navbarMainContent" aria-expanded="false" aria-label="Buka navigasi">
                    <i class="bi bi-list" aria-hidden="true"></i>
                </button>
            @endif
        </div>
    </div>
</nav>
