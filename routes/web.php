<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReceiptController;
use App\Http\Controllers\RecommendationController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SimulatorController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\WhatsAppWebhookController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;

Route::get('/', fn () => view(View::exists('landing.index') ? 'landing.index' : 'app.welcome'))->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/masuk', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/masuk', [AuthController::class, 'login'])->middleware('throttle:20,1');
    Route::get('/daftar', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/daftar', [AuthController::class, 'register'])->middleware('throttle:10,1');
});

Route::post('/demo', [AuthController::class, 'demo'])->middleware('throttle:6,1')->name('demo');
Route::post('/keluar', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::post('/webhook/wa/{secret}', WhatsAppWebhookController::class)->middleware('throttle:120,1')->name('webhook.wa');

Route::middleware(['auth', 'usaha'])->group(function () {
    Route::get('/dasbor', DashboardController::class)->name('dashboard');

    Route::get('/nota', [ReceiptController::class, 'index'])->name('receipts.index');
    Route::get('/nota/unggah', [ReceiptController::class, 'create'])->name('receipts.create');
    Route::post('/nota', [ReceiptController::class, 'store'])->name('receipts.store');
    Route::post('/nota/contoh/{sample}', [ReceiptController::class, 'storeSample'])->where('sample', '[A-Za-z0-9_.-]+')->name('receipts.sample');
    Route::get('/nota/contoh/{sample}/gambar', [ReceiptController::class, 'sampleImage'])->where('sample', '[A-Za-z0-9_.-]+')->name('receipts.sample-image');
    Route::get('/nota/{receipt}', [ReceiptController::class, 'show'])->name('receipts.show');
    Route::get('/nota/{receipt}/status', [ReceiptController::class, 'status'])->name('receipts.status');
    Route::get('/nota/{receipt}/foto', [ReceiptController::class, 'image'])->name('receipts.image');
    Route::put('/nota/{receipt}', [ReceiptController::class, 'update'])->name('receipts.update');
    Route::delete('/nota/{receipt}', [ReceiptController::class, 'destroy'])->name('receipts.destroy');

    Route::get('/rekomendasi', [RecommendationController::class, 'index'])->name('recommendations.index');
    Route::post('/rekomendasi/draf', [RecommendationController::class, 'draft'])->middleware('throttle:20,1')->name('recommendations.draft');
    Route::post('/rekomendasi/kirim', [RecommendationController::class, 'send'])->name('recommendations.send');

    Route::get('/pemasok', SupplierController::class)->name('suppliers.index');
    Route::get('/laporan', ReportController::class)->name('report');

    Route::get('/simulator-wa', [SimulatorController::class, 'index'])->name('simulator.index');
    Route::post('/simulator-wa', [SimulatorController::class, 'send'])->name('simulator.send');
    Route::post('/simulator-wa/permintaan/{supplyRequest}', [SimulatorController::class, 'reply'])->name('simulator.reply');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('index');
    Route::put('/pemasok/{supplier}', [AdminController::class, 'verify'])->name('suppliers.verify');
    Route::put('/kuota', [AdminController::class, 'quotas'])->name('quotas');
});
