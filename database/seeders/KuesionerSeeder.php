<?php

namespace Database\Seeders;

use App\Http\Controllers\Admin\QuestionController;
use App\Models\Question;
use Illuminate\Database\Seeder;

/**
 * Kuesioner penelitian: 10 soal pengetahuan (pilihan ganda) dan 10 pernyataan
 * sikap (Likert). Soal post-test dibuat identik dengan pre-test karena analisis
 * penelitiannya membandingkan jawaban sebelum dan sesudah materi edukasi.
 *
 * Opsi Likert dan skornya diambil dari QuestionController, bukan ditulis ulang,
 * supaya soal hasil seeder ini dan soal yang nanti dibuat admin lewat form
 * dinilai dengan aturan yang sama persis.
 */
class KuesionerSeeder extends Seeder
{
    /** Soal pengetahuan. 'benar' = indeks 0-based dari pilihan yang benar. */
    private const PENGETAHUAN = [
        [
            'soal'  => 'Apa yang dimaksud dengan stunting?',
            'opsi'  => [
                'Anak kelebihan berat badan',
                'Anak pendek akibat kekurangan gizi kronis',
                'Anak lahir prematur',
                'Anak mengalami obesitas',
                'Anak sulit makan karena sakit',
            ],
            'benar' => 1, // B
        ],
        [
            'soal'  => 'Mengapa stunting harus dicegah?',
            'opsi'  => [
                'Karena mengganggu perkembangan otak anak',
                'Karena membuat anak malas bergerak',
                'Karena anak jadi pendek',
                'Karena biaya pengobatan mahal',
                'Karena anak sulit tidur',
            ],
            'benar' => 0, // A
        ],
        [
            'soal'  => 'Apa peran ayah dalam mencegah stunting?',
            'opsi'  => [
                'Mencari nafkah saja',
                'Memastikan anak memakai baju baru',
                'Mengantar anak les privat',
                'Terlibat dalam pengasuhan dan menyediakan makanan bergizi',
                'Menemani anak menonton TV',
            ],
            'benar' => 3, // D
        ],
        [
            'soal'  => 'Manakah pernyataan yang SALAH tentang 1000 Hari Pertama Kehidupan?',
            'opsi'  => [
                'Periode ini dimulai sejak masa kehamilan',
                'Periode ini berakhir saat anak usia 5 tahun',
                'Periode ini penting untuk pertumbuhan otak anak',
                'Kekurangan gizi pada periode ini sulit diperbaiki',
                'Periode ini berlangsung hingga anak usia 2 tahun',
            ],
            'benar' => 1, // B — seharusnya berakhir usia 2 tahun
        ],
        [
            'soal'  => 'Apa penyebab utama stunting?',
            'opsi'  => [
                'Kurang gizi dalam waktu lama',
                'Sakit batuk pilek',
                'Faktor keturunan',
                'Kurang olahraga',
                'Terlalu banyak minum susu',
            ],
            'benar' => 0, // A
        ],
        [
            'soal'  => 'Pernyataan berikut ini yang TIDAK BENAR tentang zat gizi adalah?',
            'opsi'  => [
                'Protein penting untuk pertumbuhan',
                'Zat besi mencegah anemia',
                'Karbohidrat sumber tenaga',
                'Zink meningkatkan daya tahan tubuh',
                'Vitamin C tidak penting untuk anak',
            ],
            'benar' => 4, // E — Vitamin C justru penting
        ],
        [
            'soal'  => 'Mana menu MPASI yang paling bergizi untuk anak?',
            'opsi'  => [
                'Nasi dengan sayur dan kerupuk',
                'Mi instan',
                'Nasi dengan ayam, tempe, dan sayur',
                'Bubur instan',
                'Nasi dengan ayam, sayur, dan teh manis',
            ],
            'benar' => 2, // C
        ],
        [
            'soal'  => 'Manakah yang BUKAN alasan kebersihan lingkungan penting cegah stunting?',
            'opsi'  => [
                'Mencegah infeksi pada anak',
                'Menjaga kualitas air tetap bersih',
                'Membuat anak betah di rumah',
                'Mencegah diare pada anak',
                'Mengurangi risiko penyakit',
            ],
            'benar' => 2, // C
        ],
        [
            'soal'  => 'Tanda anak berisiko stunting adalah?',
            'opsi'  => [
                'Anak aktif bergerak',
                'Berat badan sulit naik',
                'Anak suka bermain',
                'Anak banyak tidur',
                'Anak selalu ceria',
            ],
            'benar' => 1, // B
        ],
        [
            'soal'  => 'Pernyataan berikut yang SALAH tentang pencegahan stunting adalah?',
            'opsi'  => [
                'Stunting bisa dicegah sejak masa kehamilan',
                'ASI eksklusif membantu cegah stunting',
                'Stunting hanya disebabkan faktor keturunan',
                'MPASI bergizi penting untuk anak',
                'Imunisasi lengkap melindungi anak dari penyakit',
            ],
            'benar' => 2, // C
        ],
    ];

