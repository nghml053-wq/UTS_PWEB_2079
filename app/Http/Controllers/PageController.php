<?php

namespace App\Http\Controllers;

use App\Http\Requests\DemoIdentityRequest;
use App\Http\Requests\InventoryRequest;
use App\Http\Requests\ScheduleRequest;
use App\Support\LabData;
use Carbon\CarbonImmutable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PageController extends Controller
{
    public function login(): View
    {
        $items = LabData::inventory();

        return view('login', [
            'roles' => LabData::ROLES,
            'stats' => LabData::statistics($items),
            'previewItems' => array_slice($items, 0, 3),
        ]);
    }

    public function loginProcess(DemoIdentityRequest $request): RedirectResponse
    {
        $identity = $request->identity();
        $request->session()->regenerate();
        $request->session()->put('demo_session', [
            ...$identity,
            'started_at' => now('Asia/Jakarta')->toIso8601String(),
        ]);

        return to_route('dashboard', $identity)
            ->with('success', 'Selamat datang, '.$identity['username'].'. Ruang kerja demo siap digunakan.');
    }

    public function dashboard(DemoIdentityRequest $request): View
    {
        $items = LabData::inventory();
        $today = now('Asia/Jakarta')->toDateString();
        $todaySchedules = array_values(array_filter(LabData::schedules(), fn (array $session): bool => $session['tanggal'] === $today));
        $activeRooms = array_map(function (array $room) use ($todaySchedules): array {
            $sessions = array_filter($todaySchedules, fn (array $session): bool => $session['room_id'] === $room['id']);
            $statuses = array_column($sessions, 'status');
            [$status, $tone] = in_array('Berlangsung', $statuses, true)
                ? ['Sedang digunakan', 'info']
                : (in_array('Terjadwal', $statuses, true) ? ['Ada jadwal', 'neutral'] : ['Tersedia', 'success']);

            return [...$room, 'status' => $status, 'tone' => $tone];
        }, LabData::rooms());

        $categorySummary = collect($items)->groupBy('kategori')->map(fn ($group, string $name): array => [
            'nama' => $name,
            'unit' => $group->sum('stok'),
            'tersedia' => $group->sum('tersedia'),
            'icon' => $group->first()['icon'],
        ])->values()->all();

        return view('dashboard', [
            ...$request->identity(),
            'stats' => LabData::statistics($items),
            'recentActivities' => LabData::activities(),
            'activeRooms' => $activeRooms,
            'categorySummary' => $categorySummary,
            'todaySchedules' => $todaySchedules,
        ]);
    }

    public function pengelolaan(InventoryRequest $request): View
    {
        $items = LabData::inventory();
        $filters = $request->filters();
        $filteredItems = LabData::filterInventory($items, $filters);

        return view('pengelolaan', [
            ...$request->identity(),
            'items' => $items,
            'categories' => LabData::categories(),
            'stats' => LabData::statistics($items),
            'filteredStats' => LabData::statistics($filteredItems),
            'visibleIds' => array_column($filteredItems, 'id'),
            'filters' => $filters,
        ]);
    }

    public function exportInventory(InventoryRequest $request): StreamedResponse
    {
        $items = LabData::filterInventory(LabData::inventory(), $request->filters());

        return response()->streamDownload(function () use ($items): void {
            $output = fopen('php://output', 'w');
            fwrite($output, "\xEF\xBB\xBF");
            fputcsv($output, ['Kode', 'Perangkat', 'Kategori', 'Lokasi', 'Total unit', 'Tersedia', 'Dipinjam', 'Pemeliharaan', 'Kondisi'], ',', '"', '');

            foreach ($items as $item) {
                fputcsv($output, [
                    $item['kode'], $item['nama'], $item['kategori'], $item['lokasi'],
                    $item['stok'], $item['tersedia'], $item['dipinjam'], $item['pemeliharaan'], $item['kondisi'],
                ], ',', '"', '');
            }

            fclose($output);
        }, 'sim-lab-inventaris-'.now('Asia/Jakarta')->format('Y-m-d').'.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Cache-Control' => 'no-store',
        ]);
    }

    public function jadwal(ScheduleRequest $request): View
    {
        $selectedDate = CarbonImmutable::parse($request->validated('tanggal') ?? now('Asia/Jakarta')->toDateString(), 'Asia/Jakarta')->startOfDay();
        $selectedRoom = $request->validated('ruangan') ?? '';
        $schedules = LabData::schedules();
        $roomSchedules = array_values(array_filter($schedules, fn (array $session): bool => $selectedRoom === '' || $session['room_id'] === $selectedRoom));
        $sessions = array_values(array_filter($roomSchedules, fn (array $session): bool => $session['tanggal'] === $selectedDate->toDateString()));
        $weekDates = array_map(function (int $offset) use ($selectedDate, $roomSchedules): array {
            $date = $selectedDate->startOfWeek()->addDays($offset);

            return [
                'date' => $date,
                'count' => count(array_filter($roomSchedules, fn (array $session): bool => $session['tanggal'] === $date->toDateString())),
            ];
        }, range(0, 6));

        return view('jadwal', [
            ...$request->identity(),
            'selectedDate' => $selectedDate,
            'selectedRoom' => $selectedRoom,
            'sessions' => $sessions,
            'weekDates' => $weekDates,
            'rooms' => LabData::rooms(),
            'scheduleWeek' => CarbonImmutable::now('Asia/Jakarta')->startOfWeek(),
        ]);
    }

    public function profile(DemoIdentityRequest $request): View
    {
        $identity = $request->identity();
        $session = $request->session()->get('demo_session', []);
        $hasMatchingSession = ($session['username'] ?? null) === $identity['username']
            && ($session['role'] ?? null) === $identity['role'];
        $capabilities = match ($identity['role']) {
            'Asisten Praktikum' => ['Pendampingan kegiatan praktikum', 'Pemantauan ketersediaan perangkat', 'Koordinasi penggunaan ruang'],
            'Dosen Pembina Lab' => ['Perencanaan kegiatan akademik', 'Pemantauan fasilitas penelitian', 'Evaluasi penggunaan laboratorium'],
            'Mahasiswa Peneliti' => ['Penelusuran perangkat penelitian', 'Pengecekan jadwal laboratorium', 'Persiapan kebutuhan praktikum'],
            default => ['Koordinasi inventaris laboratorium', 'Pemantauan jadwal dan ruangan', 'Rekap kondisi serta ketersediaan alat'],
        };

        return view('profile', [
            ...$identity,
            'profileData' => [
                ...LabData::profile($identity['username']),
                'terakhir_login' => $hasMatchingSession ? CarbonImmutable::parse($session['started_at'])->setTimezone('Asia/Jakarta') : null,
                'cakupan_peran' => $capabilities,
            ],
        ]);
    }

    public function logout(Request $request): RedirectResponse
    {
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return to_route('login')->with('info', 'Sesi demo telah diakhiri. Kamu dapat masuk kembali dengan peran lain.');
    }
}
