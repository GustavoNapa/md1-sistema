<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\InscriptionController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Auth;
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

// Authentication Routes
Auth::routes();

Route::middleware('auth')->group(function () {
    Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    
    // Rotas de Clientes
    Route::resource('clients', ClientController::class);
    
    // Rotas de Produtos
    Route::resource('products', ProductController::class);
    
    // Rotas de Inscrições
    Route::resource('inscriptions', InscriptionController::class);
    
    // Rotas de Importação
    Route::get('/import', [ImportController::class, 'index'])->name('import.index');
    Route::post('/import', [ImportController::class, 'import'])->name('import.process');
    Route::get('/import/template', [ImportController::class, 'downloadTemplate'])->name('import.template');
    
    // Rotas de Documentos
    Route::prefix('inscriptions/{inscription}/documents')->name('documents.')->group(function () {
        Route::get('/', [DocumentController::class, 'index'])->name('index');
        Route::get('/create', [DocumentController::class, 'create'])->name('create');
        Route::post('/upload', [DocumentController::class, 'upload'])->name('upload');
        Route::get('/{mediaId}/download', [DocumentController::class, 'download'])->name('download');
        Route::delete('/{mediaId}', [DocumentController::class, 'destroy'])->name('destroy');
    });
    
    // Rotas para registros relacionados às inscrições
    Route::post('/preceptor-records', [App\Http\Controllers\PreceptorRecordController::class, 'store'])->name('preceptor-records.store');
    Route::delete('/preceptor-records/{preceptorRecord}', [App\Http\Controllers\PreceptorRecordController::class, 'destroy'])->name('preceptor-records.destroy');
    
    Route::post('/payments', [App\Http\Controllers\PaymentController::class, 'store'])->name('payments.store');
    Route::delete('/payments/{payment}', [App\Http\Controllers\PaymentController::class, 'destroy'])->name('payments.destroy');
    
    Route::post('/sessions', [App\Http\Controllers\SessionController::class, 'store'])->name('sessions.store');
    Route::delete('/sessions/{session}', [App\Http\Controllers\SessionController::class, 'destroy'])->name('sessions.destroy');
    
    Route::post('/diagnostics', [App\Http\Controllers\DiagnosticController::class, 'store'])->name('diagnostics.store');
    Route::delete('/diagnostics/{diagnostic}', [App\Http\Controllers\DiagnosticController::class, 'destroy'])->name('diagnostics.destroy');
    
    Route::post('/achievements', [App\Http\Controllers\AchievementController::class, 'store'])->name('achievements.store');
    Route::delete('/achievements/{achievement}', [App\Http\Controllers\AchievementController::class, 'destroy'])->name('achievements.destroy');
    
    Route::post('/follow-ups', [App\Http\Controllers\FollowUpController::class, 'store'])->name('follow-ups.store');
    Route::delete('/follow-ups/{followUp}', [App\Http\Controllers\FollowUpController::class, 'destroy'])->name('follow-ups.destroy');
    
    // Rota para dashboard principal
    Route::get('/dashboard', function () {
        return redirect()->route('clients.index');
    })->name('dashboard');
});