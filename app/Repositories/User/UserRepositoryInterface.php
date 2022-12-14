<?php

namespace App\Repositories\User;
use Illuminate\Http\Request;

interface UserRepositoryInterface
{

    public function findOrFail($id);
    public function index(Request $request);
    public function newest($limit = 5);

}
