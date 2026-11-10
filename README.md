# SIM-LAB

Sistem informasi pengelolaan fasilitas laboratorium untuk demonstrasi UTS Pemrograman Web. Dibangun dengan Laravel 13, Blade, Bootstrap 5.3, dan Vite.

## Fitur

- Dashboard dengan statistik inventaris, distribusi ketersediaan, aktivitas contoh, dan agenda laboratorium.
- Inventaris 10 jenis perangkat: **44 unit**, terdiri dari **31 tersedia**, **12 dipinjam**, dan **1 dalam pemeliharaan**.
- Pencarian nama, kode, spesifikasi, atau lokasi; filter kategori dan status; detail perangkat; rekap CSV sesuai filter aktif.
- Jadwal praktikum pekan berjalan, pilihan tanggal/ruangan, dan status berdasarkan waktu WIB.
- Profil sesuai nama pengguna serta peran, dengan waktu masuk yang dicatat ketika form login berhasil dikirim.
- Tampilan responsif, navigasi ponsel, dan tema terang/gelap/otomatis yang tersimpan di browser.

## Mode demonstrasi

Inventaris, jadwal, dan profil contoh berasal dari array di `app/Support/LabData.php`. Nama pengguna dan peran diteruskan sebagai **request parameter** sesuai alur demonstrasi MVC. Login merupakan simulasi: kata sandi opsional hanya divalidasi formatnya, bukan dicocokkan dengan akun. Peran menjelaskan cakupan tugas pada profil.

Nama pengguna harus berupa teks 3–30 karakter. Kata sandi, jika diisi, harus 6–72 karakter. Pilihan peran:

- Administrator Lab
- Asisten Praktikum
- Dosen Pembina Lab
- Mahasiswa Peneliti

Tersedia preset Admin, Asisten, dan Dosen pada halaman login. Nama awal `naghamal.islam` menampilkan profil contoh yang sudah dilengkapi. Nama lainnya tetap dapat digunakan dan tidak diberi NIM/email buatan.

Konfigurasi demo memakai `SESSION_DRIVER=file`, `CACHE_STORE=file`, dan `QUEUE_CONNECTION=sync`. **Database dan migrasi tidak diperlukan untuk menjalankan demo ini.** Jadwal menggunakan tanggal pekan berjalan dalam zona waktu `Asia/Jakarta`; tanggal di luar pekan demo memiliki keadaan kosong.

## Menjalankan di Windows / Laragon

Dari folder proyek yang berisi `artisan` dan `start.ps1`:

```powershell
powershell -ExecutionPolicy Bypass -File .\start.ps1
```

Buka **http://127.0.0.1:8000**. Launcher memilih PHP yang kompatibel dari PATH atau instalasi Laragon. Jika build frontend belum tersedia, launcher menjalankan build menggunakan dependensi npm yang telah terpasang. Hentikan server dengan `Ctrl+C`.

Pilihan port, runtime, atau build ulang:

```powershell
.\start.ps1 -Port 8086
.\start.ps1 -PhpPath 'C:\laragon\bin\php\php-8.3.12-Win32-vs16-x64\php.exe' -Build
```

## Instalasi pertama

Prasyarat: **PHP 8.3+ (8.x)**, Composer, serta Node.js **20.19+ atau 22.12+**. PHP harus memiliki ekstensi Laravel seperti `mbstring`, `openssl`, `dom`, `xml`, `ctype`, `fileinfo`, dan `pdo`. Gunakan `composer check-platform-reqs` untuk memeriksa runtime.

Jika terminal Laragon masih menggunakan PHP 8.1, pilih versi yang kompatibel untuk sesi terminal tersebut:

```powershell
$env:PATH = 'C:\laragon\bin\php\php-8.3.12-Win32-vs16-x64;' + $env:PATH
php -v
```

Instal dependensi lalu siapkan aplikasi:

```powershell
composer install
Copy-Item .env.example .env
php artisan key:generate
npm.cmd ci
npm.cmd run build
php artisan serve --host=127.0.0.1 --port=8000
```

Salin `.env.example` dan buat key hanya pada instalasi baru. Untuk proyek yang sudah memiliki `.env`, gunakan konfigurasi tersebut. Aset Bootstrap, Bootstrap Icons, serta Plus Jakarta Sans disimpan lokal di `public/vendor` dan `public/fonts`; tampilan tidak memerlukan koneksi CDN saat demo dijalankan.

Jika Composer di Laragon tersendat ketika mengekstrak paket dengan `unzip.exe` bawaan Git, pastikan ekstensi PHP `zip` aktif dan keluarkan direktori `unzip.exe` dari PATH sesi terminal agar Composer memakai ekstraksi ZIP PHP.

## Pengembangan dan pemeriksaan

Gunakan PHP yang kompatibel pada PATH sebelum menjalankan perintah berikut:

```powershell
composer run dev
```

Perintah tersebut menjalankan server Laravel dan Vite. Queue worker tidak dijalankan ketika driver antrean adalah `sync`. Untuk presentasi dengan aset hasil build, gunakan `start.ps1` atau `php artisan serve`.

```powershell
php vendor/bin/phpunit
php vendor/bin/pint app/Http/Controllers/PageController.php app/Http/Requests app/Support/LabData.php app/Providers/AppServiceProvider.php routes/web.php tests/Feature --format agent
npm.cmd run build
```

Pengujian mencakup validasi input dan query yang tidak valid, konsistensi stok, filter gabungan, CSV, jadwal/WIB, escaping identitas, waktu sesi, serta logout POST. Feature tests memakai sesi/cache sementara sehingga tidak membutuhkan database.

## Struktur utama

| Lokasi | Tanggung jawab |
| --- | --- |
| `app/Support/LabData.php` | Sumber array inventaris, statistik, jadwal, ruangan, dan profil contoh |
| `app/Http/Requests` | Validasi identitas, filter inventaris, dan pilihan jadwal |
| `app/Http/Controllers/PageController.php` | Alur halaman, sesi demo, serta ekspor CSV |
| `resources/views` | Layout Blade, komponen, dan halaman aplikasi |
| `resources/css/app.css` | Sistem visual, tema, dan layout responsif |
| `resources/js/app.js` | Tema, preset login, pencarian/filter instan, dan sinkronisasi URL |
| `routes/web.php` | Login, dashboard, pengelolaan, ekspor, jadwal, profil, dan logout |

Route utama: `/login`, `/dashboard`, `/pengelolaan`, `/pengelolaan/export`, `/jadwal`, `/profile`, serta `POST /logout`.

Bootstrap dan Bootstrap Icons menggunakan lisensi MIT; Plus Jakarta Sans menggunakan SIL Open Font License. Salinan lisensi tersedia di direktori aset masing-masing.
