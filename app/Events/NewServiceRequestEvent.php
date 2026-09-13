<?php

namespace App\Events;

use App\Models\ServiceRequest;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewServiceRequestEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $serviceRequest;
    public $providerId;

    public function __construct(ServiceRequest $serviceRequest, $providerId)
    {
        $this->serviceRequest = $serviceRequest->load('service', 'customer');
        $this->providerId = $providerId;
    }

    public function broadcastOn(): array
    {
        return [
            new \Illuminate\Broadcasting\PrivateChannel('provider.' . $this->providerId),
        ];
    }

    /**
     * Get the data to broadcast.
     *
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'id' => $this->serviceRequest->id,
            'service' => $this->serviceRequest->service->name,
            'description' => $this->serviceRequest->description,
            'latitude' => $this->serviceRequest->latitude,
            'longitude' => $this->serviceRequest->longitude,
            'address' => $this->serviceRequest->address,
        ];
    }
}
