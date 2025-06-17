<?php

use App\Http\Controllers\PrintPenjualanController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/admin');
});
// Route::get('/penjualan/{penjualan}/print', [PrintPenjualanController::class, 'print'])->name('penjualan.print');
Route::get('/penjualan/{id}/cetak', [\App\Http\Controllers\PrintPenjualanController::class, 'cetak'])->name('penjualan.cetak');

