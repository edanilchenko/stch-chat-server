<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ConversationController extends Controller
{
    // GET /conversations — list all conversations for the authenticated user
    public function index(Request $request): JsonResponse
    {
        $userId = $request->user()->id;

        $conversations = Conversation::with(['user1', 'user2', 'messages' => function ($q) {
                $q->latest()->limit(1);
            }])
            ->where('user1_id', $userId)
            ->orWhere('user2_id', $userId)
            ->latest()
            ->get()
            ->map(function (Conversation $conversation) use ($userId) {
                return [
                    'id' => $conversation->id,
                    'other_user' => $conversation->otherUser($userId),
                    'last_message' => $conversation->messages->first(),
                    'created_at' => $conversation->created_at,
                ];
            });

        return response()->json($conversations);
    }

    // POST /conversations — start or retrieve a conversation with another user
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'user_id' => 'required|exists:users,id|different:' . $request->user()->id,
        ]);

        $authId = $request->user()->id;
        $otherId = (int) $request->user_id;

        // Always store the lower ID as user1 to keep the unique constraint consistent
        [$user1Id, $user2Id] = $authId < $otherId
            ? [$authId, $otherId]
            : [$otherId, $authId];

        $conversation = Conversation::firstOrCreate(
            ['user1_id' => $user1Id, 'user2_id' => $user2Id]
        );

        $conversation->load(['user1', 'user2']);

        return response()->json([
            'id' => $conversation->id,
            'other_user' => $conversation->otherUser($authId),
            'created_at' => $conversation->created_at,
        ], $conversation->wasRecentlyCreated ? 201 : 200);
    }

    // GET /conversations/{conversation}/messages — list messages in a conversation
    public function messages(Request $request, Conversation $conversation): JsonResponse
    {
        $this->authorizeConversation($request, $conversation);

        $messages = $conversation->messages()
            ->with('sender:id,name,email')
            ->oldest()
            ->get();

        return response()->json($messages);
    }

    // POST /conversations/{conversation}/messages — send a message
    public function sendMessage(Request $request, Conversation $conversation): JsonResponse
    {
        $this->authorizeConversation($request, $conversation);

        $request->validate([
            'text' => 'required|string',
        ]);

        $message = $conversation->messages()->create([
            'user_id' => $request->user()->id,
            'text' => $request->text,
        ]);

        $message->load('sender:id,name,email');

        return response()->json($message, 201);
    }

    private function authorizeConversation(Request $request, Conversation $conversation): void
    {
        $userId = $request->user()->id;

        if ($conversation->user1_id !== $userId && $conversation->user2_id !== $userId) {
            abort(403);
        }
    }
}
