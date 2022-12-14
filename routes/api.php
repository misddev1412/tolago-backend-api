<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\AuthenController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\FriendController;
use App\Http\Controllers\EventStreamController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\HotelController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SocialAccountController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\UtilityController;
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
    Route::post('post/like/{id}', [PostController::class, 'like']);
    Route::delete('post/unlike/{id}', [PostController::class, 'unlike']);
    Route::post('post/comment/{id}', [PostController::class, 'comment']);
    Route::get('post/comment/{id}', [PostController::class, 'commentList']);
    Route::apiResource('post', PostController::class);
    Route::apiResource('hotel', HotelController::class);
    Route::apiResource('room', RoomController::class);
    Route::apiResource('utility', UtilityController::class);
    Route::put('room/{id}/price', [RoomController::class, 'updatePrice']);

    Route::apiResource('category', CategoryController::class);

    Route::get('chat', [ChatController::class, 'chat']);
    Route::get('chat/{recipient_id}', [ChatController::class, 'chatWithRecipient']);
    Route::post('chat', [ChatController::class, 'store']);

    Route::get('me', [AuthenController::class, 'me']);
    Route::post('refresh-token', [AuthenController::class, 'refresh']);
    Route::post('logout', [AuthenController::class, 'logout']);

    Route::post('friend/invite/{id}', [FriendController::class, 'sendInvite']);
    Route::post('friend/accept/{id}', [FriendController::class, 'acceptInvite']);
    Route::get('friend/my-friend', [FriendController::class, 'myFriends']);

    Route::get('auth/create-qr-url', [AuthenController::class, 'createQrUrl']);
    Route::post('auth/enable-qr-url', [AuthenController::class, 'enableQrCode']);
    Route::put('auth/update-profile', [AuthenController::class, 'updateProfile']);

    Route::get('address/lat-lang-from-address', [AddressController::class, 'getLatAndLangFromAddress']);

    Route::get('user/newest', [UserController::class, 'newest']);
});

Route::group(['prefix' => 'v1/admin', 'middleware' => ['auth:api', 'admin']], function () {
    Route::apiResource('role', RoleController::class);
    Route::apiResource('user', UserController::class);
});

//routes v1 for AuthenController
Route::prefix('v1')->group(function () {
    Route::post('login', [AuthenController::class, 'login']);
    Route::post('signup', [AuthenController::class, 'signup']);
    Route::get('/event-stream-notification', [EventStreamController::class, 'getEventStreamNotification']);

    Route::get('login/{social}', [SocialAccountController::class, 'redirectToProvider']);
    Route::get('login/{social}/callback', [SocialAccountController::class, 'handleProviderCallback']);

});
