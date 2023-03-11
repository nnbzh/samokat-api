<?php

use App\Http\Controllers\GameController;
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

Route::post('game/start', [GameController::class, 'start']);
Route::get('game/status', [GameController::class, 'status']);
Route::put('game/status', [GameController::class, 'update']);
Route::post('game/promocode', [GameController::class, 'givePromoCode']);
