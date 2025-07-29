<?php

namespace App\Http\Controllers;

use App\Models\WhatsappConversation;
use App\Models\WhatsappMessage;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class WhatsappController extends Controller
{
    /**
     * Exibir a tela principal do chat
     */
    public function index()
    {
        return view('whatsapp.index');
    }

    /**
     * API: Listar conversas para a sidebar
     */
    public function conversations(Request $request): JsonResponse
    {
        $query = WhatsappConversation::with('client')
            ->active()
            ->orderBy('last_message_at', 'desc');

        // Filtro "somente minhas"
        if ($request->boolean('mine')) {
            $query->where('user_id', auth()->id());
        }

        // Busca por nome ou telefone
        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('contact_name', 'like', "%{$search}%")
                  ->orWhere('contact_phone', 'like', "%{$search}%")
                  ->orWhereHas('client', function ($clientQuery) use ($search) {
                      $clientQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $conversations = $query
            ->offset($request->get('offset', 0))
            ->limit($request->get('limit', 20))
            ->get();

        return response()->json([
            'conversations' => $conversations->map(function ($conversation) {
                $lastMessage = $conversation->messages()
                    ->orderBy('created_at', 'desc')
                    ->first();

                return [
                    'id' => $conversation->id,
                    'contact_name' => $conversation->display_name,
                    'contact_phone' => $conversation->contact_phone,
                    'unread_count' => $conversation->unread_count,
                    'last_message_at' => $conversation->last_message_at,
                    'last_message' => $lastMessage ? [
                        'content' => $lastMessage->content,
                        'type' => $lastMessage->type,
                        'direction' => $lastMessage->direction,
                    ] : null,
                    'client' => $conversation->client ? [
                        'id' => $conversation->client->id,
                        'name' => $conversation->client->name,
                    ] : null,
                ];
            }),
        ]);
    }

    /**
     * API: Obter mensagens de uma conversa
     */
    public function messages(Request $request, WhatsappConversation $conversation): JsonResponse
    {
        $messages = $conversation->messages()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->offset($request->get('offset', 0))
            ->limit($request->get('limit', 50))
            ->get()
            ->reverse()
            ->values();

        return response()->json([
            'messages' => $messages->map(function ($message) {
                return [
                    'id' => $message->id,
                    'message_id' => $message->message_id,
                    'direction' => $message->direction,
                    'type' => $message->type,
                    'content' => $message->content,
                    'media' => $message->media,
                    'status' => $message->status,
                    'status_icon' => $message->status_icon,
                    'status_class' => $message->status_class,
                    'sent_at' => $message->sent_at,
                    'delivered_at' => $message->delivered_at,
                    'read_at' => $message->read_at,
                    'user' => $message->user ? [
                        'id' => $message->user->id,
                        'name' => $message->user->name,
                    ] : null,
                ];
            }),
            'conversation' => [
                'id' => $conversation->id,
                'contact_name' => $conversation->display_name,
                'contact_phone' => $conversation->contact_phone,
                'client' => $conversation->client ? [
                    'id' => $conversation->client->id,
                    'name' => $conversation->client->name,
                ] : null,
            ],
        ]);
    }

    /**
     * API: Enviar mensagem
     */
    public function sendMessage(Request $request): JsonResponse
    {
        $request->validate([
            'conversation_id' => 'required|exists:whatsapp_conversations,id',
            'type' => 'required|in:text,image,audio,video,document',
            'content' => 'required_if:type,text',
            'media' => 'required_unless:type,text',
        ]);

        $conversation = WhatsappConversation::findOrFail($request->conversation_id);

        // Criar mensagem na fila
        $message = WhatsappMessage::create([
            'conversation_id' => $conversation->id,
            'message_id' => uniqid('msg_'),
            'direction' => 'outbound',
            'type' => $request->type,
            'content' => $request->content,
            'media' => $request->media,
            'from_phone' => config('services.evolution.instance_name'),
            'to_phone' => $conversation->contact_phone,
            'user_id' => auth()->id(),
            'status' => 'pending',
        ]);

        // TODO: Adicionar à fila de envio
        // dispatch(new SendWhatsappMessage($message));

        // Atualizar conversa
        $conversation->update([
            'last_message_at' => now(),
        ]);

        return response()->json([
            'message' => [
                'id' => $message->id,
                'message_id' => $message->message_id,
                'direction' => $message->direction,
                'type' => $message->type,
                'content' => $message->content,
                'media' => $message->media,
                'status' => $message->status,
                'status_icon' => $message->status_icon,
                'status_class' => $message->status_class,
                'sent_at' => $message->sent_at,
                'user' => [
                    'id' => auth()->id(),
                    'name' => auth()->user()->name,
                ],
            ],
        ]);
    }

    /**
     * API: Marcar conversa como lida
     */
    public function markAsRead(WhatsappConversation $conversation): JsonResponse
    {
        $conversation->markAsRead();

        return response()->json(['success' => true]);
    }

    /**
     * API: Obter possíveis matches para associação
     */
    public function possibleMatches(WhatsappConversation $conversation): JsonResponse
    {
        $matches = $conversation->getPossibleMatches();
        
        return response()->json([
            'matches' => $matches,
            'current_association' => $conversation->association_info,
        ]);
    }

    /**
     * API: Associar conversa manualmente
     */
    public function associate(Request $request, WhatsappConversation $conversation): JsonResponse
    {
        $request->validate([
            'type' => 'required|in:client,contact',
            'id' => 'required|integer',
            'reason' => 'nullable|string|max:255',
        ]);

        $success = \App\Services\ConversationLinker::manualAssociate(
            $conversation,
            $request->type,
            $request->id,
            auth()->id(),
            $request->reason
        );

        if (!$success) {
            return response()->json(['error' => 'Falha ao associar conversa'], 400);
        }

        // Recarregar a conversa para obter os dados atualizados
        $conversation->refresh();
        $conversation->load('client');

        return response()->json([
            'success' => true,
            'association' => $conversation->association_info,
        ]);
    }

    /**
     * API: Desassociar conversa
     */
    public function unlink(Request $request, WhatsappConversation $conversation): JsonResponse
    {
        $request->validate([
            'reason' => 'nullable|string|max:255',
        ]);

        $success = \App\Services\ConversationLinker::unlink(
            $conversation,
            auth()->id(),
            $request->reason
        );

        if (!$success) {
            return response()->json(['error' => 'Conversa já está desassociada'], 400);
        }

        return response()->json([
            'success' => true,
            'association' => $conversation->association_info,
        ]);
    }

    /**
     * Redirecionar para criação de cliente com telefone pré-preenchido
     */
    public function createClient(Request $request)
    {
        $phone = $request->get('phone');
        
        return redirect()->route('clients.create', ['telefone' => $phone]);
    }


}