    /** Pernyataan sikap. true = favourable, false = unfavourable. */
    private const SIKAP = [
        ['soal' => 'ASI eksklusif selama 6 bulan penting untuk mencegah stunting.', 'favourable' => true],
        ['soal' => 'Ibu hamil harus memeriksakan kehamilan secara rutin.', 'favourable' => true],
        ['soal' => 'Ayah wajib ikut memantau pertumbuhan anak ke posyandu.', 'favourable' => true],
        ['soal' => 'Saya siap menyediakan makanan bergizi setiap hari untuk keluarga.', 'favourable' => true],
        ['soal' => 'Membawa anak ke posyandu hanya membuang-buang waktu.', 'favourable' => false],
        ['soal' => 'Mencuci tangan dengan sabun sebelum menyuapi anak tidak terlalu penting.', 'favourable' => false],
        ['soal' => 'Anak yang pendek pasti bisa mengejar tinggi badannya sendiri tanpa perlu intervensi khusus.', 'favourable' => false],
        ['soal' => 'Protein hewani seperti telur, ikan, dan ayam penting diberikan kepada anak setiap hari.', 'favourable' => true],
        ['soal' => 'Urusan gizi dan kesehatan anak sepenuhnya adalah tugas ibu.', 'favourable' => false],
        ['soal' => 'Imunisasi lengkap sangat penting untuk melindungi anak dari penyakit yang dapat memicu stunting.', 'favourable' => true],
    ];

    public function run(): void
    {
        // Seeder ini dijalankan lewat cron yang berulang tiap menit. Tanpa penjaga
        // ini, satu menit kelalaian menghapus-dan-membuat ulang soal — dan kalau
        // sudah ada responden yang mengerjakan, jawaban mereka ikut terhapus
        // karena question_options memakai cascadeOnDelete.
        if (Question::exists()) {
            $this->command->warn('Soal sudah ada di database. Seeder dilewati agar tidak menggandakan.');
            $this->command->warn('Kosongkan tabel questions lebih dulu bila memang ingin mengisi ulang.');

            return;
        }

        foreach (['pre', 'post'] as $tipe) {
            $urutan = 1;

            foreach (self::PENGETAHUAN as $item) {
                $question = Question::create([
                    'question_text'   => $item['soal'],
                    'type'            => $tipe,
                    'question_format' => 'multiple_choice',
                    'is_favourable'   => null,
                    'order'           => $urutan++,
                ]);

                $labels = ['A', 'B', 'C', 'D', 'E'];

                foreach ($item['opsi'] as $i => $teks) {
                    $benar = ($i === $item['benar']);

                    $question->options()->create([
                        'label'       => $labels[$i],
                        'option_text' => $teks,
                        'is_correct'  => $benar,
                        'score'       => $benar ? 10 : 0,
                    ]);
                }
            }

            foreach (self::SIKAP as $item) {
                $question = Question::create([
                    'question_text'   => $item['soal'],
                    'type'            => $tipe,
                    'question_format' => 'likert',
                    'is_favourable'   => $item['favourable'],
                    'order'           => $urutan++,
                ]);

                $skor = $item['favourable']
                    ? QuestionController::LIKERT_SCORES_FAV
                    : QuestionController::LIKERT_SCORES_UNFAV;

                foreach (QuestionController::LIKERT_OPTIONS as $i => $opsi) {
                    $question->options()->create([
                        'label'       => $opsi['label'],
                        'option_text' => $opsi['option_text'],
                        'is_correct'  => false,
                        'score'       => $skor[$i],
                    ]);
                }
            }
        }

        $this->command->info('Kuesioner dibuat: ' . Question::count() . ' soal (pre + post).');
    }
}
