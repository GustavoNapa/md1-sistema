<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\InscriptionController;
use App\Http\Controllers\ImportController;
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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Rotas protegidas por autenticação
Route::middleware('auth')->group(function () {
    Route::resource('clients', ClientController::class);
    Route::resource('inscriptions', InscriptionController::class);
    
    // Rotas de importação
    Route::get('/import', [ImportController::class, 'index'])->name('import.index');
    Route::post('/import/clients', [ImportController::class, 'clients'])->name('import.clients');
    Route::get('/import/template', [ImportController::class, 'downloadTemplate'])->name('import.template');
    
    // Rota para dashboard principal
    Route::get('/dashboard', function () {
        return redirect()->route('clients.index');
    })->name('dashboard');
});

