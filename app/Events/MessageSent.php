<?php
// app/Events/MessageSent.php

namespace App\Events;

use App\Models\TicketMessage;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Broadcast inmediato (ShouldBroadcastNow) — sin cola.
 * Para mensajes de chat la latencia importa más que el throughput.
 * Si el sistema crece, cambiar a ShouldBroadcast y configurar una cola dedicada.
 */
class MessageSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly TicketMessage $message
    ) {}

    /**
     * Canal privado por ticket — solo usuarios autorizados en channels.php
     * pueden suscribirse.
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('ticket.' . $this->message->ticket_id),
        ];
    }

    /**
     * Nombre de evento explícito con punto.
     * En el cliente se escucha como: channel.listen('.message.sent', ...)
     * El punto inicial indica evento personalizado (no nombre de clase PHP).
     */
    public function broadcastAs(): string
    {
        return 'message.sent';
    }

    /**
     * Payload mínimo — solo lo que TicketMessageThread necesita renderizar.
     * No exponer datos sensibles (company_id, IPs, etc.).
     */
    public function broadcastWith(): array
    {
        $this->message->loadMissing('user');

        return [
            'id'          => $this->message->id,
            'ticket_id'   => $this->message->ticket_id,
            'message'     => $this->message->message,
            'is_internal' => (bool) $this->message->is_internal,
            'created_at'  => $this->message->created_at->toIso8601String(),
            'user'        => $this->message->user ? [
                'id'   => $this->message->user->id,
                'name' => $this->message->user->name,
            ] : null,
        ];
    }
}
