<?php

declare(strict_types=1);

namespace App\Domain\Message\Repositories;

use App\Infrastructure\Models\ConversationModel;
use App\Infrastructure\Models\MessageModel;

interface MessageRepositoryInterface
{
    public function findConversation(int $userId, int $otherUserId, int $offerId): ?ConversationModel;

    public function createConversation(int $userOneId, int $userTwoId, int $offerId): ConversationModel;

    public function getUserConversations(int $userId): array;

    public function getConversationMessages(int $conversationId): array;

    public function sendMessage(int $conversationId, int $senderId, string $content): MessageModel;

    public function markAsRead(int $conversationId, int $userId): void;

    public function getUnreadCount(int $userId): int;
}
