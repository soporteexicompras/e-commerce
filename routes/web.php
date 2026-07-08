<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Home (/) se registra automáticamente desde
// vendor/aimeos/aimeos-laravel/routes/aimeos.php (Route::match GET|POST '/' as 'aimeos_home').
// No redeclararla aquí: route:cache falla con "Another route has already been assigned name [aimeos_home]".

// Redirección si alguien accede a /dashboard directamente
Route::get('/dashboard', function () {
    $user = auth()->user();
    if (!$user) return redirect('/login');
    if ($user->superuser || $user->hasAimeosGroup('editor')) return redirect('/admin');
    return redirect('/');
})->middleware('auth')->name('dashboard');

// Rutas de perfil: cambiadas a /profile/me para no colisionar con aimeos_shop_account
Route::middleware('auth')->group(function () {
    Route::get('/profile/me', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile/me', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile/me', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Favoritos / Wishlist (accesible para guests y autenticados)
Route::middleware(['web'])->prefix('favorites')->name('favorites.')->group(function () {
    Route::get('/',         [\App\Http\Controllers\FavoriteController::class, 'index'])->name('index');
    Route::post('/',        [\App\Http\Controllers\FavoriteController::class, 'store'])->name('store');
    Route::delete('/{product}', [\App\Http\Controllers\FavoriteController::class, 'destroy'])->name('destroy');
    Route::post('/sync',    [\App\Http\Controllers\FavoriteController::class, 'sync'])->middleware('auth')->name('sync');
});

require __DIR__.'/auth.php';
