<?php

namespace App\Http\Controllers;

use App\Models\WhatsappConversation;
use App\Models\WhatsappMessage;
use App\Models\Client;
use App\Models\ClientPhone;
use App\Services\ConversationLinker;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class WhatsappController extends Controller
{
    protected $conversationLinker;

    public function __construct(ConversationLinker $conversationLinker)
    {
        $this->conversationLinker = $conversationLinker;
    }

    /**
     * Exibe a tela principal do chat WhatsApp
     */
    public function index()
    {
        return view('whatsapp.index');
    }

    /**
     * API: Lista conversas com paginação e filtros
     */
    public function conversations(Request $request): JsonResponse
    {
        try {
            $query = WhatsappConversation::with(['client', 'user'])
                ->orderBy('last_message_at', 'desc');

            // Filtro "somente minhas"
            if ($request->boolean('mine')) {
                $query->where('user_id', auth()->id());
            }

            // Busca por nome ou telefone
            if ($search = $request->get('search')) {
                $query->where(function ($q) use ($search) {
                    $q->where('contact_name', 'like', "%{$search}%")
                      ->orWhere('contact_phone', 'like', "%{$search}%");
                });
            }

            $conversations = $query->offset($request->get('offset', 0))
                                 ->limit($request->get('limit', 20))
                                 ->get();

            return response()->json([
                'conversations' => $conversations->map(function ($conversation) {
                    return [
                        'id' => $conversation->id,
                        'contact_name' => $conversation->contact_name,
                        'contact_phone' => $conversation->contact_phone,
                        'unread_count' => $conversation->unread_count,
                        'last_message_at' => $conversation->last_message_at?->format('H:i'),
                        'client_name' => $conversation->client?->name,
                        'is_linked' => !is_null($conversation->client_id),
                        'user_name' => $conversation->user?->name,
                    ];
                })
            ]);
        } catch (\Exception $e) {
            \Log::error('Erro ao carregar conversas: ' . $e->getMessage());
            return response()->json(['error' => 'Erro interno do servidor'], 500);
        }
    }

    /**
     * API: Lista mensagens de uma conversa
     */
    public function messages(WhatsappConversation $conversation, Request $request): JsonResponse
    {
        try {
            $messages = WhatsappMessage::where('conversation_id', $conversation->id)
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
                        'content' => $message->content,
                        'direction' => $message->direction,
                        'status' => $message->status,
                        'status_icon' => $message->getStatusIcon(),
                        'created_at' => $message->created_at->format('H:i'),
                        'user_name' => $message->user?->name,
                    ];
                })
            ]);
        } catch (\Exception $e) {
            \Log::error('Erro ao carregar mensagens: ' . $e->getMessage());
            return response()->json(['error' => 'Erro interno do servidor'], 500);
        }
    }

    /**
     * API: Envia uma mensagem
     */
    public function sendMessage(Request $request): JsonResponse
    {
        $request->validate([
            'conversation_id' => 'required|exists:whatsapp_conversations,id',
            'content' => 'required|string|max:4096',
        ]);

        try {
            $conversation = WhatsappConversation::findOrFail($request->conversation_id);
            
            $message = WhatsappMessage::create([
                'conversation_id' => $conversation->id,
                'content' => $request->content,
                'direction' => 'outbound',
                'status' => 'pending',
                'user_id' => auth()->id(),
            ]);

            // TODO: Adicionar à fila de envio
            // dispatch(new SendWhatsappMessageJob($message));

            return response()->json([
                'message' => [
                    'id' => $message->id,
                    'content' => $message->content,
                    'direction' => $message->direction,
                    'status' => $message->status,
                    'status_icon' => $message->getStatusIcon(),
                    'created_at' => $message->created_at->format('H:i'),
                    'user_name' => $message->user?->name,
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Erro ao enviar mensagem: ' . $e->getMessage());
            return response()->json(['error' => 'Erro ao enviar mensagem'], 500);
        }
    }

    /**
     * API: Marca conversa como lida
     */
    public function markAsRead(WhatsappConversation $conversation): JsonResponse
    {
        try {
            $conversation->update(['unread_count' => 0]);
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            \Log::error('Erro ao marcar como lida: ' . $e->getMessage());
            return response()->json(['error' => 'Erro interno do servidor'], 500);
        }
    }

    /**
     * API: Busca possíveis vínculos para uma conversa
     */
    public function getMatches(WhatsappConversation $conversation): JsonResponse
    {
        try {
            $matches = $this->conversationLinker->findMatches($conversation->contact_phone);
            
            return response()->json([
                'matches' => $matches->map(function ($client) {
                    return [
                        'id' => $client->id,
                        'name' => $client->name,
                        'email' => $client->primaryEmail?->email,
                        'phone' => $client->primaryPhone?->phone,
                    ];
                })
            ]);
        } catch (\Exception $e) {
            \Log::error('Erro ao buscar matches: ' . $e->getMessage());
            return response()->json(['error' => 'Erro interno do servidor'], 500);
        }
    }

    /**
     * API: Associa conversa a um cliente
     */
    public function associate(WhatsappConversation $conversation, Request $request): JsonResponse
    {
        $request->validate([
            'client_id' => 'required|exists:clients,id',
        ]);

        try {
            $this->conversationLinker->associate($conversation, $request->client_id, auth()->id());
            
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            \Log::error('Erro ao associar conversa: ' . $e->getMessage());
            return response()->json(['error' => 'Erro ao associar conversa'], 500);
        }
    }

    /**
     * API: Remove vínculo da conversa
     */
    public function unlink(WhatsappConversation $conversation): JsonResponse
    {
        try {
            $this->conversationLinker->unlink($conversation, auth()->id());
            
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            \Log::error('Erro ao desvincular conversa: ' . $e->getMessage());
            return response()->json(['error' => 'Erro ao desvincular conversa'], 500);
        }
    }
}

