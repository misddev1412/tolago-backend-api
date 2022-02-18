<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use App\Http\Requests\CreateCommentRequest;
use App\Jobs\Comment\ProcessCreateComment;
use App\Helpers\Response;
use App\Enum\HttpStatusCode;
use Gate;

class CommentController extends Controller
{
    //structures for comments model
    protected $comment;
    protected $post;
    protected $user;
    
    public function __construct(Comment $comment, Post $post, User $user)
    {
        $this->comment = $comment;
        $this->post = $post;
        $this->user = $user;
    }

    //create comment
    public function store(CreateCommentRequest $request)
    {
        if (Gate::forUser(Auth::guard('api')->user())->denies('create-comment')) {
            return Response::generateResponse(HttpStatusCode::FORBIDDEN, '', []);
        }

        $post = $this->post->findOrFail($request->post_id);

        if (!$post) {
            return Response::generateResponse(HttpStatusCode::NOT_FOUND, __('Not Found'), []);
        }

        $data = $request->only('comment', 'commentable_id', 'commentable_type');
        $data['user_id']        = Auth::guard('api')->user()->id;

        ProcessCreateComment::dispatch($data);

        return Response::generateResponse(HttpStatusCode::CREATED, '', []);
    }
}
