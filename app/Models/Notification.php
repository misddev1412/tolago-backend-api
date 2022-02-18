<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';

    public $incrementing = false;

    // In Laravel 6.0+ make sure to also set $keyType
    protected $keyType = 'string';


    //fields for notifications model with sender_id and recipient_id
    protected $fillable = [
        'notifiable_id',
        'notifiable_type',
        'is_sent_sse',
        'type',
        'data',
        'read_at',
        'created_at',
        'updated_at',
    ];



    //relationship with user model
    public function recipient()
    {
        return $this->belongsTo('App\Models\User', 'notifiable_id');
    }

    //list unread conversations
    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    //list read conversations
    public function scopeRead($query)
    {
        return $query->whereNotNull('read_at');
    }

    //is_sent_sse
    public function scopeSentSse($query)
    {
        return $query->where('is_sent_sse', 1);
    }

    //is not sent sse
    public function scopeNotSentSse($query)
    {
        return $query->where('is_sent_sse', 0);
    }

    //scope by notifiable_id
    public function scopeByNotifiableId($query, $notifiable_id)
    {
        return $query->where('notifiable_id', $notifiable_id);
    }

}
