<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;

class Category extends Model
{
    use HasFactory, Sluggable;
    //field categories table with metadata and image files
    protected $fillable = [
        'name',
        'slug',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'type',
        'parent_id',
        'image',
        'status',
        'created_at',
        'updated_at',
    ];

    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name'
            ]
        ];
    }

    //relationship with posts model  (many to many)
    public function posts()
    {
        return $this->belongsToMany('App\Models\Post');
    }

    //relationship with subcategories model  (one to many)
    public function subcategories()
    {
        return $this->hasMany('App\Models\Category', 'parent_id');
    }

    //scope with all relationship
    public function scopeWithAll($query)
    {
        return $query->with('posts', 'subcategories');
    }

    //scope paginate with all relationship and parameter
    public function scopePaginateWithAll($query, $perPage = 15, $columns = ['*'], $pageName = 'page', $page = null)
    {
        return $query->withAll()->paginate($perPage, $columns, $pageName, $page);
    }

}
