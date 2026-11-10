<?php

namespace App\Support;

use Carbon\CarbonImmutable;
use Illuminate\Support\Str;

class LabData
{
    public const ROLES = [
        'Administrator Lab',
        'Asisten Praktikum',
        'Dosen Pembina Lab',
        'Mahasiswa Peneliti',
    ];

    public static function canonicalUsername(string $username): string
    {
        return match ($username) {
            'gamal.ramadhan' => 'naghamal.islam',
            default => $username,
        };
    }

    /** @return list<array<string, mixed>> */
    public static function inventory(): array
    {
        $items = [
            [
                'id' => 1,
                'kode' => 'LAB-WRK-001',
                'nama' => 'Workstation AI & Rendering Tower',
                'kategori' => 'Komputasi Tinggi',
                'spesifikasi' => 'Intel Core i9-14900K, 64GB DDR5, NVIDIA RTX 4090 24GB, SSD NVMe 2TB',
                'lokasi' => 'Lab AI & Riset, Meja 01',
                'stok' => 4,
                'tersedia' => 3,
                'pemeliharaan' => 0,
                'kondisi' => 'Optimal',
                'icon' => 'pc-display',
            ],
            [
                'id' => 2,
                'kode' => 'LAB-VR-002',
                'nama' => 'Meta Quest 3 VR Enterprise Kit',
                'kategori' => 'Virtual & Augmented Reality',
                'spesifikasi' => 'Storage 512GB, dual RGB cameras, Elite Strap dan controller charger',
                'lokasi' => 'Lab Multimedia 01, Lemari A',
                'stok' => 6,
                'tersedia' => 2,
                'pemeliharaan' => 0,
                'kondisi' => 'Sangat baik',
                'icon' => 'headset-vr',
            ],
            [
                'id' => 3,
                'kode' => 'LAB-SRV-003',
                'nama' => 'Dell PowerEdge R750 Rack Server',
                'kategori' => 'Infrastruktur Jaringan & Server',
                'spesifikasi' => 'Dual Intel Xeon Silver 4314, 128GB ECC, 8x 1.92TB SAS SSD RAID 10',
                'lokasi' => 'Data Center, Lantai 1',
                'stok' => 2,
                'tersedia' => 2,
                'pemeliharaan' => 0,
                'kondisi' => 'Prima, online 24 jam',
                'icon' => 'hdd-rack',
            ],
            [
                'id' => 4,
                'kode' => 'LAB-NET-004',
                'nama' => 'Cisco Catalyst 2960-X Managed Switch',
                'kategori' => 'Infrastruktur Jaringan & Server',
                'spesifikasi' => '48 port Gigabit Ethernet PoE+ 370W, 4x 1G SFP uplinks, LAN Base Image',
                'lokasi' => 'Lab Jaringan, Rack Lantai 2',
                'stok' => 5,
                'tersedia' => 5,
                'pemeliharaan' => 0,
                'kondisi' => 'Normal',
                'icon' => 'router',
            ],
            [
                'id' => 5,
                'kode' => 'LAB-CAM-005',
                'nama' => 'Sony FX30 Cinema Line 4K Camera',
                'kategori' => 'Multimedia & Audio Visual',
                'spesifikasi' => 'Sensor APS-C 26MP, dual base ISO, lensa Sigma 18-50mm f/2.8',
                'lokasi' => 'Studio Broadcasting, Lantai 3',
                'stok' => 3,
                'tersedia' => 1,
                'pemeliharaan' => 0,
                'kondisi' => 'Sangat terawat',
                'icon' => 'camera-reels',
            ],
            [
                'id' => 6,
                'kode' => 'LAB-PRJ-006',
                'nama' => 'Epson EB-735Fi Laser Ultra Short Throw',
                'kategori' => 'Display & Presentasi',
                'spesifikasi' => '3600 lumens, Full HD, interactive dual pen, touch support, HDMI 2.0',
                'lokasi' => 'Lab Multimedia 01, Area Presentasi',
                'stok' => 2,
                'tersedia' => 2,
                'pemeliharaan' => 0,
                'kondisi' => 'Optimal',
                'icon' => 'projector',
            ],
            [
                'id' => 7,
                'kode' => 'LAB-3DP-007',
                'nama' => 'Bambu Lab X1-Carbon Combo 3D Printer',
                'kategori' => 'Fabrikasi Digital & IoT',
                'spesifikasi' => 'CoreXY 500mm/s, dual auto bed leveling, AMS 4-color multi-material',
                'lokasi' => 'Lab Maker Space & Robotika',
                'stok' => 2,
                'tersedia' => 1,
                'pemeliharaan' => 1,
                'kondisi' => 'Satu unit dalam kalibrasi nozzle',
                'icon' => 'printer',
            ],
            [
                'id' => 8,
                'kode' => 'LAB-IOT-008',
                'nama' => 'NVIDIA Jetson Orin Nano Developer Kit',
                'kategori' => 'Fabrikasi Digital & IoT',
                'spesifikasi' => '8GB LPDDR5, 40 TOPS AI, 1024-core Ampere GPU, M.2 NVMe slot',
                'lokasi' => 'Lab AI & Riset, Research Box 3',
                'stok' => 8,
                'tersedia' => 6,
                'pemeliharaan' => 0,
                'kondisi' => 'Sangat baik',
                'icon' => 'motherboard',
            ],
            [
                'id' => 9,
                'kode' => 'LAB-AUD-009',
                'nama' => 'Rodecaster Pro II Production Console',
                'kategori' => 'Multimedia & Audio Visual',
                'spesifikasi' => '4 combo XLR inputs, Revolution Preamps, dual USB-C, 8 SMART Pads',
                'lokasi' => 'Studio Broadcasting, Desk 1',
                'stok' => 2,
                'tersedia' => 2,
                'pemeliharaan' => 0,
                'kondisi' => 'Prima',
                'icon' => 'soundwave',
            ],
            [
                'id' => 10,
                'kode' => 'LAB-TAB-010',
                'nama' => 'Wacom Cintiq 16 FHD Creative Pen Display',
                'kategori' => 'Display & Presentasi',
                'spesifikasi' => '15.6 inch Full HD, 8192 level pen pressure, Pro Pen 2 tanpa baterai',
                'lokasi' => 'Lab Multimedia 01, Station Desain',
                'stok' => 10,
                'tersedia' => 7,
                'pemeliharaan' => 0,
                'kondisi' => 'Baik',
                'icon' => 'tablet-landscape',
            ],
        ];

        return array_map(function (array $item): array {
            $item['dipinjam'] = $item['stok'] - $item['tersedia'] - $item['pemeliharaan'];
            $item['status_key'] = $item['pemeliharaan'] > 0 ? 'pemeliharaan' : ($item['dipinjam'] > 0 ? 'dipinjam' : 'tersedia');
            [$item['status'], $item['tone']] = match ($item['status_key']) {
                'pemeliharaan' => ['Pemeliharaan', 'warning'],
                'dipinjam' => ['Sebagian dipinjam', 'info'],
                default => ['Tersedia', 'success'],
            };
            $item['search'] = Str::lower(implode(' ', array_intersect_key($item, array_flip([
                'kode', 'nama', 'kategori', 'spesifikasi', 'lokasi',
            ]))));

            return $item;
        }, $items);
    }

