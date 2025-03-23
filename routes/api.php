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

Route::get('/', function() {
    return response()->json([
        'status' => 'success',
        'message' => 'API está funcionando corretamente',
        'timestamp' => now(),
        'version' => '1.0'
    ], 200);
}); 

Route::controller(DemandasController::class)->group(function () {
    
    Route::get('/demandas', 'getDemandas');
    Route::get('/demanda/{cod}', 'getDemanda');
    Route::post('/demanda', 'createDemanda');
    Route::put('/demanda/{cod}', [DemandasController::class, 'updateDemanda'])->middleware('cors'); 
    Route::delete('/demanda/{cod}', 'deleteDemanda');
});
