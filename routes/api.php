<?php

use App\Http\Controllers\DemandasController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::controller(DemandasController::class)->group(function () {
    Route::get('/demandas', 'getDemandas'); // Rota para listar demandas
    Route::get('/demandas/{id}', 'getDemanda'); // Rota para obter uma demanda específica

    Route::post('/demandas', 'createDemanda'); // Rota para criar uma demanda

    Route::put('/demandas/{id}', 'updateDemanda'); // Rota para atualizar uma demanda
    
    Route::delete('/demandas/{id}', 'deleteDemanda'); // Rota para deletar uma demanda
});
