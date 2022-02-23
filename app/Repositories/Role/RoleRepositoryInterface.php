<?php 

namespace App\Repositories\Role;
use Illuminate\Http\Request;

interface RoleRepositoryInterface
{

    public function findOrFailWithAll($id);
    public function index(Request $request);

}