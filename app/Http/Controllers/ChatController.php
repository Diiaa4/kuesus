<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    /* ---------- customer ---------- */

    public function customerIndex(Request $request)
    {
        $me = $request->user();

        return view('chat', [
            'chat' => $this->config($me->id, $me->id, route('chat.messages'), route('chat.send')),
        ]);
    }

    public function customerMessages(Request $request)
    {
        return $this->fetch($request, $request->user()->id);
    }

    public function customerSend(Request $request)
    {
        return $this->store($request, $request->user()->id);
    }

    /* ---------- admin ---------- */

    public function adminIndex(Request $request, ?User $customer = null)
    {
        if ($customer) {
            abort_if($customer->isAdmin(), 404);
        }

        return view('admin.chat', [
            'customer' => $customer,
            'threads' => $this->threadList(),
            'chat' => $customer ? $this->config(
                $customer->id,
                $request->user()->id,
                route('admin.chat.messages', $customer),
                route('admin.chat.send', $customer)
            ) : null,
        ]);
    }

    public function adminMessages(Request $request, User $customer)
    {
        abort_if($customer->isAdmin(), 404);

        return $this->fetch($request, $customer->id);
    }

    public function adminSend(Request $request, User $customer)
    {
        abort_if($customer->isAdmin(), 404);

        return $this->store($request, $customer->id);
    }

    public function threads()
    {
        return response()->json($this->threadList()->map(fn ($t) => [
            'id' => $t->id,
            'name' => $t->name,
            'unread' => (int) $t->unread,
            'url' => route('admin.chat', $t->id),
        ]));
    }

    /* ---------- shared ---------- */

    private function threadList()
    {
        return User::where('role', 'customer')
            ->whereHas('chatMessages')
            ->withCount(['chatMessages as unread' => fn ($q) => $q
                ->whereColumn('sender_id', 'customer_id')->whereNull('read_at')])
            ->withMax('chatMessages', 'created_at')
            ->orderByDesc('chat_messages_max_created_at')
            ->get();
    }

    private function config(int $customerId, int $meId, string $messagesUrl, string $sendUrl): array
    {
        return [
            'customerId' => $customerId,
            'me' => $meId,
            'messagesUrl' => $messagesUrl,
            'sendUrl' => $sendUrl,
            'csrf' => csrf_token(),
        ];
    }

    private function fetch(Request $request, int $customerId)
    {
        $meId = $request->user()->id;

        // Pesan dari pihak lain dianggap terbaca saat halaman chat dibuka/polling.
        Message::where('customer_id', $customerId)
            ->where('sender_id', '!=', $meId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        $messages = Message::where('customer_id', $customerId)
            ->where('id', '>', (int) $request->query('after', 0))
            ->orderBy('id')
            ->get()
            ->map->toChat();

        return response()->json($messages);
    }

    private function store(Request $request, int $customerId)
    {
        $data = $request->validate(['body' => 'required|string|max:1000']);

        $message = Message::create([
            'customer_id' => $customerId,
            'sender_id' => $request->user()->id,
            'body' => trim($data['body']),
        ]);

        // Realtime lewat Reverb. Kalau server WebSocket mati, polling di browser tetap jalan.
        try {
            broadcast(new MessageSent($message));
        } catch (\Throwable $e) {
            report($e);
        }

        return response()->json($message->toChat());
    }
}
