<!DOCTYPE html>
<html lang="id" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="SIM-LAB, sistem informasi pengelolaan inventaris dan kegiatan laboratorium Universitas Jember. Demo UTS Pemrograman Web.">
    <meta name="theme-color" content="#2858b8">
    <title>@yield('title', 'Ruang kerja laboratorium') | SIM-LAB</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <script>
        (() => {
            let preference = 'system';
            try { preference = localStorage.getItem('simlab-theme') || 'system'; } catch {}
            if (!['light', 'dark', 'system'].includes(preference)) preference = 'system';
            const dark = preference === 'dark' || (preference === 'system' && matchMedia('(prefers-color-scheme: dark)').matches);
            document.documentElement.dataset.bsTheme = dark ? 'dark' : 'light';
            document.documentElement.dataset.themePreference = preference;
        })();
    </script>
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap-icons/bootstrap-icons.min.css') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="{{ request()->routeIs('login') ? 'login-page' : 'workspace-page' }}">
    <a class="skip-link" href="#main-content">Lewati ke konten utama</a>
    <x-navbar :username="$username ?? null" :role="$role ?? null" />

    <main id="main-content" class="main-content" tabindex="-1">
        <div class="app-container">
            @include('partials.alert')
            @yield('content')
        </div>
    </main>

    <x-footer />
    <script src="{{ asset('vendor/bootstrap/bootstrap.bundle.min.js') }}" defer></script>
    @stack('scripts')
</body>
</html>
