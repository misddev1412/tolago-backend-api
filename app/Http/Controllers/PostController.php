<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Helpers\Response;
use App\Enum\HttpStatusCode;
use App\Http\Requests\Post\CreatePostRequest;
use App\Http\Requests\Post\UpdatePostRequest;
use App\Http\Requests\Translation\TranslationPostRequest;
use Auth;
use Illuminate\Support\Facades\Storage;
use App\Jobs\Post\ProcessCreatePost;
use App\Jobs\Post\ProcessUpdatePost;
use App\Jobs\Post\ProcessDeletePost;
use App\Jobs\Post\ProcessLikePost;
use App\Jobs\Post\ProcessCommentPost;
use App\Jobs\Post\ProcessPostTranslation;
use App\Jobs\Post\ProcessUnlikePost;
use Gate;
use Lang;
use App\Services\MediaService;
use App\Repositories\Post\PostRepositoryInterface;
use App\Helpers\Helper;
use App\Models\Comment;

class PostController extends Controller
{

    //init post variables
    protected $post;
    protected $postRepository;

    //structed controller with post model
    public function __construct(Post $post, PostRepositoryInterface $postRepository)
    {
        $this->post = $post;
        $this->postRepository = $postRepository;
    }

    //resources index scope paginateWithAll return generateResponse
    public function index(Request $request)
    {
        if (Gate::forUser(Auth::guard('api')->user())->allows('view-all-post')) {
            $posts = $this->postRepository->index($request, true);
        } else {
            $posts = $this->postRepository->index($request);
        }

        return Response::generateResponse(HttpStatusCode::OK, '', $posts);
    }

    //autocomplete scope paginateWithAll return generateResponse
    public function autocomplete(Request $request)
    {
        $posts = $this->postRepository->autocomplete($request);

        return Response::generateResponse(HttpStatusCode::OK, '', $posts);
    }

    //resources show scope findOrFailWithAll return generateResponse
    public function show($id)
    {
        $post = $this->postRepository->findOrFailWithAll($id);

        if (Gate::forUser(Auth::guard('api')->user())->denies('view-post', $post)) {
            return Response::generateResponse(HttpStatusCode::FORBIDDEN, '', []);
        }

        return Response::generateResponse(HttpStatusCode::OK, '', $post);
    }

    //resources store scope create return generateResponse
    public function store(CreatePostRequest $request)
    {

        if (Gate::forUser(Auth::guard('api')->user())->denies('create-post')) {
            return Response::generateResponse(HttpStatusCode::FORBIDDEN, '', []);
        }

        $fileNames = [];

        if ($request->file('files')) {
            // dd($request->file('files'));
            foreach($request->file('files') as $file) {
                $fileNames[] = Storage::disk('local')->put('tmp/files', $file);
            }
        }

        ProcessCreatePost::dispatch(Auth::guard('api')->user()->id, $request->except('files'), Lang::getLocale(), $fileNames, Helper::getClientIps(), Helper::getClientAgent());

        return Response::generateResponse(HttpStatusCode::CREATED, '', []);
    }

    //function translation controller
    public function translation($id, TranslationPostRequest $request)
    {
        ProcessPostTranslation::dispatch($id, $request->except('_token'));
        return Response::generateResponse(HttpStatusCode::OK, '', []);
    }

    //resources update scope findOrFailWithAll return generateResponse
    public function update(UpdatePostRequest $request, $id)
    {
        $fileName = null;
        $post = $this->post->findOrFail($id);
        if (Gate::forUser(Auth::guard('api')->user())->denies('update-post', $post)) {
            return Response::generateResponse(HttpStatusCode::FORBIDDEN, '', []);
        }

        if ($request->file('image')) {
            $file = $request->file('image');
            $fileName = Storage::disk('local')->put('tmp/images', $file);
        }


        if (!Auth::guard('api')->user()->hasRole('admin') || !Gate::forUser(Auth::guard('api')->user())->denies('create-system-post', $post)) {
            $request = $request->except('type', 'image');
        } else {
            $request = $request->except('image');
        }

        ProcessUpdatePost::dispatch(Auth::guard('api')->user()->id, $request, $id, $fileName, Helper::getClientIps(), Helper::getClientAgent());

        return Response::generateResponse(HttpStatusCode::OK, '', []);
    }

    //resources delete scope findOrFailWithAll return generateResponse
    public function destroy($id)
    {
        $post = $this->post->findOrFail($id);
        if (Gate::forUser(Auth::guard('api')->user())->denies('delete-post', $post)) {
            return Response::generateResponse(HttpStatusCode::FORBIDDEN, '', []);
        }

        ProcessDeletePost::dispatch($id, Auth::guard('api')->user()->id, Helper::getClientIps(), Helper::getClientAgent());

        return Response::generateResponse(HttpStatusCode::OK, '', []);
    }


    public function like(Request $request, $id)
    {
        $post = $this->post->findOrFail($id);

        ProcessLikePost::dispatch($id, Auth::guard('api')->user()->id, $request->only('like_type'), Helper::getClientIps(), Helper::getClientAgent());

        return Response::generateResponse(HttpStatusCode::OK, '', []);
    }

    public function unlike(Request $request, $id)
    {
        $post = $this->post->findOrFail($id);

        ProcessUnlikePost::dispatch($id, Auth::guard('api')->user()->id, $request->only('like_type'), Helper::getClientIps(), Helper::getClientAgent());

        return Response::generateResponse(HttpStatusCode::OK, '', []);
    }

    public function comment(Request $request, $id) {
        $post = $this->post->findOrFail($id);

        ProcessCommentPost::dispatch($id, Auth::guard('api')->user()->id, $request->only('comment_text'), Helper::getClientIps(), Helper::getClientAgent());

        return Response::generateResponse(HttpStatusCode::OK, '', []);
    }

    public function commentList(Request $request, $id) {
        $comments  = Comment::where('commentable_type', 'App\Models\Post')
            ->where('commentable_id', $id)
            ->where('parent_id', 0)
            ->with('user.image')
            ->orderBy('created_at', 'desc')->paginate($request->get('limit', 10), ['*'], 'page', $request->get('page', 1));
        return Response::generateResponse(HttpStatusCode::OK, '', $comments);
    }


}
