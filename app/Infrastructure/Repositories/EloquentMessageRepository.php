<?php

declare(strict_types=1);

namespace App\Infrastructure\Repositories;

use App\Domain\Message\Repositories\MessageRepositoryInterface;
use App\Infrastructure\Models\ConversationModel;
use App\Infrastructure\Models\MessageModel;

final class EloquentMessageRepository implements MessageRepositoryInterface
{
    public function findConversation(int $userId, int $otherUserId, int $offerId): ?ConversationModel
    {
        return ConversationModel::where(function ($q) use ($userId, $otherUserId) {
                $q->where('user_one_id', $userId)->where('user_two_id', $otherUserId);
            })
            ->orWhere(function ($q) use ($userId, $otherUserId) {
                $q->where('user_one_id', $otherUserId)->where('user_two_id', $userId);
            })
            ->where('offer_id', $offerId)
            ->first();
    }

    public function createConversation(int $userOneId, int $userTwoId, int $offerId): ConversationModel
    {
        return ConversationModel::create([
            'user_one_id' => $userOneId,
            'user_two_id' => $userTwoId,
            'offer_id' => $offerId,
            'last_message_at' => now(),
        ]);
    }

    public function getUserConversations(int $userId): array
    {
        return ConversationModel::where('user_one_id', $userId)
            ->orWhere('user_two_id', $userId)
            ->with(['userOne', 'userTwo', 'offer', 'messages' => function ($q) {
                $q->latest()->limit(1);
            }])
            ->orderBy('last_message_at', 'desc')
            ->get()
            ->all();
    }

    public function getConversationMessages(int $conversationId): array
    {
        return MessageModel::where('conversation_id', $conversationId)
            ->with('sender')
            ->orderBy('created_at', 'asc')
            ->get()
            ->all();
    }

    public function sendMessage(int $conversationId, int $senderId, string $content): MessageModel
    {
        $message = MessageModel::create([
            'conversation_id' => $conversationId,
            'sender_id' => $senderId,
            'content' => $content,
        ]);

        ConversationModel::where('id', $conversationId)->update([
            'last_message_at' => now(),
        ]);

        return $message;
    }

    public function markAsRead(int $conversationId, int $userId): void
    {
        MessageModel::where('conversation_id', $conversationId)
            ->where('sender_id', '!=', $userId)
            ->where('is_read', false)
            ->update(['is_read' => true]);
    }

    public function getUnreadCount(int $userId): int
    {
        $conversationIds = ConversationModel::where('user_one_id', $userId)
            ->orWhere('user_two_id', $userId)
            ->pluck('id');

        return MessageModel::whereIn('conversation_id', $conversationIds)
            ->where('sender_id', '!=', $userId)
            ->where('is_read', false)
            ->count();
    }
}
