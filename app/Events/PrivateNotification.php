<?php

namespace App\Events;

use App\Helpers\Helper;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PrivateNotification implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public string $user_id,
        public array $message,
        public string $notificationType="notification type",
        public bool $is_user_type_student=true,
    ){
        
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        $channel=$this->is_user_type_student?
        Helper::getStudentChannel($this->user_id):
        Helper::getEmployeeChannel($this->user_id);
        return [
            new PrivateChannel($channel),
        ];
    }
    public function broadcastAs(){
        return 'new-notification';
    }
}
