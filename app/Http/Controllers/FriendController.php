<?php

namespace App\Http\Controllers;

use App\Jobs\UserFriend\ProcessAcceptFriend;
use App\Repositories\UserFriend\UserFriendRepositoryInterface;
use Illuminate\Http\Request;
use App\Models\UserFriend;
use App\Helpers\Response;
use App\Enum\HttpStatusCode;
use App\Jobs\UserFriend\ProcessInviteFriend;
use Auth;
use App\Models\User;

class FriendController extends Controller
{
    //construcrt friend controller with UserFriend model
    public function __construct(UserFriend $userFriend, UserFriendRepositoryInterface $userFriendRepository)
    {
        $this->userFriend = $userFriend;
        $this->userFriendRepository = $userFriendRepository;
    }



    //get paginate friends of current user
    public function getFriends(Request $request)
    {
        $friends = $this->userFriend->where('user_id', $request->user_id)->accepted()->paginate(10);
        return Response::generateResponse(HttpStatusCode::OK, '', $friends);
    }

    //send invite friend
    public function sendInvite($friendId)
    {
        if ($friendId == Auth::guard('api')->user()->id) {
            return Response::generateResponse(HttpStatusCode::BAD_REQUEST, '', 'You can not invite yourself');
        }

        $friend = $this->userFriend->where('user_id', Auth::guard('api')->user()->id)->where('friend_id', $friendId)->first();
        if ($friend) {
            return Response::generateResponse(HttpStatusCode::CONFLICT, '', []);
        }
        if (!User::where('id', $friendId)->first()) {
            return Response::generateResponse(HttpStatusCode::NOT_FOUND, '', []);
        }

        ProcessInviteFriend::dispatch(Auth::guard('api')->user()->id, $friendId, UserFriend::PENDING);
        return Response::generateResponse(HttpStatusCode::CREATED, '', []);
    }

    //accept invite friend
    public function acceptInvite($friendId)
    {
        $friend = $this->userFriend->where('user_id', Auth::guard('api')->user()->id)->where('friend_id', $friendId)->first();
        if (!$friend) {
            return Response::generateResponse(HttpStatusCode::NOT_FOUND, '', []);
        }
        if ($friend->status == UserFriend::PENDING) {
            ProcessAcceptFriend::dispatch(Auth::guard('api')->user()->id, $friendId);
            return Response::generateResponse(HttpStatusCode::OK, '', []);
        }
        return Response::generateResponse(HttpStatusCode::CONFLICT, '', []);
    }

    public function myFriends()
    {
        $friends = $this->userFriendRepository->myFriends(Auth::guard('api')->user()->id);
        return Response::generateResponse(HttpStatusCode::OK, '', $friends);
    }
}
