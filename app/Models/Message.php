<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;

class Message extends Model
{
    use HasFactory;

    protected $fillable = ['message', 'sender_id', 'recipient_id', 'status'];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function sender() {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function recipient() {
        return $this->belongsTo(User::class, 'recipient_id');
    }

    public function getChatMessages($senderId, $recipientId, $page = 1) {
        return $this->where(function($query) use ($senderId, $recipientId) {
            $query->where('sender_id', $senderId)->where('recipient_id', $recipientId);
        })->orWhere(function($query) use ($senderId, $recipientId) {
            $query->where('sender_id', $recipientId)->where('recipient_id', $senderId);
        })->with('recipient.image', 'sender.image')->orderBy('created_at', 'desc')->paginate(10, '*', 'page', $page);
    }

    public function countUnreadMessagesGroupByRecipientId() {
        return DB::table('messages')
            ->select('sender_id', DB::raw('count(*) as total'))
            ->groupBy('sender_id')
            ->get();;
    }

}
