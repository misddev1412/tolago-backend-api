<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use App\Models\Room;

class RoomPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(User $user)
    {
        return true;
    }

    public function view(User $user, Room $room)
    {
        return $room->status == 1 || $user->hasRole('admin') || $user->can('view-private-room-permission') || $user->id == $room->user_id;
    }

    public function create(User $user)
    {
        return $user->can('create-room-permission') || $user->hasRole('admin');
    }

    public function update(User $user, Room $room)
    {
        return ($user->id === $room->user_id && $user->can('update-room-permission')) || $user->hasRole('admin');
    }

    public function delete(User $user, Room $room)
    {
        return ($user->id === $room->user_id && $user->can('delete-room-permission')) || $user->hasRole('admin');
    }

    public function restore(User $user, Room $room)
    {
        return ($user->id === $room->user_id && $user->can('restore-room-permission')) || $user->hasRole('admin');
    }

    public function forceDelete(User $user, Room $room)
    {
        return ($user->id === $room->user_id && $user->can('force-delete-room-permission')) || $user->hasRole('admin');
    }
}
