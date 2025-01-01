<?php

namespace App\Events;

use App\Models\WallstreetEvent;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewWallstreetEvent implements ShouldBroadcastNow
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public int $wallstreetDrinkId,
        public WallstreetEvent $wallstreetEvent
    ) {}

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('wallstreet-prices.'.$this->wallstreetDrinkId),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'data' => $this->wallstreetEvent,
        ];
    }
}