    /** @param list<array<string, mixed>> $items
     * @return array{total_alat: int, siap_pakai: int, sedang_dipinjam: int, pemeliharaan: int, total_jenis: int, total_kategori: int, persentase_tersedia: float}
     */
    public static function statistics(array $items): array
    {
        $total = array_sum(array_column($items, 'stok'));
        $available = array_sum(array_column($items, 'tersedia'));

        return [
            'total_alat' => $total,
            'siap_pakai' => $available,
            'sedang_dipinjam' => array_sum(array_column($items, 'dipinjam')),
            'pemeliharaan' => array_sum(array_column($items, 'pemeliharaan')),
            'total_jenis' => count($items),
            'total_kategori' => count(array_unique(array_column($items, 'kategori'))),
            'persentase_tersedia' => $total > 0 ? round($available / $total * 100, 1) : 0.0,
        ];
    }

    /** @return list<string> */
    public static function categories(): array
    {
        return array_values(array_unique(array_column(self::inventory(), 'kategori')));
    }

    /** @param list<array<string, mixed>> $items
     * @param  array{q: string, kategori: string, status: string}  $filters
     * @return list<array<string, mixed>>
     */
    public static function filterInventory(array $items, array $filters): array
    {
        return array_values(array_filter($items, fn (array $item): bool => ($filters['q'] === '' || str_contains($item['search'], Str::lower($filters['q'])))
            && ($filters['kategori'] === '' || $item['kategori'] === $filters['kategori'])
            && ($filters['status'] === '' || $item['status_key'] === $filters['status'])
        ));
    }

