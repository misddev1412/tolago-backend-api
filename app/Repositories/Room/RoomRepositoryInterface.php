<?php 

namespace App\Repositories\Room;
use Illuminate\Http\Request;

interface RoomRepositoryInterface
{

    public function findOrFailWithAll($id);
    public function index(Request $request);

}