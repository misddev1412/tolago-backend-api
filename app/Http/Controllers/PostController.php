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
use App\Jobs\Post\ProcessPostTranslation;
use Gate;
use Lang;
use App\Services\MediaService;
use App\Repositories\PostRepositoryInterface;
use App\Helpers\Helper;


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

        $file = $request->file('image');
        $fileName = Storage::disk('local')->put('tmp/images', $file);

        if (!Storage::disk('local')->exists($fileName)) {
            return Response::generateResponse(HttpStatusCode::INTERNAL_SERVER_ERROR, '', []);
        }

        ProcessCreatePost::dispatch(Auth::guard('api')->user()->id, $request->except('image'), Lang::getLocale(), $fileName, Helper::getClientIps(), Helper::getClientAgent());

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


        if (!Auth::guard('api')->user()->role == 'admin' || !Gate::forUser(Auth::guard('api')->user())->denies('create-system-post', $post)) {
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

    
}
