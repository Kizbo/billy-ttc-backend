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
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

/**
 * Return the game state.
 */
Route::get("/", [GameController::class, "game"]);

/**
 * Clear the board and update win scores if there was a winner in last round
 */
Route::post("/restart", [GameController::class, "restart_game"]);

/**
 * Reset game, removing scores and resetting everything to default values
 */
Route::delete("/", [GameController::class, "reset_game"]);

/**
 * Update board on given coordinates with symbol
 */
Route::post("/{piece}", [GameController::class, "update_board_square"]);
