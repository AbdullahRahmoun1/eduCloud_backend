<?php

namespace App\Events\BusEvents;

use App\Helpers\Helper;
use App\Http\Controllers\BusReturningTripController as trip;
use App\Models\Bus;
use Brick\Math\BigInteger;
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
    public function __construct(
        private $student,
        private bool $absent,
        private String $link
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
        $result['absent']=$this->absent;
        if($this->absent){
            $result+=[
                'title'=>"Student Absence on School Bus",
                'body'=>"The student $studentName did not ride"
                ." the school bus on the return trip back home."
            ];
        }else {
            $result+=[
                'title'=>"Attention! Returning trip started.",
                'body'=>"Click on the message to track the current location of the bus.",
                'link' => $this->link
            ];
        }
        return $result;
    }
    public function broadCastAs() {
        return 'returningTripStarted';
    }
}
