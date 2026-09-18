<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Respondent;
use App\Models\Question;

class ExportController extends Controller
{
    const CHILD_HEADERS = ['Nama Anak', 'Tgl Lahir Anak', 'Umur Anak (bln)', 'JK Anak', 'Anak Ke', 'BB Anak (kg)', 'TB Anak (cm)'];

    private function childColumns(Respondent $r): array
    {
        $c = $r->child;
        return $c
            ? [$c->name, $c->birth_date->format('d/m/Y'), $c->ageInMonths(), $c->gender, $c->child_order, $c->weight_kg + 0, $c->height_cm + 0]
            : array_fill(0, count(self::CHILD_HEADERS), '-');
    }

    /** Soal kondisi anak diekspor sebagai teks jawaban, soal lain sebagai label (A/B/STS/...) */
    private function answerCell(Respondent $r, Question $q, string $testType): string
    {
        $answer = $r->answers->where('question_id', $q->question_id)->where('test_type', $testType)->first();
        if (!$answer || !$answer->option) return '-';
        return $q->category === 'kondisi_anak' ? $answer->option->option_text : $answer->option->label;
    }

    private function preQuestions()
    {
        return Question::where('type', 'pre')->orderByRaw("category = 'kondisi_anak' DESC")->orderBy('order')->get();
    }

    private function preHeader(Question $q): string
    {
        return ($q->category === 'kondisi_anak' ? 'Anak Q' : 'Pre Q') . $q->order;
    }

    public function exportExcel()
    {
        $respondents   = Respondent::with(['answers.option', 'answers.question', 'location', 'child'])->get();
        $preQuestions  = $this->preQuestions();
        $postQuestions = Question::where('type', 'post')->orderBy('order')->get();

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // ── Header ──────────────────────────────────────────────
        $headers = ['Kode', 'Nama', 'Umur', 'Jenis Kelamin', 'Status Perkawinan', 'Pekerjaan', 'Jumlah Anak', 'Riwayat Penyakit', 'Lokasi', ...self::CHILD_HEADERS];
        foreach ($preQuestions as $q)  $headers[] = $this->preHeader($q);
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

        // Auto width kolom — pakai index angka supaya aman kalau kolom > 26
        for ($i = 1; $i <= count($headers); $i++) {
            $col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i);
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
                ...$this->childColumns($r),
            ];

            foreach ($preQuestions as $q)  $data[] = $this->answerCell($r, $q, 'pre');
            foreach ($postQuestions as $q) $data[] = $this->answerCell($r, $q, 'post');

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

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit;
    }

    public function exportCsv()
    {
        $respondents   = Respondent::with(['answers.question', 'answers.option', 'location', 'child'])->get();
        $preQuestions  = $this->preQuestions();
        $postQuestions = Question::where('type', 'post')->orderBy('order')->get();

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
            $header = ['Kode', 'Nama', 'Umur', 'Jenis Kelamin', 'Status Perkawinan', 'Pekerjaan', 'Riwayat Penyakit', 'Lokasi', ...self::CHILD_HEADERS];
            foreach ($preQuestions as $q)  $header[] = $this->preHeader($q);
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
                    $medicalHistory,
                    $r->location ? $r->location->name : '-',
                    ...$this->childColumns($r),
                ];

                foreach ($preQuestions as $q)  $row[] = $this->answerCell($r, $q, 'pre');
                foreach ($postQuestions as $q) $row[] = $this->answerCell($r, $q, 'post');

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