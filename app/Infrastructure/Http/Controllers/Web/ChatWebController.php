<?php

declare(strict_types=1);

namespace App\Infrastructure\Http\Controllers\Web;

use App\Application\Message\UseCases\GetConversationsUseCase;
use App\Application\Message\UseCases\GetMessagesUseCase;
use App\Application\Message\UseCases\SendMessageUseCase;
use App\Infrastructure\Http\Controllers\BaseController;
use App\Infrastructure\Models\ConversationModel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ChatWebController extends BaseController
{
    public function __construct(
        private readonly GetConversationsUseCase $getConversations,
        private readonly GetMessagesUseCase $getMessages,
        private readonly SendMessageUseCase $sendMessage,
    ) {}

    public function index(): View
    {
        $conversations = $this->getConversations->execute(auth()->id());

        return view('chat.index', compact('conversations'));
    }

    public function show(int $id): View
    {
        $conversation = ConversationModel::with(['userOne', 'userTwo', 'offer'])
            ->findOrFail($id);

        $otherUser = $conversation->otherUser(auth()->id());

        // Verificar que el usuario es participante
        if ($conversation->user_one_id !== auth()->id() && $conversation->user_two_id !== auth()->id()) {
            abort(403);
        }

        $messages = $this->getMessages->execute($id, auth()->id());

        return view('chat.show', compact('conversation', 'otherUser', 'messages'));
    }

    public function startConversation(Request $request): RedirectResponse
    {
        $request->validate([
            'receiver_id' => 'required|integer',
            'offer_id' => 'required|integer',
            'amount' => 'required|numeric|min:1',
            'message' => 'nullable|string|max:1000',
        ]);

        $amount = $request->input('amount');
        $userMessage = $request->input('message');

        // Construir mensaje inicial con el monto
        $content = "Hola, quiero cambiar \$" . number_format((float) $amount, 2);
        if ($userMessage) {
            $content .= "\n\n" . $userMessage;
        }

        $result = $this->sendMessage->execute(
            senderId: auth()->id(),
            receiverId: (int) $request->input('receiver_id'),
            offerId: (int) $request->input('offer_id'),
            content: $content,
            amount: (float) $request->input('amount')
        );

        return redirect()->route('chat.show', $result['conversation_id'])
            ->with('success', 'Mensaje enviado');
    }

    public function send(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $conversation = ConversationModel::findOrFail($id);

        // Verificar participación
        if ($conversation->user_one_id !== auth()->id() && $conversation->user_two_id !== auth()->id()) {
            abort(403);
        }

        $otherUser = $conversation->otherUser(auth()->id());

        $this->sendMessage->execute(
            senderId: auth()->id(),
            receiverId: $otherUser->id,
            offerId: $conversation->offer_id,
            content: $request->input('message')
        );

        return back()->with('success', 'Mensaje enviado');
    }
}
