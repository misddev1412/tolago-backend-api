<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\Role\RoleRepositoryInterface;

class RoleController extends Controller
{
    protected $role;
    protected $user;
    
    //construct for role model
    public function __construct(RoleRepositoryInterface $role)
    {
        $this->role = $role;
    }

    public function index(Request $request)
    {
        return $this->role->index($request);
    }
}
