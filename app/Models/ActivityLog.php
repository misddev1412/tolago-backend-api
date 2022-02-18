<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use HasFactory;
    protected $table = 'activity_logs';
    protected $fillable = [
        'user_id',
        'action_type',
        'action_id',
        'action_table',
        'status',
        'ip_address',
        'user_agent',
        'payload',
    ];
    
    //belongs to user
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
}
