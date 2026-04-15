<?php

namespace App\Infrastructure\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MessageModel extends Model
{
    protected $table = 'messages';

    protected $fillable = [
        'conversation_id',
        'sender_id',
        'content',
        'is_read',
        'image_path',
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(ConversationModel::class, 'conversation_id');
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'sender_id');
    }
}
