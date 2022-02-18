<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Http\Requests\Category\CreateCategoryRequest;
use App\Helpers\Response;
use App\Enum\HttpStatusCode;

class CategoryController extends Controller
{
    //var $category
    protected $category;
    
    //construction Category model
    public function __construct(Category $category)
    {
        $this->category = $category;
    }

    //resources index scope paginateWithAll return generateResponse
    public function index()
    {
        $categories = $this->category->paginateWithAll();
        return Response::generateResponse(HttpStatusCode::OK, '', $categories);
    }

    //store function return generateResponse
    public function store(CreateCategoryRequest $request)
    {
        $dataCreate = [
            'name' => $request->name,
            'description' => $request->description,
            'parent_id' => $request->parent_id,
            'status' => $request->status,
            'meta_title' => $request->meta_title,
            'meta_description' => $request->meta_description,
            'meta_keywords' => $request->meta_keywords,
            'featured' => $request->featured,
        ];

        $category = $this->category->create($dataCreate);
        return Response::generateResponse(HttpStatusCode::CREATED, '', $category);
    }

}
