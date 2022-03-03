<?php 

namespace App\Repositories\Utility;

/**
 * Interface GalleryRepositoryInterface
 * @package App\Repositories
 */
use App\Models\Hotel;
use App\Models\Utility;

use App\Repositories\Utility\UtilityRepositoryInterface;
use Cache;
use App\Services\UtilityService;
use Illuminate\Http\Request;
class UtilityRepository implements UtilityRepositoryInterface
{

    private $utility;

    //constructor with Post model
    public function __construct(Utility $utility)
    {
        $this->utility = $utility;
    }

    /**
     * @param $id
     * @return mixed
     */
    public function findOrFailWithAll($id)
    {
        $data = Cache::get("utilities.{$id}");
        if (!$data) {
            $data = $this->utility->findOrFailWithAll($id);
            $utilityService = new UtilityService($data);
            $utilityService->cacheSingleUtility();
        }
        return $data; 
    }

    //index function
    public function index(Request $request)
    {
        $perPage    = $request->get('per_page', 10);
        $page       = $request->get('page', 1);

        if ($request->keyword) {
            $utilities = $this->utility->search($request->keyword, function ($search, string $query, array $options) use ($perPage, $page) {
                $options = [
                    'sort' => ['created_at:desc'],
                    'limit' => $perPage,
                    'offset' => $perPage * ($page - 1),
                ];

                return $search->search($query, $options);
            })->paginate($perPage);
        } else {
            $utilities = $this->utility->orderBy('created_at', 'desc')->paginate($perPage);
        }

        $utilities->load('translationCurrentLanguage');
        return $utilities;
    }

    //auto complete function
    public function autoComplete(Request $request)
    {
        $perPage    = $request->get('per_page', 5);
        $page       = $request->get('page', 1);

        if ($request->keyword) {
            $utilities = $this->getDataSearchSugesstion($request->keyword, $perPage, $page);
        } else {
            $utilities = $this->utility->orderBy('created_at', 'desc')->paginate($perPage);
        }

        $data = $utilities->pluck('name')->unique()->toArray();
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
        $utilities = $this->utility->search($name, function ($search, string $query, array $options) use ($perPage, $page) {
            $options = [
                'sort' => ['created_at:desc'],
                'limit' => $perPage,
                'offset' => $perPage * ($page - 1),
            ];
            return $search->search($query, $options);
        })->paginate($perPage);
        
        return $utilities;
    }
}