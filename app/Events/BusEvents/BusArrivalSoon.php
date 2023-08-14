<?php

namespace App\Events\BusEvents;

use App\Helpers\Helper;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BusArrivalSoon implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(public $student)
    {
        
    }

    public function broadcastWith() {
        $studentName=$this->student->full_name;
        $result=Helper::msgBasicData(true,$this->student);
        $result+=[
            'title'=>"Bus arrival is soon!",
            'body'=>"The bus will be here in 2-5 minutes."
        ];
        return $result;
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
    public function broadCastAs() {
        return "BusWillArriveSoon";
    }
}
