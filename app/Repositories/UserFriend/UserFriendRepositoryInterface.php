<?php

namespace App\Repositories\UserFriend;
use Illuminate\Http\Request;

interface UserFriendRepositoryInterface
{

    public function findOrFail($id);
    public function index(Request $request);
    public function newest($limit = 5);
    public function myFriends($userId, $page = 1);

}
