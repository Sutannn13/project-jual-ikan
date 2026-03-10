Route::post('/produks', [App\Http\Controllers\ProdukController::class, 'store']);
Route::get('/produks/{id}', [App\Http\Controllers\ProdukController::class, 'show']);