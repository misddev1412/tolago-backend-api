<?php 

namespace App\Repositories\Post;
use Illuminate\Http\Request;

interface PostRepositoryInterface
{

    public function findOrFailWithAll($id);
    public function index(Request $request, $viewFull = false);

}