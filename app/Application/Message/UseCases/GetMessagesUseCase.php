<?php

declare(strict_types=1);

namespace App\Application\Message\UseCases;

use App\Domain\Message\Repositories\MessageRepositoryInterface;

final class GetMessagesUseCase
{
    public function __construct(
        private readonly MessageRepositoryInterface $repository,
    ) {}

    public function execute(int $conversationId, int $userId): array
    {
        $messages = $this->repository->getConversationMessages($conversationId);

        // Marcar como leídos
        $this->repository->markAsRead($conversationId, $userId);

        return array_map(function ($msg) {
            return [
                'id' => $msg->id,
                'content' => $msg->content,
                'sender_id' => $msg->sender_id,
                'sender_name' => $msg->sender->name,
                'is_read' => $msg->is_read,
                'image_path' => $msg->image_path,
                'created_at' => $msg->created_at->toIso8601String(),
            ];
        }, $messages);
    }
}
