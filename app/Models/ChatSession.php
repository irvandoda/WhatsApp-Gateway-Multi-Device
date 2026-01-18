<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatSession extends Model
{
    protected $fillable = [
        'user_id',
		'body',
        'phone_number',
		'push_name',
        'last_message',
        'last_seen_at',
    ];

    public function messages()
    {
        return $this->hasMany(ChatMessage::class, 'session_id');
    }
}
