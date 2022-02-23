<?php 

namespace App\Repositories\Hotel;
use Illuminate\Http\Request;

interface HotelRepositoryInterface
{

    public function findOrFailWithAll($id);
    public function index(Request $request);

}