    /** @return list<array{id: string, nama: string, kapasitas: int, fasilitas: string, icon: string}> */
    public static function rooms(): array
    {
        return [
            ['id' => 'multimedia', 'nama' => 'Lab Multimedia & Grafis 01', 'kapasitas' => 32, 'fasilitas' => 'Workstation desain & presentasi', 'icon' => 'display'],
            ['id' => 'jaringan', 'nama' => 'Lab Jaringan & Server', 'kapasitas' => 24, 'fasilitas' => 'Jaringan, server & komputasi', 'icon' => 'diagram-3'],
            ['id' => 'studio', 'nama' => 'Studio Broadcasting & Audio', 'kapasitas' => 12, 'fasilitas' => 'Podcast, video & green screen', 'icon' => 'mic'],
            ['id' => 'maker', 'nama' => 'Lab AI & Robotic Maker Space', 'kapasitas' => 16, 'fasilitas' => 'Robotika, IoT & fabrikasi digital', 'icon' => 'cpu'],
        ];
    }

    /** Jadwal contoh untuk minggu berjalan, seluruh waktu menggunakan WIB.
     * @return list<array<string, mixed>>
     */
    public static function schedules(): array
    {
        $week = CarbonImmutable::now('Asia/Jakarta')->startOfWeek();
        $now = CarbonImmutable::now('Asia/Jakarta');
        $rooms = array_column(self::rooms(), null, 'id');
        $sessions = [
            [0, '08:00', '10:00', 'Pemrograman Web', 'multimedia', 'Muhammad Naghamal Islam', 30, 'Praktikum'],
            [0, '10:00', '12:00', 'Dasar Jaringan Komputer', 'jaringan', 'Rendy Pratama', 24, 'Praktikum'],
            [0, '13:00', '15:00', 'Riset Model Deep Learning', 'maker', 'Dr. Aris', 12, 'Riset'],
            [1, '08:00', '10:00', 'Desain Interaksi & Antarmuka', 'multimedia', 'Muhammad Naghamal Islam', 30, 'Praktikum'],
            [1, '09:00', '11:00', 'Administrasi Jaringan', 'jaringan', 'Rendy Pratama', 24, 'Praktikum'],
            [1, '10:30', '12:30', 'Videografi & Broadcasting', 'studio', 'Muhammad Fikri', 12, 'Praktikum'],
            [1, '13:00', '15:00', 'Robotika & Internet of Things', 'maker', 'Dr. Aris', 16, 'Praktikum'],
            [1, '15:30', '17:00', 'Pemrograman Web', 'multimedia', 'Muhammad Naghamal Islam', 28, 'Praktikum'],
            [2, '08:00', '10:00', 'Pemodelan & Animasi 3D', 'multimedia', 'Siti Nurhaliza', 28, 'Praktikum'],
            [2, '10:00', '12:00', 'Produksi Audio Digital', 'studio', 'Muhammad Fikri', 10, 'Praktikum'],
            [2, '13:00', '15:00', 'Keamanan Infrastruktur', 'jaringan', 'Rendy Pratama', 24, 'Praktikum'],
            [3, '08:00', '10:00', 'Computer Vision', 'maker', 'Dr. Aris', 16, 'Riset'],
            [3, '10:00', '12:00', 'Virtual Reality & Game Dev', 'multimedia', 'Siti Nurhaliza', 24, 'Praktikum'],
            [3, '13:00', '15:00', 'Produksi Konten Multimedia', 'studio', 'Muhammad Fikri', 12, 'Praktikum'],
            [4, '08:00', '10:00', 'Pemrograman Web', 'multimedia', 'Muhammad Naghamal Islam', 30, 'Praktikum'],
            [4, '09:00', '11:00', 'Jaringan Komputer Lanjut', 'jaringan', 'Rendy Pratama', 24, 'Praktikum'],
            [4, '13:00', '15:00', 'Prototipe & Pencetakan 3D', 'maker', 'Dr. Aris', 14, 'Riset'],
        ];

        return array_map(function (array $session) use ($week, $now, $rooms): array {
            [$day, $start, $end, $title, $room, $person, $participants, $type] = $session;
            $date = $week->addDays($day);
            $startsAt = $date->setTimeFromTimeString($start);
            $endsAt = $date->setTimeFromTimeString($end);
            [$status, $tone] = $now->greaterThanOrEqualTo($endsAt)
                ? ['Selesai', 'neutral']
                : ($now->greaterThanOrEqualTo($startsAt) ? ['Berlangsung', 'success'] : ['Terjadwal', 'info']);

            return [
                'tanggal' => $date->toDateString(),
                'mulai' => $start,
                'selesai' => $end,
                'nama' => $title,
                'room_id' => $room,
                'ruangan' => $rooms[$room]['nama'],
                'penanggung_jawab' => $person,
                'peserta' => $participants,
                'jenis' => $type,
                'status' => $status,
                'tone' => $tone,
            ];
        }, $sessions);
    }

