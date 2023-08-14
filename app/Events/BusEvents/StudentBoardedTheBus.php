<?php

namespace App\Events\BusEvents;

use App\Helpers\Helper;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class StudentBoardedTheBus implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        private $student
        ){
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array {
        return [
            new PrivateChannel(
            Helper::getStudentChannel($this->student->id)
            )    
        ];
    }

    public function broadcastWith() {
        $studentName=$this->student->full_name;
        $result=Helper::msgBasicData(true,$this->student);
        $result+=[
            'title'=>"Student Boarding Alert",
            'body'=>"Student $studentName has safely boarded the school bus"
        ];
        return $result;
    }

    public function broadCastAs() {
        return "StudentBoardedTheBus";
    }
}
