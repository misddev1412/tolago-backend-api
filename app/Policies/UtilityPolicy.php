<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Utility;
use Illuminate\Auth\Access\HandlesAuthorization;

class UtilityPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return true;
    }

    public function view(User $user, Utility $utility)
    {
        return $utility->status == 1 || $user->hasRole('admin') || $user->can('view-private-room-permission') || $user->id == $utility->user_id;
    }

    public function create(User $user)
    {
        return $user->can('create-room-permission') || $user->hasRole('admin');
    }

    public function update(User $user, Utility $utility)
    {
        return ($user->id === $utility->user_id && $user->can('update-room-permission')) || $user->hasRole('admin');
    }

    public function delete(User $user, Utility $utility)
    {
        return ($user->id === $utility->user_id && $user->can('delete-room-permission')) || $user->hasRole('admin');
    }

    public function restore(User $user, Utility $utility)
    {
        return ($user->id === $utility->user_id && $user->can('restore-room-permission')) || $user->hasRole('admin');
    }

    public function forceDelete(User $user, Utility $utility)
    {
        return ($user->id === $utility->user_id && $user->can('force-delete-room-permission')) || $user->hasRole('admin');
    }
}
