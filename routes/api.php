<?php

use App\Http\Controllers\Api\WebhookController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Rotas de Webhook (sem autenticação para permitir chamadas externas)
Route::prefix('webhooks')->group(function () {
    Route::post('/test', [WebhookController::class, 'test']);
    Route::post('/{action}', [WebhookController::class, 'handle'])
        ->where('action', '[a-z-]+');
});

// Rotas protegidas por autenticação
Route::middleware('auth:sanctum')->group(function () {
    // Aqui podem ser adicionadas outras rotas de API que precisam de autenticação
});


use App\Http\Controllers\Api\BonusController;

// Rotas protegidas por autenticação
Route::middleware('auth:sanctum')->group(function () {
    // Aqui podem ser adicionadas outras rotas de API que precisam de autenticação
});

// Rotas de bônus para inscrições (temporariamente sem auth para teste)
Route::post("/inscriptions/{inscription}/bonuses", [BonusController::class, "store"]);
Route::get("/inscriptions/{inscription}/bonuses/{bonus}", [BonusController::class, "show"]);
Route::put("/inscriptions/{inscription}/bonuses/{bonus}", [BonusController::class, "update"]);
Route::delete("/inscriptions/{inscription}/bonuses/{bonus}", [BonusController::class, "destroy"]);




use App\Http\Controllers\Api\AchievementTypeController;

Route::apiResource("achievement-types", AchievementTypeController::class);


