<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = ['message', 'sender_id', 'recipient_id'];

    public function user() {
        return $this->belongsTo(User::class);
    }

}
