<?php 

namespace App\Repositories\Room;

/**
 * Interface GalleryRepositoryInterface
 * @package App\Repositories
 */
use App\Models\Hotel;
use App\Models\Room;

use App\Repositories\Room\RoomRepositoryInterface;
use Cache;
use App\Services\RoomService;
use Illuminate\Http\Request;
class RoomRepository implements RoomRepositoryInterface
{

    private $room;

    //constructor with Post model
    public function __construct(Room $room)
    {
        $this->room = $room;
    }

    /**
     * @param $id
     * @return mixed
     */
    public function findOrFailWithAll($id)
    {
        $data = Cache::get("rooms.{$id}");
        if (!$data) {
            $data = $this->room->findOrFailWithAll($id);
            $roomService = new RoomService($data);
            $roomService->cacheSingleRoom();
        }
        return $data; 
    }

    //index function
    public function index(Request $request)
    {
        $perPage    = $request->get('per_page', 10);
        $page       = $request->get('page', 1);

        if ($request->keyword) {
            $rooms = $this->room->search($request->keyword, function ($search, string $query, array $options) use ($perPage, $page) {
                $options = [
                    'sort' => ['created_at:desc'],
                    'limit' => $perPage,
                    'offset' => $perPage * ($page - 1),
                ];

                return $search->search($query, $options);
            })->paginate($perPage);
        } else {
            $rooms = $this->room->orderBy('created_at', 'desc')->paginate($perPage);
        }

        $rooms->load('hotel');
        $rooms->load('translationCurrentLanguage');
        return $rooms;
    }

    //auto complete function
    public function autoComplete(Request $request)
    {
        $perPage    = $request->get('per_page', 5);
        $page       = $request->get('page', 1);

        if ($request->keyword) {
            $rooms = $this->getDataSearchSugesstion($request->keyword, $perPage, $page);
        } else {
            $rooms = $this->room->orderBy('created_at', 'desc')->paginate($perPage);
        }

        $data = $rooms->pluck('name')->unique()->toArray();
        for ($i = 0; $i < $perPage; $i++) {
            if (count($data) < $perPage) {
                // dd($this->getDataSearchSugesstion($request->title, $perPage, $page + 1));
                $dataMerge = $this->getDataSearchSugesstion($request->keyword, $perPage, $page + 1)->pluck('name')->unique()->toArray();
                $data = array_merge($data, $dataMerge);
            } else {
                break;
            }
        }

        return array_unique($data);
    }

    private function getDataSearchSugesstion($name, $perPage, $page) {
        $rooms = $this->room->search($name, function ($search, string $query, array $options) use ($perPage, $page) {
            $options = [
                'sort' => ['created_at:desc'],
                'limit' => $perPage,
                'offset' => $perPage * ($page - 1),
            ];
            return $search->search($query, $options);
        })->paginate($perPage);
        
        return $rooms;
    }
}