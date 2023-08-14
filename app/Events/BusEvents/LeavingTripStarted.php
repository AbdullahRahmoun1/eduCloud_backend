<?php

namespace App\Events;

use App\Helpers\Helper;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class LeavingTripStarted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        private $student,
        private String $link
        ) {
        
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel(
            Helper::getStudentChannel($this->student->id)
            )    
        ];
    }
    public function broadcastWith() {
        $studentName=$this->student->full_name;
        $result=[
            'student_id'=>$this->student->id,
            'student_name'=>$studentName,
            'date'=>date("Y-m-d"),
            'time'=>date("g:i A"),
            'link'=>$this->link
        ];
        return $result;
    }

    public function broadCastAs() {
        return 'leavingTripStarted';
    }
}
