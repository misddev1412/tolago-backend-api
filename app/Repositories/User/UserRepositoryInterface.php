<?php 

namespace App\Repositories\User;
use Illuminate\Http\Request;

interface UserRepositoryInterface
{

    public function findOrFail($id);
    public function index(Request $request);

}