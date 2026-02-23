<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Respondent;
use App\Models\Question;

class ExportController extends Controller
{
    public function exportExcel()
    {
        $respondents = Respondent::with(['answers.option', 'answers.question', 'location'])->get();
        $preQuestions = Question::where('type', 'pre')->orderBy('order')->get();
        $postQuestions = Question::where('type', 'post')->orderBy('order')->get();

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header
        $headers = ['Kode', 'Nama', 'Umur', 'Jenis Kelamin', 'Status Perkawinan', 'Pekerjaan', 'Lokasi'];
        foreach ($preQuestions as $q) $headers[] = 'Pre Q' . $q->order;
        foreach ($postQuestions as $q) $headers[] = 'Post Q' . $q->order;
        $headers[] = 'Waktu Pre-Test';
        $headers[] = 'Waktu Post-Test';
        $sheet->fromArray($headers, null, 'A1');

        // Data
        $row = 2;
        foreach ($respondents as $r) {
            $data = [
                $r->access_code,
                $r->name,
                $r->age,
                $r->gender,
                $r->marital_status,
                $r->occupation,
                $r->location ? $r->location->name : '-',
            ];

            foreach ($preQuestions as $q) {
                $answer = $r->answers->where('question_id', $q->id)->where('test_type', 'pre')->first();
                $data[] = $answer ? $answer->option->label : '-';
            }

            foreach ($postQuestions as $q) {
                $answer = $r->answers->where('question_id', $q->id)->where('test_type', 'post')->first();
                $data[] = $answer ? $answer->option->label : '-';
            }

            $data[] = $r->pre_test_at ?? '-';
            $data[] = $r->post_test_at ?? '-';
            $sheet->fromArray($data, null, 'A' . $row);
            $row++;
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = 'data-penelitian.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit;
    }

    public function exportCsv()
    {
        $respondents = Respondent::with(['answers.question', 'answers.option', 'location'])->get();
        $preQuestions = Question::where('type', 'pre')->orderBy('order')->get();
        $postQuestions = Question::where('type', 'post')->orderBy('order')->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=data-penelitian.csv'
        ];

        $callback = function () use ($respondents, $preQuestions, $postQuestions) {
            $file = fopen('php://output', 'w');

            // Header CSV
            $header = ['Kode', 'Nama', 'Umur', 'Jenis Kelamin', 'Status Perkawinan', 'Pekerjaan', 'Lokasi'];
            foreach ($preQuestions as $q) $header[] = 'Pre Q' . $q->order;
            foreach ($postQuestions as $q) $header[] = 'Post Q' . $q->order;
            $header[] = 'Waktu Pre-Test';
            $header[] = 'Waktu Post-Test';
            fputcsv($file, $header);

            // Data
            foreach ($respondents as $r) {
                $row = [
                    $r->access_code,
                    $r->name,
                    $r->age,
                    $r->gender,
                    $r->marital_status,
                    $r->occupation,
                    $r->location ? $r->location->name : '-',
                ];

                foreach ($preQuestions as $q) {
                    $answer = $r->answers->where('question_id', $q->id)->where('test_type', 'pre')->first();
                    $row[] = $answer ? $answer->option->label : '-';
                }

                foreach ($postQuestions as $q) {
                    $answer = $r->answers->where('question_id', $q->id)->where('test_type', 'post')->first();
                    $row[] = $answer ? $answer->option->label : '-';
                }

                $row[] = $r->pre_test_at ?? '-';
                $row[] = $r->post_test_at ?? '-';
                fputcsv($file, $row);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}