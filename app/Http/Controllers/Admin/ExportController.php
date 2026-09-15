<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Respondent;
use App\Models\Question;
use Illuminate\Support\Facades\Log;

class ExportController extends Controller
{
    /**
     * File export memuat kode akses seluruh responden — dan kode akses adalah
     * kredensial login mereka. Siapa pun yang memegang file itu bisa masuk
     * sebagai responden mana pun. Selama kolom itu masih ikut terekspor,
     * minimal harus ada jejak siapa mengunduhnya dan kapan.
     */
    private function catatExport(string $format, int $jumlah): void
    {
        Log::info('Data responden diexport', [
            'format'     => $format,
            'jumlah'     => $jumlah,
            'admin'      => auth()->user()?->email,
            'ip'         => request()->ip(),
        ]);
    }

    public function exportExcel()
    {
        $respondents   = Respondent::with(['answers.option', 'answers.question', 'location'])->get();
        $this->catatExport('xlsx', $respondents->count());
        $preQuestions  = Question::where('type', 'pre')->orderBy('order')->get();
        $postQuestions = Question::where('type', 'post')->orderBy('order')->get();

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // ── Header ──────────────────────────────────────────────
        $headers = ['Kode', 'Nama', 'Umur', 'Jenis Kelamin', 'Status Perkawinan', 'Pekerjaan', 'Jumlah Anak', 'Riwayat Penyakit', 'Lokasi'];
        foreach ($preQuestions as $q)  $headers[] = 'Pre Q'  . $q->order;
        foreach ($postQuestions as $q) $headers[] = 'Post Q' . $q->order;
        $headers[] = 'Pre-Test Selesai';
        $headers[] = 'Post-Test Selesai';                   
        $headers[] = 'Waktu Pre-Test';
        $headers[] = 'Waktu Post-Test';
        $sheet->fromArray($headers, null, 'A1');

        // Style header — bold & background hijau
        $headerStyle = [
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '1a7a5e']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        ];
        $lastCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($headers));
        $sheet->getStyle('A1:' . $lastCol . '1')->applyFromArray($headerStyle);

        // Auto width kolom
        foreach (range('A', $lastCol) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // ── Data ────────────────────────────────────────────────
        $row = 2;
        foreach ($respondents as $r) {
            // Riwayat penyakit dari JSON
            $medicalHistory = is_array($r->medical_history)
                ? implode(', ', $r->medical_history)
                : ($r->medical_history ?? '-');

            $data = [
                $r->access_code,
                $r->name,
                $r->age,
                $r->gender,
                $r->marital_status,
                $r->occupation,
                $r->total_children ?? 0,
                $medicalHistory,
                $r->location ? $r->location->name : '-',
            ];

            // Jawaban Pre-Test per soal
            foreach ($preQuestions as $q) {
                $answer  = $r->answers->where('question_id', $q->id)->where('test_type', 'pre')->first();
                // Null-safe: cek answer dan option tidak null sebelum akses ->label
                $data[] = ($answer && $answer->option) ? $answer->option->label : '-';
            }

            // Jawaban Post-Test per soal
            foreach ($postQuestions as $q) {
                $answer  = $r->answers->where('question_id', $q->id)->where('test_type', 'post')->first();
                $data[] = ($answer && $answer->option) ? $answer->option->label : '-';
            }

            $data[] = $r->pre_test_done  ? 'Ya' : 'Belum';
            $data[] = $r->post_test_done ? 'Ya' : 'Belum';
            $data[] = $r->pre_test_at  ? $r->pre_test_at->format('d/m/Y H:i')  : '-';
            $data[] = $r->post_test_at ? $r->post_test_at->format('d/m/Y H:i') : '-';

            $sheet->fromArray($data, null, 'A' . $row);
            $row++;
        }

        // ── Output ──────────────────────────────────────────────
        $writer   = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = 'data-penelitian-' . date('Ymd-His') . '.xlsx';

        // streamDownload, bukan header()+exit: exit mematikan proses sebelum Laravel
        // sempat menutup session dan menjalankan middleware terminate-nya.
        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type'  => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    public function exportCsv()
    {
        $respondents   = Respondent::with(['answers.question', 'answers.option', 'location'])->get();
        $preQuestions  = Question::where('type', 'pre')->orderBy('order')->get();
        $postQuestions = Question::where('type', 'post')->orderBy('order')->get();

        $this->catatExport('csv', $respondents->count());

        $filename = 'data-penelitian-' . date('Ymd-His') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename=' . $filename,
        ];

        $callback = function () use ($respondents, $preQuestions, $postQuestions) {
            $file = fopen('php://output', 'w');

            // BOM untuk Excel agar UTF-8 terbaca dengan benar
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // ── Header CSV ──────────────────────────────────────
            $header = ['Kode', 'Nama', 'Umur', 'Jenis Kelamin', 'Status Perkawinan', 'Pekerjaan', 'Jumlah Anak', 'Riwayat Penyakit', 'Lokasi'];
            foreach ($preQuestions as $q)  $header[] = 'Pre Q'  . $q->order;
            foreach ($postQuestions as $q) $header[] = 'Post Q' . $q->order;
            $header[] = 'Pre-Test Selesai';
            $header[] = 'Post-Test Selesai';
            $header[] = 'Waktu Pre-Test';
            $header[] = 'Waktu Post-Test';
            fputcsv($file, $header);

            // ── Data CSV ────────────────────────────────────────
            foreach ($respondents as $r) {
                // Riwayat penyakit dari JSON
                $medicalHistory = is_array($r->medical_history)
                    ? implode('; ', $r->medical_history)
                    : ($r->medical_history ?? '-');

                $row = [
                    $r->access_code,
                    $r->name,
                    $r->age,
                    $r->gender,
                    $r->marital_status,
                    $r->occupation,
                    $r->total_children ?? 0,
                    $medicalHistory,
                    $r->location ? $r->location->name : '-',
                ];

                // Jawaban Pre-Test per soal
                foreach ($preQuestions as $q) {
                    $answer  = $r->answers->where('question_id', $q->id)->where('test_type', 'pre')->first();
                    // Null-safe: cek answer dan option tidak null sebelum akses ->label
                    $row[] = ($answer && $answer->option) ? $answer->option->label : '-';
                }

                // Jawaban Post-Test per soal
                foreach ($postQuestions as $q) {
                    $answer  = $r->answers->where('question_id', $q->id)->where('test_type', 'post')->first();
                    $row[] = ($answer && $answer->option) ? $answer->option->label : '-';
                }

                $row[] = $r->pre_test_done  ? 'Ya' : 'Belum';
                $row[] = $r->post_test_done ? 'Ya' : 'Belum';
                $row[] = $r->pre_test_at  ? $r->pre_test_at->format('d/m/Y H:i')  : '-';
                $row[] = $r->post_test_at ? $r->post_test_at->format('d/m/Y H:i') : '-';

                fputcsv($file, $row);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}