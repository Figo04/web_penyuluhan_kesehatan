<?php

namespace Tests\Feature;

use App\Models\Material;
use App\Models\Respondent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BerandaTest extends TestCase
{
    use RefreshDatabase;

    /** Judul pada langkah yang terkunci — penanda paling andal, karena tautan
     *  Materi selalu ada di navbar sehingga tidak bisa dipakai sebagai patokan. */
    private const KUNCI_MATERI = 'title="Selesaikan Pre-Test terlebih dahulu"';
    private const KUNCI_POST   = 'title="Selesaikan Pre-Test dan baca semua materi terlebih dahulu"';

    private int $nomor = 0;

    private function buka(array $overrides = [], int $jumlahMateri = 2)
    {
        if (Material::count() === 0) {
            for ($i = 1; $i <= $jumlahMateri; $i++) {
                Material::create(['title' => "Materi $i", 'type' => 'artikel', 'content' => 'https://contoh.id', 'order' => $i]);
            }
        }

        $r = Respondent::create(array_merge([
            'access_code' => 'SEHAT' . str_pad(++$this->nomor, 4, '0', STR_PAD_LEFT),
            'name' => 'Rina',
            'age' => 30,
            'gender' => 'perempuan',
            'marital_status' => 'menikah',
            'occupation' => 'Guru',
            'total_children' => 1,
        ], $overrides));

        return $this->withSession(['respondent_id' => $r->id])->get('/beranda');
    }

    public function test_sapaan_ibu_untuk_responden_perempuan(): void
    {
        $this->buka(['gender' => 'perempuan', 'name' => 'Rina'])
            ->assertOk()
            ->assertSee('Selamat datang kembali,')
            ->assertSee('Ibu Rina');
    }

    public function test_sapaan_bapak_untuk_responden_laki_laki(): void
    {
        $this->buka(['gender' => 'laki-laki', 'name' => 'Budi'])
            ->assertOk()
            ->assertSee('Bapak Budi')
            ->assertDontSee('Ibu Budi');
    }

    public function test_judul_kenali_stunting_tetap_ada(): void
    {
        $this->buka()->assertOk()->assertSee('Kenali Stunting');
    }

    public function test_responden_baru_hanya_pretest_yang_terbuka(): void
    {
        $res = $this->buka();

        $res->assertOk()
            ->assertSee('0 dari 3 selesai')
            ->assertSee('Mulai Pre-Test')
            // Materi dan post-test digambar sebagai langkah terkunci, bukan tautan.
            ->assertSee(self::KUNCI_MATERI, false)
            ->assertSee(self::KUNCI_POST, false);
    }

    public function test_setelah_pretest_materi_terbuka_dan_cta_mengarah_ke_materi(): void
    {
        $res = $this->buka(['pre_test_done' => true]);

        $res->assertOk()
            ->assertSee('1 dari 3 selesai')
            ->assertSee('Lanjutkan Materi')
            ->assertDontSee(self::KUNCI_MATERI, false)
            // Post-test masih terkunci karena materi belum dibaca.
            ->assertSee(self::KUNCI_POST, false);
    }

    public function test_setelah_materi_selesai_posttest_terbuka(): void
    {
        $res = $this->buka(['pre_test_done' => true, 'material_done' => true]);

        $res->assertOk()
            ->assertSee('2 dari 3 selesai')
            ->assertSee('Kerjakan Post-Test')
            ->assertDontSee(self::KUNCI_POST, false);
    }

    public function test_semua_selesai_menampilkan_ucapan_terima_kasih(): void
    {
        $this->buka(['pre_test_done' => true, 'material_done' => true, 'post_test_done' => true])
            ->assertOk()
            ->assertSee('3 dari 3 selesai')
            ->assertSee('Terima kasih telah berpartisipasi')
            ->assertDontSee('Kerjakan Post-Test');
    }

    public function test_tanpa_materi_posttest_tidak_terkunci_selamanya(): void
    {
        // Admin belum mengisi materi — responden tidak boleh terjebak.
        $this->buka(['pre_test_done' => true], jumlahMateri: 0)
            ->assertOk()
            ->assertSee('Kerjakan Post-Test');
    }

    public function test_kartu_info_kesehatan_muncul_hanya_setelah_pretest(): void
    {
        $this->buka()->assertDontSee('Info Kesehatan Untukmu');

        $this->buka(['pre_test_done' => true])->assertSee('Info Kesehatan Untukmu');
    }

    public function test_delapan_kartu_info_kesehatan_tersedia(): void
    {
        $res = $this->buka(['pre_test_done' => true]);

        foreach ([
            'Tanda Anak Berisiko Stunting',
            'Kesalahan yang Sering Dilakukan Orang Tua',
            'Contoh Menu Sehari untuk Balita',
            'Peran Ayah yang Sering Diabaikan',
            'Pentingnya Posyandu',
            'Lingkungan Rumah yang Sehat',
            'Dampak Stunting Saat Dewasa',
            'Langkah Mudah Mulai Hari Ini',
        ] as $judul) {
            $res->assertSee($judul);
        }
    }
}
