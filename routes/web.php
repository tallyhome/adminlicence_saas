<?php

use App\Http\Controllers\Admin\AdminAuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route principale qui redirige vers la page de connexion admin
Route::get('/', function () {
    return redirect()->route('admin.login');
});

// Définition explicite de la route 'login' qui redirige vers 'admin.login'
Route::get('/login', function () {
    return redirect()->route('admin.login');
})->name('login');

// Inclure les routes admin
require __DIR__.'/admin.php';
