<?php

namespace App\Repositories\Post;

/**
 * Interface GalleryRepositoryInterface
 * @package App\Repositories
 */
use App\Models\Post;

use App\Repositories\Post\PostRepositoryInterface;
use Cache;
use App\Services\PostService;
use App\Jobs\Post\ProcessCachePost;
use Illuminate\Http\Request;
class PostRepository implements PostRepositoryInterface
{

    private $post;

    //constructor with Post model
    public function __construct(Post $post)
    {
        $this->post = $post;
    }

    /**
     * @param $id
     * @return mixed
     */
    public function findOrFailWithAll($id)
    {
        $data = Cache::get("posts.{$id}");
        if (!$data) {
            $data = $this->post->findOrFailWithAll($id);
            $postService = new PostService($data);
            $postService->cacheSinglePost();
        }
        $data->total_likes = $this->countGroupLikeType($data->id);
        return $data;
    }

    //index function
    public function index(Request $request, $viewFull = false)
    {
        $perPage    = $request->get('per_page', 10);
        $page       = $request->get('page', 1);

        if ($request->title) {
            $posts = $this->post->search($request->title, function ($search, string $query, array $options) use ($perPage, $page, $viewFull) {
                $options = [
                    'sort' => ['created_at:desc'],
                    'limit' => $perPage,
                    'offset' => $perPage * ($page - 1),
                ];

                if (!$viewFull) {
                    $options['filter'] = ['status = 1'];
                }

                return $search->search($query, $options);
            })->where('user_id', 1)->where('main_id', 0)->paginate($perPage);
        } else {
            $posts = $this->post->where('main_id', 0)->orderBy('created_at', 'desc')->paginate($perPage);
        }
        $posts->load('user.image');
        $posts->load('images');
        $posts->load('postChildren.video.images', 'postChildren.image');
        $posts->load('comments');
        $posts->load('postCount');
        $posts->map(function ($post) {
            $post->total_likes = $this->countGroupLikeType($post->id);
            return $post;
        });
        $posts->load('translationCurrentLanguage');
        return $posts;
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

    private function countGroupLikeType($postId) {
        return $this->post->countGroupLikeType($postId);
    }
}
