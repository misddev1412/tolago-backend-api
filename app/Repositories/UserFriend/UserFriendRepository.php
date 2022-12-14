<?php

namespace App\Repositories\UserFriend;

/**
 * Interface GalleryRepositoryInterface
 * @package App\Repositories
 */
use App\Models\User;

use App\Models\UserFriend;
use Cache;
use App\Services\UserService;

use Illuminate\Http\Request;
class UserFriendRepository implements UserFriendRepositoryInterface
{

    private $user;

    //constructor with Post model
    public function __construct(User $user, UserFriend $userFriend)
    {
        $this->user = $user;
        $this->userFriend = $userFriend;
    }


    /**
     * @param $id
     * @return mixed
     */
    public function findOrFail($id)
    {
        $data = Cache::get("users.{$id}");
        if (!$data) {
            $data = $this->user->findOrFail($id);
            $userService = new UserService($data);
            $userService->cacheSinglePost();
        }
        return $data;
    }

    //index function
    public function index(Request $request)
    {
        $perPage    = $request->get('per_page', 10);
        $page       = $request->get('page', 1);

        if ($request->keyword || $request->status) {
            $users = $this->user->search($request->keyword, function ($search, string $query, array $options) use ($perPage, $page, $request) {
                $options = [
                    'sort' => ['created_at:desc'],
                    'limit' => $perPage,
                    'offset' => $perPage * ($page - 1),
                ];

                if ($request->status) {
                    $options['filter'] = ['status = ' . $request->status];
                }

                return $search->search($query, $options);
            })->paginate($perPage);
        } else {
            $users = $this->user->orderBy('created_at', 'desc')->paginate($perPage);
        }

        return $users;
    }

    public function store($dataCreate)
    {
        $user = $this->user->create($dataCreate);

        return $user;
    }

    //auto complete function
    public function autoComplete(Request $request)
    {
        $perPage    = $request->get('per_page', 5);
        $page       = $request->get('page', 1);

        if ($request->title) {
            $posts = $this->getDataSearchSugesstion($request->title, $perPage, $page);
        } else {
            $posts = $this->post->orderBy('created_at', 'desc')->paginate($perPage);
        }

        $data = $posts->pluck('title')->unique()->toArray();
        for ($i = 0; $i < $perPage; $i++) {
            if (count($data) < $perPage) {
                // dd($this->getDataSearchSugesstion($request->title, $perPage, $page + 1));
                $dataMerge = $this->getDataSearchSugesstion($request->title, $perPage, $page + 1)->pluck('title')->unique()->toArray();
                $data = array_merge($data, $dataMerge);
            } else {
                break;
            }
        }

        return array_unique($data);
    }

    private function getDataSearchSugesstion($title, $perPage, $page) {
        $posts = $this->post->search($title, function ($search, string $query, array $options) use ($perPage, $page) {
            $options = [
                'sort' => ['created_at:desc'],
                'limit' => $perPage,
                'offset' => $perPage * ($page - 1),
            ];
            return $search->search($query, $options);
        })->paginate($perPage);

        return $posts;
    }

    public function newest($limit = 5)
    {
        $users = $this->user->search('', function($search, string $query) use ($limit) {
            $options = [
                'sort' => ['created_at:desc'],
                'limit' => $limit,
            ];



            return $search->search($query, $options);
        })
        ->get();
        $users = $users->load('image')->take($limit);
        return $users;
    }

    public function myFriends($userId, $page = 1)
    {
        $limit = 10;
        $userFriends = $this->userFriend->search('', function($search, string $query) use ($limit, $userId, $page) {
            $options = [
                'sort' => ['created_at:desc'],
                'limit' => $limit,
                'filter' => ['friend_id = ' . $userId],
                'offset' => $limit * ($page - 1),
            ];


            return $search->search($query, $options);
        })
        ->paginate($limit);
        $userFriends->load('user.image');
        return $userFriends;
    }
}
