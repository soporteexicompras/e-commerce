<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Home: muestra el catálogo principal de Exicompras
Route::group(['middleware' => ['web']], function () {
    Route::get('/', '\Aimeos\Shop\Controller\CatalogController@homeAction')->name('aimeos_home');
});

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

require __DIR__.'/auth.php';
