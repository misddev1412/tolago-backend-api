<?php

use Illuminate\Support\Facades\Broadcast;
use App\Broadcasting\UserChannel;
use App\Broadcasting\UserGlobal;
use Illuminate\Broadcasting\PrivateChannel;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

// Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
//     return (int) $user->id === (int) $id;
// });
// Broadcast::channel('user.{user}', UserChannel::class);



Broadcast::channel('chat-private.{id}.{userId}', UserChannel::class);
Broadcast::channel('global.{id}', UserGlobal::class);

