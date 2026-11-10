<?php

namespace Tests\Feature;

use Carbon\CarbonImmutable;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class SimLabTest extends TestCase
{
    private const IDENTITY = ['username' => 'gamal.review', 'role' => 'Administrator Lab'];

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
        $this->travelTo(CarbonImmutable::parse('2026-11-10 09:30:00', 'Asia/Jakarta'));
    }

    public function test_login_records_a_demo_session_and_passes_only_identity_to_the_dashboard(): void
    {
        $response = $this->post(route('login.process'), [
            'username' => '  gamal.review  ',
            'role' => 'Administrator Lab',
            'password' => 'demo-password',
        ]);

        $response->assertRedirect(route('dashboard', self::IDENTITY))
            ->assertSessionHas('demo_session', [
                ...self::IDENTITY,
                'started_at' => '2026-11-10T09:30:00+07:00',
            ])
            ->assertSessionMissing('demo_session.password');

        $this->assertGuest();
    }

    public function test_demo_login_does_not_require_a_password(): void
    {
        $this->post(route('login.process'), self::IDENTITY)
            ->assertRedirect(route('dashboard', self::IDENTITY))
            ->assertSessionHasNoErrors();
    }

    public function test_legacy_administrator_username_uses_the_naghamal_identity(): void
    {
        $identity = ['username' => 'naghamal.islam', 'role' => 'Administrator Lab'];

        $this->post(route('login.process'), [
            'username' => 'gamal.ramadhan',
            'role' => 'Administrator Lab',
        ])->assertRedirect(route('dashboard', $identity))
            ->assertSessionHas('demo_session.username', 'naghamal.islam');

        $this->get(route('profile', ['username' => 'gamal.ramadhan', 'role' => 'Administrator Lab']))
            ->assertOk()
            ->assertViewHas('username', 'naghamal.islam')
            ->assertViewHas('profileData', fn (array $profile): bool => $profile['nama_lengkap'] === 'Muhammad Naghamal Islam');
    }

    /** @param array<string, mixed> $input */
    #[DataProvider('invalidLoginInputs')]
    public function test_invalid_login_input_is_rejected_and_can_be_rendered_again(array $input, string $field): void
    {
        $this->from(route('login'))->post(route('login.process'), array_replace(self::IDENTITY, $input))
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors($field)
            ->assertSessionMissing('demo_session')
            ->assertSessionMissing('_old_input.password');

        $this->get(route('login'))->assertOk();
    }

    /** @return array<string, array{array<string, mixed>, string}> */
    public static function invalidLoginInputs(): array
    {
        return [
            'missing username' => [['username' => null], 'username'],
            'blank username' => [['username' => '   '], 'username'],
            'short username' => [['username' => 'ab'], 'username'],
            'long username' => [['username' => str_repeat('a', 31)], 'username'],
            'array username' => [['username' => ['gamal']], 'username'],
            'missing role' => [['role' => null], 'role'],
            'unsupported role' => [['role' => 'Super Admin'], 'role'],
            'array role' => [['role' => ['Administrator Lab']], 'role'],
            'array password' => [['password' => ['secret']], 'password'],
            'short password' => [['password' => '12345'], 'password'],
            'long password' => [['password' => str_repeat('a', 73)], 'password'],
        ];
    }

    #[DataProvider('workspacePages')]
    public function test_workspace_pages_support_default_and_validated_query_identities(string $page): void
    {
        $this->get(route($page))->assertOk()
            ->assertViewHas('username', 'Guest')
            ->assertViewHas('role', 'Administrator Lab');

        $this->get(route($page, self::IDENTITY))->assertOk()
            ->assertViewHas('username', self::IDENTITY['username'])
            ->assertViewHas('role', self::IDENTITY['role'])
            ->assertSee(route('jadwal', self::IDENTITY))
            ->assertSee(route('pengelolaan', self::IDENTITY))
            ->assertSee(route('profile', self::IDENTITY));
    }

    #[DataProvider('workspacePages')]
    public function test_array_query_identity_redirects_safely_to_login(string $page): void
    {
        $response = $this->get(route($page, ['username' => ['gamal'], 'role' => 'Administrator Lab']))
            ->assertRedirect(route('login'));

        $this->followRedirects($response)->assertOk()->assertSeeText('Nama pengguna harus berupa teks.');
    }

    /** @return array<string, array{string}> */
    public static function workspacePages(): array
    {
        return [
            'dashboard' => ['dashboard'],
            'inventory' => ['pengelolaan'],
            'schedule' => ['jadwal'],
            'profile' => ['profile'],
        ];
    }

    public function test_dashboard_and_inventory_totals_match_and_account_for_every_unit(): void
    {
        $dashboard = $this->get(route('dashboard'))->assertOk()->assertSeeText('Selasa, 10 November 2026');
        $inventory = $this->get(route('pengelolaan'))->assertOk();
        $expected = [
            'total_alat' => 44,
            'siap_pakai' => 31,
            'sedang_dipinjam' => 12,
            'pemeliharaan' => 1,
            'total_jenis' => 10,
            'total_kategori' => 6,
            'persentase_tersedia' => 70.5,
        ];

        $this->assertSame($expected, $dashboard->viewData('stats'));
        $this->assertSame($expected, $inventory->viewData('stats'));

        foreach ($inventory->viewData('items') as $item) {
            $this->assertSame($item['stok'], $item['tersedia'] + $item['dipinjam'] + $item['pemeliharaan']);
            $this->assertGreaterThanOrEqual(0, min($item['tersedia'], $item['dipinjam'], $item['pemeliharaan']));
        }
    }

    public function test_inventory_search_covers_specifications_case_and_surrounding_whitespace(): void
    {
        $this->get(route('pengelolaan', [...self::IDENTITY, 'q' => '  nViDiA  ']))
            ->assertOk()
            ->assertViewHas('visibleIds', [1, 8])
            ->assertViewHas('filters', ['q' => 'nViDiA', 'kategori' => '', 'status' => ''])
            ->assertViewHas('filteredStats', fn (array $stats): bool => $stats['total_jenis'] === 2
                && $stats['total_alat'] === 12 && $stats['siap_pakai'] === 9);
    }

    /** @param array<string, string> $filters
     * @param  list<int>  $visibleIds
     */
    #[DataProvider('inventoryFilters')]
    public function test_inventory_combines_filters_and_handles_empty_results(array $filters, array $visibleIds): void
    {
        $response = $this->get(route('pengelolaan', [...self::IDENTITY, ...$filters]))
            ->assertOk()->assertViewHas('visibleIds', $visibleIds);

        if ($visibleIds === []) {
            $response->assertViewHas('filteredStats', fn (array $stats): bool => $stats['total_alat'] === 0
                && $stats['siap_pakai'] === 0 && $stats['persentase_tersedia'] === 0.0);
        }
    }

    /** @return array<string, array{array<string, string>, list<int>}> */
    public static function inventoryFilters(): array
    {
        return [
            'category and maintenance' => [['kategori' => 'Fabrikasi Digital & IoT', 'status' => 'pemeliharaan'], [7]],
            'search and category' => [['q' => 'NVIDIA', 'kategori' => 'Komputasi Tinggi'], [1]],
            'fully available devices' => [['status' => 'tersedia'], [3, 4, 6, 9]],
            'location search' => [['q' => 'Studio Broadcasting'], [5, 9]],
            'no matching device' => [['q' => 'perangkat-tidak-ditemukan'], []],
        ];
    }

    public function test_csv_export_uses_the_same_filters_and_keeps_comma_separated_fields_intact(): void
    {
        $response = $this->get(route('pengelolaan.export', [...self::IDENTITY, 'q' => 'NVIDIA']))
            ->assertOk()
            ->assertDownload('sim-lab-inventaris-2026-11-10.csv')
            ->assertHeader('Content-Type', 'text/csv; charset=UTF-8');

        $csv = $response->streamedContent();
        $this->assertStringStartsWith("\xEF\xBB\xBF", $csv);
        $rows = array_map(fn (string $line): array => str_getcsv($line, ',', '"', ''), explode("\n", trim(substr($csv, 3))));

        $this->assertCount(3, $rows);
        $this->assertSame('Kode', $rows[0][0]);
        $this->assertSame('LAB-WRK-001', $rows[1][0]);
        $this->assertSame('LAB-IOT-008', $rows[2][0]);
        $this->assertSame('Lab AI & Riset, Meja 01', $rows[1][3]);
        $this->assertSame(['4', '3', '1', '0'], array_slice($rows[1], 4, 4));
        $this->assertSame(['8', '6', '2', '0'], array_slice($rows[2], 4, 4));
        $this->assertStringNotContainsString('LAB-VR-002', $csv);
    }

    public function test_empty_csv_export_contains_only_column_headers(): void
    {
        $csv = $this->get(route('pengelolaan.export', ['q' => 'no-such-device']))
            ->assertOk()->streamedContent();

        $this->assertCount(1, explode("\n", trim($csv)));
        $this->assertStringContainsString('Pemeliharaan', $csv);
        $this->assertStringNotContainsString('LAB-', $csv);
    }

    /** @param array<string, mixed> $parameters */
    #[DataProvider('invalidQueryInputs')]
    public function test_invalid_query_inputs_return_validation_errors_for_json_requests(string $page, array $parameters, string $field): void
    {
        $this->getJson(route($page, [...self::IDENTITY, ...$parameters]))
            ->assertUnprocessable()->assertJsonValidationErrors($field);
    }

    /** @return array<string, array{string, array<string, mixed>, string}> */
    public static function invalidQueryInputs(): array
    {
        return [
            'invalid query role' => ['dashboard', ['role' => 'Super Admin'], 'role'],
            'array query role' => ['profile', ['role' => ['Administrator Lab']], 'role'],
            'array search' => ['pengelolaan', ['q' => ['NVIDIA']], 'q'],
            'long search' => ['pengelolaan', ['q' => str_repeat('a', 101)], 'q'],
            'invalid category' => ['pengelolaan', ['kategori' => 'Unknown'], 'kategori'],
            'array status' => ['pengelolaan', ['status' => ['tersedia']], 'status'],
            'export array identity' => ['pengelolaan.export', ['username' => ['gamal']], 'username'],
            'export invalid filter' => ['pengelolaan.export', ['status' => 'Unknown'], 'status'],
            'array date' => ['jadwal', ['tanggal' => ['2026-11-10']], 'tanggal'],
            'impossible date' => ['jadwal', ['tanggal' => '2026-02-31'], 'tanggal'],
            'invalid room' => ['jadwal', ['ruangan' => 'Unknown'], 'ruangan'],
        ];
    }

    public function test_schedule_filters_rooms_dates_and_calculates_wib_session_status(): void
    {
        $response = $this->get(route('jadwal', [...self::IDENTITY, 'tanggal' => '2026-11-10', 'ruangan' => 'multimedia']))
            ->assertOk()->assertViewHas('selectedRoom', 'multimedia');

        $sessions = $response->viewData('sessions');
        $this->assertCount(2, $sessions);
        $this->assertSame(['multimedia', 'multimedia'], array_column($sessions, 'room_id'));
        $this->assertSame(['2026-11-10', '2026-11-10'], array_column($sessions, 'tanggal'));
        $this->assertSame(['Berlangsung', 'Terjadwal'], array_column($sessions, 'status'));
        $this->assertCount(7, $response->viewData('weekDates'));

        $this->travelTo(CarbonImmutable::parse('2026-11-10 10:00:00', 'Asia/Jakarta'));
        $later = $this->get(route('jadwal', ['ruangan' => 'multimedia']))->assertOk();
        $this->assertSame('Selesai', $later->viewData('sessions')[0]['status']);
    }

    public function test_schedule_outside_the_demo_week_displays_an_empty_state(): void
    {
        $this->get(route('jadwal', ['tanggal' => '2026-10-28']))
            ->assertOk()->assertViewHas('sessions', [])
            ->assertSeeText('Belum ada kegiatan terjadwal');
    }

    public function test_profile_login_time_stays_fixed_after_refresh(): void
    {
        $this->post(route('login.process'), self::IDENTITY)->assertRedirect();

        $this->travel(1)->hours();
        $this->get(route('profile', self::IDENTITY))->assertOk()
            ->assertSeeText('10 November 2026, 09:30 WIB');

        $this->travel(2)->hours();
        $this->get(route('profile', self::IDENTITY))->assertOk()
            ->assertSeeText('10 November 2026, 09:30 WIB');
    }

    public function test_unknown_profiles_do_not_invent_identity_or_login_time(): void
    {
        $response = $this->get(route('profile', self::IDENTITY))->assertOk()
            ->assertSeeText('Akses langsung tanpa form login');

        $profile = $response->viewData('profileData');
        $this->assertSame('gamal.review', $profile['nama_lengkap']);
        $this->assertNull($profile['nomor_identitas']);
        $this->assertNull($profile['email']);
        $this->assertNull($profile['terakhir_login']);
    }

    public function test_a_changed_query_identity_does_not_reuse_another_demo_login_time(): void
    {
        $this->post(route('login.process'), self::IDENTITY)->assertRedirect();

        $this->get(route('profile', ['username' => 'asisten.labkom', 'role' => 'Asisten Praktikum']))
            ->assertOk()->assertViewHas('profileData', fn (array $profile): bool => $profile['terakhir_login'] === null);
    }

    public function test_user_supplied_identity_is_escaped_in_views(): void
    {
        $username = '<script>alert(1)</script>';

        $this->get(route('profile', ['username' => $username, 'role' => 'Mahasiswa Peneliti']))
            ->assertOk()->assertSee($username)->assertDontSee($username, false);
    }

    public function test_logout_requires_post_and_clears_the_session_and_rotates_the_csrf_token(): void
    {
        $this->withSession(['demo_session' => self::IDENTITY, 'demo_note' => 'temporary', '_token' => 'previous-token']);

        $this->get(route('logout'))->assertStatus(405)->assertSessionHas('demo_session');

        $this->post(route('logout'))->assertRedirect(route('login'))
            ->assertSessionMissing('demo_session')
            ->assertSessionMissing('demo_note')
            ->assertSessionHas('_token', fn (string $token): bool => $token !== '' && $token !== 'previous-token')
            ->assertSessionHas('info');

        $this->get(route('login'))->assertOk()->assertSeeText('Sesi demo telah diakhiri.');
    }
}