    /** @return list<array<string, string>> */
    public static function activities(): array
    {
        $items = array_column(self::inventory(), null, 'kode');

        return [
            ['waktu' => '10:20', 'user' => 'Muhammad Fikri', 'aksi' => 'Peminjaman perangkat', 'item' => $items['LAB-CAM-005']['nama'], 'kode' => 'LAB-CAM-005', 'status' => 'Disetujui', 'tone' => 'success', 'icon' => 'arrow-up-right'],
            ['waktu' => '09:45', 'user' => 'Siti Nurhaliza', 'aksi' => 'Pengembalian perangkat', 'item' => $items['LAB-VR-002']['nama'], 'kode' => 'LAB-VR-002', 'status' => 'Selesai', 'tone' => 'neutral', 'icon' => 'arrow-down-left'],
            ['waktu' => '09:00', 'user' => 'Rendy Pratama', 'aksi' => 'Kalibrasi & pemeliharaan', 'item' => $items['LAB-3DP-007']['nama'], 'kode' => 'LAB-3DP-007', 'status' => 'Dikerjakan', 'tone' => 'warning', 'icon' => 'tools'],
            ['waktu' => '08:15', 'user' => 'Dr. Aris', 'aksi' => 'Reservasi perangkat riset', 'item' => $items['LAB-WRK-001']['nama'], 'kode' => 'LAB-WRK-001', 'status' => 'Disetujui', 'tone' => 'success', 'icon' => 'calendar-check'],
        ];
    }

    /** @return array{nama_lengkap: string, nomor_identitas: ?string, email: ?string} */
    public static function profile(string $username): array
    {
        $username = self::canonicalUsername($username);

        $profiles = [
            'naghamal.islam' => ['nama_lengkap' => 'Muhammad Naghamal Islam', 'nomor_identitas' => '232410101001', 'email' => 'naghamal.islam@student.unej.ac.id'],
            'asisten.labkom' => ['nama_lengkap' => 'Asisten Laboratorium', 'nomor_identitas' => null, 'email' => null],
            'dosen.multimedia' => ['nama_lengkap' => 'Dosen Multimedia', 'nomor_identitas' => null, 'email' => null],
        ];

        return $profiles[$username] ?? [
            'nama_lengkap' => $username,
            'nomor_identitas' => null,
            'email' => null,
        ];
    }
}
