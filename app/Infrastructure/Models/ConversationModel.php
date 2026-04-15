<?php

namespace App\Infrastructure\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ConversationModel extends Model
{
    protected $table = 'conversations';

    protected $fillable = [
        'user_one_id',
        'user_two_id',
        'offer_id',
        'last_message_at',
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
    ];

    public function userOne(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'user_one_id');
    }

    public function userTwo(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'user_two_id');
    }

    public function offer(): BelongsTo
    {
        return $this->belongsTo(OfferModel::class, 'offer_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(MessageModel::class, 'conversation_id')->orderBy('created_at', 'asc');
    }

    public function otherUser(int $currentUserId): \App\Models\User
    {
        return $this->user_one_id === $currentUserId ? $this->userTwo : $this->userOne;
    }
}
