<?php

declare(strict_types=1);

namespace App\Application\Message\UseCases;

use App\Domain\Message\Repositories\MessageRepositoryInterface;
use App\Domain\Offer\Repositories\OfferRepositoryInterface;

final class SendMessageUseCase
{
    public function __construct(
        private readonly MessageRepositoryInterface $messageRepository,
        private readonly OfferRepositoryInterface $offerRepository,
    ) {}

    public function execute(int $senderId, int $receiverId, int $offerId, string $content, ?float $amount = null): array
    {
        // Buscar o crear conversación
        $conversation = $this->messageRepository->findConversation($senderId, $receiverId, $offerId);
        $isNewConversation = !$conversation;

        if (!$conversation) {
            // Determinar quién es user_one y user_two (el menor ID siempre es user_one)
            if ($senderId < $receiverId) {
                $conversation = $this->messageRepository->createConversation($senderId, $receiverId, $offerId);
            } else {
                $conversation = $this->messageRepository->createConversation($receiverId, $senderId, $offerId);
            }
        }

        // Si es nueva conversación y hay monto, reservarlo
        if ($isNewConversation && $amount !== null && $amount > 0) {
            $offer = $this->offerRepository->findById($offerId);
            if ($offer) {
                $offer->reserveAmount($amount);
                $this->offerRepository->save($offer);
            }
        }

        // Enviar mensaje
        $message = $this->messageRepository->sendMessage($conversation->id, $senderId, $content);

        return [
            'conversation_id' => $conversation->id,
            'message' => [
                'id' => $message->id,
                'content' => $message->content,
                'sender_id' => $message->sender_id,
                'created_at' => $message->created_at->toIso8601String(),
            ],
        ];
    }
}
