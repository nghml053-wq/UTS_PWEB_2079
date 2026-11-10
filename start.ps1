[CmdletBinding()]
param (
    [ValidateRange(1024, 65535)]
    [int] $Port = 8000,
    [string] $PhpPath,
    [switch] $Build
)

$ErrorActionPreference = 'Stop'
$candidates = @()

if ($PhpPath) {
    $candidates += $PhpPath
} else {
    $defaultPhp = Get-Command php -ErrorAction SilentlyContinue
    if ($defaultPhp) {
        $candidates += $defaultPhp.Source
    }

    $laragonPhp = Join-Path $env:SystemDrive 'laragon\bin\php'
    if (Test-Path -LiteralPath $laragonPhp) {
        $candidates += Get-ChildItem -Path "$laragonPhp\*\php.exe" -File |
            Sort-Object FullName | Select-Object -ExpandProperty FullName
    }
}

$php = $null
foreach ($candidate in ($candidates | Select-Object -Unique)) {
    if (-not (Test-Path -LiteralPath $candidate -PathType Leaf)) {
        continue
    }

    $version = & $candidate -r 'echo PHP_VERSION_ID;'
    if ($LASTEXITCODE -eq 0 -and "$version" -match '^\d+$' -and [int] $version -ge 80300 -and [int] $version -lt 90000) {
        $php = (Resolve-Path -LiteralPath $candidate).Path
        break
    }
}

if (-not $php) {
    throw 'PHP 8.3+ (8.x) belum ditemukan. Gunakan .\start.ps1 -PhpPath "C:\lokasi\php.exe".'
}

$originalPath = $env:PATH
$env:PATH = (Split-Path -Parent $php) + ';' + $originalPath
Push-Location -LiteralPath $PSScriptRoot

try {
    if (-not (Test-Path -LiteralPath 'vendor\autoload.php')) {
        throw 'Dependensi PHP belum tersedia. Pilih PHP 8.3+ lalu jalankan composer install terlebih dahulu; lihat README.md.'
    }

    if (-not (Test-Path -LiteralPath '.env')) {
        Copy-Item -LiteralPath '.env.example' -Destination '.env'
    }

    & $php artisan config:clear --no-interaction
    if ($LASTEXITCODE -ne 0) {
        throw 'Konfigurasi Laravel belum dapat dimuat.'
    }

    if (-not (Select-String -LiteralPath '.env' -Pattern '^APP_KEY=\S+' -Quiet)) {
        & $php artisan key:generate --no-interaction
        if ($LASTEXITCODE -ne 0) {
            throw 'APP_KEY belum berhasil dibuat.'
        }
    }

    if ($Build -or -not (Test-Path -LiteralPath 'public\build\manifest.json')) {
        if (-not (Test-Path -LiteralPath 'node_modules\vite')) {
            throw 'Dependensi frontend belum tersedia. Jalankan npm.cmd ci lalu ulangi perintah ini.'
        }

        & npm.cmd run build
        if ($LASTEXITCODE -ne 0) {
            throw 'Build frontend gagal. Periksa pesan Vite di atas.'
        }
    }

    Write-Host "PHP: $php"
    Write-Host "Buka SIM-LAB di http://127.0.0.1:$Port"
    & $php artisan serve --host=127.0.0.1 --port=$Port --no-interaction
} finally {
    Pop-Location
    $env:PATH = $originalPath
}
