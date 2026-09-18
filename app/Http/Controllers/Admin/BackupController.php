<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class BackupController extends Controller
{
    /**
     * Halaman backup database
     */
    public function index()
    {
        // Ambil daftar tabel dan jumlah baris masing-masing
        $tables = [];
        $tableNames = DB::select('SHOW TABLES');
        $dbName = config('database.connections.mysql.database');

        foreach ($tableNames as $row) {
            $tableName = array_values((array) $row)[0];
            $count = DB::table($tableName)->count();
            $tables[] = [
                'name'  => $tableName,
                'count' => $count,
            ];
        }

        return view('admin.backup', compact('tables'));
    }

    /**
     * Generate dan download file SQL backup
     */
    public function download()
    {
        $dbName    = config('database.connections.mysql.database');
        $timestamp = now()->format('Y-m-d_H-i-s');
        $filename  = "{$dbName}_backup_{$timestamp}.sql";

        // Stream langsung ke browser sebagai download
        return response()->stream(function () use ($dbName) {
            $this->generateSql($dbName);
        }, 200, [
            'Content-Type'        => 'application/octet-stream',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Cache-Control'       => 'no-cache, no-store, must-revalidate',
            'Pragma'              => 'no-cache',
            'Expires'             => '0',
        ]);
    }

    /**
     * Generate SQL dump via PHP (tanpa mysqldump — kompatibel Railway/cloud)
     */
    private function generateSql(string $dbName): void
    {
        $tableNames = DB::select('SHOW TABLES');

        // Header file SQL
        echo "-- ============================================================\n";
        echo "-- Database Backup: {$dbName}\n";
        echo "-- Dibuat pada: " . now()->format('Y-m-d H:i:s') . " WIB\n";
        echo "-- Aplikasi: SehatEdukasi — Platform Edukasi Pencegahan Stunting\n";
        echo "-- ============================================================\n\n";
        echo "SET FOREIGN_KEY_CHECKS = 0;\n";
        echo "SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';\n";
        echo "SET NAMES utf8mb4;\n\n";

        foreach ($tableNames as $row) {
            $table = array_values((array) $row)[0];

            // ── CREATE TABLE ──────────────────────────────────────
            echo "-- ------------------------------------------------------------\n";
            echo "-- Tabel: `{$table}`\n";
            echo "-- ------------------------------------------------------------\n";
            echo "DROP TABLE IF EXISTS `{$table}`;\n";

            $createRow = DB::select("SHOW CREATE TABLE `{$table}`");
            $createSql = $createRow[0]->{'Create Table'};
            echo $createSql . ";\n\n";

            // ── INSERT DATA ───────────────────────────────────────
            $rows = DB::table($table)->get();

            if ($rows->isEmpty()) {
                echo "-- (tidak ada data)\n\n";
                continue;
            }

            // Ambil nama kolom dari baris pertama
            $columns = array_keys((array) $rows->first());
            $columnList = implode(', ', array_map(fn($c) => "`{$c}`", $columns));

            // Tulis INSERT dalam batch 100 baris supaya tidak memory-intensive
            $batch = [];
            foreach ($rows as $row) {
                $values = array_map(function ($val) {
                    if (is_null($val)) return 'NULL';
                    if (is_numeric($val)) return $val;
                    return "'" . addslashes($val) . "'";
                }, (array) $row);

                $batch[] = '(' . implode(', ', $values) . ')';

                if (count($batch) >= 100) {
                    echo "INSERT INTO `{$table}` ({$columnList}) VALUES\n";
                    echo implode(",\n", $batch) . ";\n";
                    $batch = [];
                }
            }

            if (!empty($batch)) {
                echo "INSERT INTO `{$table}` ({$columnList}) VALUES\n";
                echo implode(",\n", $batch) . ";\n";
            }

            echo "\n";
        }

        echo "SET FOREIGN_KEY_CHECKS = 1;\n";
        echo "-- ============================================================\n";
        echo "-- Backup selesai\n";
        echo "-- ============================================================\n";
    }
}