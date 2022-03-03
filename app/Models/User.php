<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use App\Permissions\HasPermissionsTrait;
use Laravel\Scout\Searchable;
use Illuminate\Database\Eloquent\Casts\Attribute;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasPermissionsTrait, Searchable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name', 
        'last_name',
        'email',
        'password',
        'secret_code',
        'email_verified_at',
        'image_id',
        'status',
        'slug',
        'phone_number',
        'facebook_id',
        'google_id',
        'twitter_id',
        'linkedin_id',
        'qr_code',
        'cover_image',
        'username',
        'display_name',
        'birthday',
        'gender',
        'address_book_id',
        'is_phone_verified',
        'remember_token',
        'is_random_password'
    ];

    protected $appends = ['fullname'];

    //function attributes fullname
    protected function fullname(): Attribute
    {
        return new Attribute(
            get: fn ($value, $attributes) => $attributes['first_name'] . ' ' . $attributes['last_name'],
        );

    }


    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    //has many voucher Model
    public function vouchers()
    {
        return $this->hasMany('App\Models\Voucher');
    }

    public function image() {
        return $this->belongsTo('App\Models\Image', 'image_id');
    }

    public function scopeWithAll($query)
    {
        return $query->with(['image', 'vouchers']);
    }
}
