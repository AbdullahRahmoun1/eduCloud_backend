<?php

namespace App\Events\BusEvents;

use App\Helpers\Helper;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StudentLeftTheBus implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
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
        $result=[
            'student_id'=>$this->student->id,
            'student_name'=>$studentName,
            'date'=>date("Y-m-d"),
            'time'=>date("g:i A"),
            'title'=>"Student Arrival Alert",
            'body'=>"Student $studentName has safely disembarked from the school bus"
        ];
        
        return $result;
    }
    public function broadCastAs() {
        return 'studentLeftTheBus';
    }
}
