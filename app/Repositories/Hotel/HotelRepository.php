<?php 

namespace App\Repositories\Hotel;

/**
 * Interface GalleryRepositoryInterface
 * @package App\Repositories
 */
use App\Models\Hotel;

use App\Repositories\Hotel\HotelRepositoryInterface;
use Cache;
use App\Services\HoteLService;
use Illuminate\Http\Request;
class HotelRepository implements HotelRepositoryInterface
{

    private $hotel;

    //constructor with Post model
    public function __construct(Hotel $hotel)
    {
        $this->hotel = $hotel;
    }

    /**
     * @param $id
     * @return mixed
     */
    public function findOrFailWithAll($id)
    {
        $data = Cache::get("hotels.{$id}");
        if (!$data) {
            $data = $this->hotel->findOrFailWithAll($id);
            $hotelService = new HotelService($data);
            $hotelService->cacheSingleHotel();
        }
        return $data; 
    }

    //index function
    public function index(Request $request)
    {
        $perPage    = $request->get('per_page', 10);
        $page       = $request->get('page', 1);

        if ($request->keyword) {
            $hotels = $this->hotel->search($request->keyword, function ($search, string $query, array $options) use ($perPage, $page) {
                $options = [
                    'sort' => ['created_at:desc'],
                    'limit' => $perPage,
                    'offset' => $perPage * ($page - 1),
                ];

                return $search->search($query, $options);
            })->where('user_id', 1)->paginate($perPage);
        } else {
            $hotels = $this->hotel->orderBy('created_at', 'desc')->paginate($perPage);
        }

        $hotels->load('user');
        $hotels->load('country');
        $hotels->load('translationCurrentLanguage');
        return $hotels;
    }

    //auto complete function
    public function autoComplete(Request $request)
    {
        $perPage    = $request->get('per_page', 5);
        $page       = $request->get('page', 1);

        if ($request->keyword) {
            $hotels = $this->getDataSearchSugesstion($request->keyword, $perPage, $page);
        } else {
            $hotels = $this->hotel->orderBy('created_at', 'desc')->paginate($perPage);
        }

        $data = $hotels->pluck('name')->unique()->toArray();
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
        $hotels = $this->hotel->search($name, function ($search, string $query, array $options) use ($perPage, $page) {
            $options = [
                'sort' => ['created_at:desc'],
                'limit' => $perPage,
                'offset' => $perPage * ($page - 1),
            ];
            return $search->search($query, $options);
        })->paginate($perPage);
        
        return $hotels;
    }
}