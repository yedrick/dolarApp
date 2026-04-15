<?php

declare(strict_types=1);

namespace App\Application\Message\UseCases;

use App\Domain\Message\Repositories\MessageRepositoryInterface;

final class GetConversationsUseCase
{
    public function __construct(
        private readonly MessageRepositoryInterface $repository,
    ) {}

    public function execute(int $userId): array
    {
        $conversations = $this->repository->getUserConversations($userId);

        return array_map(function ($conv) use ($userId) {
            $otherUser = $conv->otherUser($userId);
            $lastMessage = $conv->messages->first();

            return [
                'id' => $conv->id,
                'other_user' => [
                    'id' => $otherUser->id,
                    'name' => $otherUser->name,
                ],
                'offer' => [
                    'id' => $conv->offer->id,
                    'type' => $conv->offer->type,
                    'price' => $conv->offer->price,
                ],
                'last_message' => $lastMessage ? [
                    'content' => $lastMessage->content,
                    'created_at' => $lastMessage->created_at->toIso8601String(),
                    'is_mine' => $lastMessage->sender_id === $userId,
                    'is_read' => $lastMessage->is_read,
                    'image_path' => $lastMessage->image_path,
                ] : null,
                'last_message_at' => $conv->last_message_at?->toIso8601String(),
            ];
        }, $conversations);
    }
}
