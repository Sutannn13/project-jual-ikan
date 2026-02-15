<?php

namespace App\Events;

use App\Models\ChatMessage;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public array $messageData;
    public int $receiverId;

    /**
     * Create a new event instance.
     */
    public function __construct(ChatMessage $message)
    {
        $this->receiverId = $message->receiver_id;
        $this->messageData = [
            'id'         => $message->id,
            'message'    => $message->message,
            'sender_id'  => $message->sender_id,
            'receiver_id'=> $message->receiver_id,
            'created_at' => $message->created_at->format('H:i'),
            'sender_name'=> $message->sender->name ?? 'Unknown',
        ];
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('chat.' . $this->receiverId),
        ];
    }

    /**
     * Data to broadcast.
     */
    public function broadcastWith(): array
    {
        return $this->messageData;
    }

    /**
     * Event name for client-side.
     */
    public function broadcastAs(): string
    {
        return 'message.sent';
    }
}
