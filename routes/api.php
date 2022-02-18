<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\AuthenController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\FriendController;
use App\Http\Controllers\EventStreamController;
use App\Http\Controllers\ChatController;
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


//routes v1 for PostController
Route::prefix('v1')->middleware('auth:api')->group(function () {
    Route::post('broadcasting/auth', [AuthenController::class, 'broadcastingAuth']);

    Route::post('post/translation/{id}', [PostController::class, 'translation']);
    Route::get('post/search/suggestion', [PostController::class, 'autocomplete']);
    Route::apiResource('post', PostController::class);

    Route::apiResource('category', CategoryController::class);

    Route::get('chat', [ChatController::class, 'chat']);
    Route::post('chat', [ChatController::class, 'store']);

    Route::get('me', [AuthenController::class, 'me']);
    Route::post('refresh-token', [AuthenController::class, 'refresh']);
    Route::post('logout', [AuthenController::class, 'logout']);

    Route::post('friend/invite/{id}', [FriendController::class, 'sendInvite']);
});

//routes v1 for AuthenController
Route::prefix('v1')->group(function () {
    Route::post('login', [AuthenController::class, 'login']);
    Route::post('signup', [AuthenController::class, 'signup']);
    Route::get('/event-stream-notification', [EventStreamController::class, 'getEventStreamNotification']);

});