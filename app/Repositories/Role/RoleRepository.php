<?php 

namespace App\Repositories\Role;

/**
 * Interface GalleryRepositoryInterface
 * @package App\Repositories
 */
use App\Models\Role;

use App\Repositories\Role\RoleRepositoryInterface;
use Cache;
use App\Services\RoleService;
use App\Jobs\Post\ProcessCachePost;
use Illuminate\Http\Request;
class RoleRepository implements RoleRepositoryInterface
{

    private $role;

    //constructor with Post model
    public function __construct(Role $role)
    {
        $this->role = $role;
    }

    /**
     * @param $id
     * @return mixed
     */
    public function findOrFailWithAll($id)
    {
        $data = Cache::get("roles.{$id}");
        if (!$data) {
            $data = $this->role->findOrFailWithAll($id);
            $roleService = new RoleService($data);
            $roleService->cacheSingleRole();
        }
        return $data; 
    }

    //index function
    public function index(Request $request)
    {
        $perPage    = $request->get('per_page', 10);
        $page       = $request->get('page', 1);

        if ($request->name) {
            $roles = $this->role->search($request->name, function ($search, string $query, array $options) use ($perPage, $page) {
                $options = [
                    // 'sort' => ['created_at:desc'],
                    'limit' => $perPage,
                    'offset' => $perPage * ($page - 1),
                ];
            
                return $search->search($query, $options);
            });
            $ids = $roles->get()->pluck('id')->toArray();
            $roles = Role::whereIn('id', $ids)->orderBy('id', 'desc')->paginate($perPage);
        } else {
            $roles = $this->role->orderBy('id', 'desc')->paginate($perPage);
        }

        return $roles;
    }
}