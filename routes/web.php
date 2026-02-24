<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RespondentAuthController;
use App\Http\Controllers\Respondent\HomeController;
use App\Http\Controllers\Respondent\TestController;
use App\Http\Controllers\Respondent\MaterialController;
use App\Http\Controllers\Respondent\ProfileController;
use App\Http\Controllers\Respondent\ConsultationController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\RespondentController;
use App\Http\Controllers\Admin\QuestionController;
use App\Http\Controllers\Admin\MaterialController as AdminMaterialController;
use App\Http\Controllers\Admin\DoctorController;
use App\Http\Controllers\Admin\ExportController;
use App\Http\Controllers\Admin\AdminAuthController;

// ==========================================
// HALAMAN UTAMA → redirect ke login
// ==========================================
Route::get('/', function () {
    return redirect()->route('login');
});

// ==========================================
// AUTH RESPONDEN
// ==========================================
Route::get('/login', [RespondentAuthController::class, 'showLogin'])->name('login');
Route::post('/login', [RespondentAuthController::class, 'login'])->name('login.post');
Route::get('/register', [RespondentAuthController::class, 'showRegister'])->name('register');
Route::post('/register', [RespondentAuthController::class, 'register'])->name('register.post');
Route::post('/logout', [RespondentAuthController::class, 'logout'])->name('logout');

// ==========================================
// AUTH ADMIN (di luar middleware — wajib bisa diakses tanpa login)
// ==========================================
Route::prefix('admin')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'showLogin'])->name('admin.login');
    Route::post('/login', [AdminAuthController::class, 'login'])->name('admin.login.post');
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');
});

// ==========================================
// AREA RESPONDEN (harus login sebagai responden)
// ==========================================
Route::middleware(['auth.respondent'])->group(function () {
    Route::get('/beranda', [HomeController::class, 'index'])->name('respondent.home');
    Route::get('/profil', [ProfileController::class, 'index'])->name('respondent.profile');
    Route::get('/konsultasi', [ConsultationController::class, 'index'])->name('respondent.consultation');

    // Buku Kerja - Pre Test
    Route::get('/buku-kerja/pre-test', [TestController::class, 'showPreTest'])->name('respondent.pretest');
    Route::post('/buku-kerja/pre-test', [TestController::class, 'submitPreTest'])->name('respondent.pretest.submit');

    // Buku Kerja - Post Test
    Route::get('/buku-kerja/post-test', [TestController::class, 'showPostTest'])->name('respondent.posttest');
    Route::post('/buku-kerja/post-test', [TestController::class, 'submitPostTest'])->name('respondent.posttest.submit');

    // Auto save jawaban
    Route::post('/buku-kerja/autosave', [TestController::class, 'autoSave'])->name('respondent.autosave');

    // Materi
    Route::get('/materi', [MaterialController::class, 'index'])->name('respondent.material');
    Route::post('/materi/{material}/baca', [MaterialController::class, 'markAsRead'])->name('respondent.material.read');
});

// ==========================================
// AREA ADMIN (harus login sebagai admin)
// ==========================================
Route::prefix('admin')->middleware(['auth', 'auth.admin'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

    // Responden
    Route::get('/responden', [RespondentController::class, 'index'])->name('admin.respondents');

    // Hasil Test
    Route::get('/hasil-test', [RespondentController::class, 'hasilTest'])->name('admin.hasil-test');

    // Soal
    Route::resource('soal', QuestionController::class)->names('admin.questions');

    // Materi
    Route::resource('materi', AdminMaterialController::class)->names('admin.materials');

    // Dokter
    Route::resource('dokter', DoctorController::class)->names('admin.doctors');

    // Export
    Route::get('/export/excel', [ExportController::class, 'exportExcel'])->name('admin.export.excel');
    Route::get('/export/csv', [ExportController::class, 'exportCsv'])->name('admin.export.csv');
});