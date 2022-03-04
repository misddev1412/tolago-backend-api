<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use App\Translations\TranslationTrait;
use DB;
use Laravel\Scout\Searchable;

class Post extends Model
{
    use HasFactory, Sluggable, TranslationTrait, Searchable;

    //fillable fields for posts model with default values
    protected $fillable = [
        'title',
        'main_id',
        'body',
        'user_id',
        'category_id',
        'slug',
        'status',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'featured',
        'image_id',
        'video_id',
        'created_at',
        'updated_at',
    ];

    //protected translation fields for posts model
    protected $translatable = [
        'title',
        'body',
        'slug',
        'meta_title',
        'meta_description',
        'meta_keywords',
    ];
    
    //translation functions for posts
    public function translation()
    {
        return $this->hasOne('App\Models\TranslationPost');
    }

    //translation functions for posts
    public function translationCurrentLanguage()
    {
        return $this->hasOne('App\Models\TranslationPost')->where('locale', app()->getLocale());
    }



    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'title'
            ]
        ];
    }

    //scope status is active
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function sortableAttributes()
    {
        return [
            'created_at',
            'updated_at',
        ];
    }

    //scope featured
    public function scopeFeatured($query)
    {
        return $query->where('featured', 1);
    }

    //relationship with user model
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    //relationship with category model  (many to many)
    public function categories()
    {
        return $this->belongsToMany('App\Models\Category');
    }

    //relationship with tags model  (many to many)
    public function tags()
    {
        return $this->belongsToMany('App\Models\Tag');
    }

    //relationship with comments model  (one to many)

    //relationship with comments model  (one to many) polymorphic
    public function comments()
    {
        return $this->morphMany('App\Models\Comment', 'commentable');
    }

    //relationship with like polymorphic
    public function likes()
    {
        return $this->morphMany('App\Models\Like', 'likeable');
    }

    //relationship with views model  (one to many)
    public function views()
    {
        return $this->hasMany('App\Models\View');
    }

    //relationship with ratings model  (one to many)
    public function ratings()
    {
        return $this->hasMany('App\Models\Rating');
    }

    //with comments and likes and categories and user
    public function scopeWithAll($query)
    {
        return $query->with('comments', 'likes', 'categories', 'user', 'images')->with([
            'translation' => function ($query) {
                $this->translatable[] = 'post_id';
                $query->select($this->translatable)->where('locale', app()->getLocale());
            }
        ]);
    }

    //paginate with params posts with all relationships  and active
    public function scopePaginateWithAll($query, $perPage = 15, $columns = ['*'], $pageName = 'page', $page = null)
    {
        return $query->withAll()->active()->paginate($perPage, $columns, $pageName, $page);
    }

     //paginate with params posts with all relationships  and active
    public function scopeAllPostAndPaginateWithAll($query, $perPage = 15, $columns = ['*'], $pageName = 'page', $page = null)
    {
        return $query->withAll()->paginate($perPage, $columns, $pageName, $page);
    }

    //findorfail post by id and active and all relationships 
    public function scopeFindOrFailWithAll($query, $id)
    {
        return $query->withAll()->findOrFail($id);
    }


    //findOrFail post by slug and active and all relationships
    public function scopeFindOrFailWithAllBySlug($query, $slug)
    {
        return $query->withAll()->where('slug', $slug)->firstOrFail();
    }

    public function isPublished()
    {
        return $this->status == 1;
    }

    // public function shouldBeSearchable()
    // {
    //     return $this->isPublished();
    // }

    public function toSearchableArray()
    {
        $array = $this->only('title', 'status', 'featured');
        $array['created_at'] = $this->created_at->timestamp;
        if ($this->translation) {
            $array['translation'] = $this->translation->only('title', 'locale') ?? [];
        }
        return $array;
    }

    //relationship with Image model (many to many)
    public function images()
    {
        return $this->belongsToMany('App\Models\Image', 'image_post', 'post_id', 'image_id');
    }


    public function postChildren()
    {
        return $this->hasMany('App\Models\Post', 'main_id');
    }

    public function image() {
        return $this->belongsTo('App\Models\Image', 'image_id');
    }

    public function video() {
        return $this->belongsTo('App\Models\Video', 'video_id');
    }
}
