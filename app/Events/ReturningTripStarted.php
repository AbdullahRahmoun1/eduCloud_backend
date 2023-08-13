<?php

namespace App\Events;

use App\Helpers\Helper;
use App\Http\Controllers\BusReturningTripController as trip;
use App\Models\Bus;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ReturningTripStarted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(public Bus $bus){
        
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        $ids=$this->bus->students->pluck('id');
        $channels=$ids->map(
            fn($id)=>new PrivateChannel(
                Helper::getStudentChannel($id)
            )    
        );
        return $channels->toArray();
    }

    public function broadcastWith(){
        $data=trip::generateBusKeyAndLink($this->bus);
        return [
            'link'=>$data['link']
        ];
    }

    public function broadCastAs() {
        return 'returningTripStarted';
    }

}
