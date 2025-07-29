<?php

namespace App\Http\Controllers;

use App\Models\WhatsappConversation;
use App\Models\WhatsappMessage;
<<<<<<< HEAD
use App\Models\Client;
use App\Models\ClientPhone;
use App\Services\ConversationLinker;
=======
>>>>>>> 80f40225cb6817a4fe5a1b80530045030db9b600
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class WhatsappController extends Controller
{
<<<<<<< HEAD
    protected $conversationLinker;

    public function __construct(ConversationLinker $conversationLinker)
    {
        $this->conversationLinker = $conversationLinker;
    }

    /**
     * Exibe a tela principal do chat WhatsApp
=======
    /**
     * Exibir a tela principal do chat
>>>>>>> 80f40225cb6817a4fe5a1b80530045030db9b600
     */
    public function index()
    {
        return view('whatsapp.index');
    }

    /**
<<<<<<< HEAD
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
=======
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
>>>>>>> 80f40225cb6817a4fe5a1b80530045030db9b600
     */
    public function sendMessage(Request $request): JsonResponse
    {
        $request->validate([
            'conversation_id' => 'required|exists:whatsapp_conversations,id',
<<<<<<< HEAD
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
=======
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


>>>>>>> 80f40225cb6817a4fe5a1b80530045030db9b600
}

