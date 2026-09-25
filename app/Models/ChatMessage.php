<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'sender_id',
        'receiver_id',
        'message_text',
        'gift_id',
        'tokens_spent',
        'image_url',
    ];

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public function getMessageAttribute()
    {
        return $this->attributes['message_text'] ?? null;
    }

    public function setMessageAttribute($value)
    {
        $this->attributes['message_text'] = $value;
    }
}
