<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProdukController; // Biar lebih bersih

Route::get('/produks', [ProdukController::class, 'index']); // Rute baru buat Catalog
Route::post('/produks', [ProdukController::class, 'store']);
Route::get('/produks/{id}', [ProdukController::class, 'show']);
// Kalo butuh hapus via API nanti
Route::delete('/produks/{id}', [ProdukController::class, 'destroy']);