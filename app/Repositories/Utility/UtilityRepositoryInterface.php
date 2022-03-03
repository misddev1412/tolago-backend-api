<?php 

namespace App\Repositories\Utility;
use Illuminate\Http\Request;

interface UtilityRepositoryInterface
{

    public function findOrFailWithAll($id);
    public function index(Request $request);

}