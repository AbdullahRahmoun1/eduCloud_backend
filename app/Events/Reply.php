<?php

namespace App\Events;

use App\Helpers\Helper;
use App\Models\Employee;
use App\Models\Reply as ModelsReply;
use App\Models\Student;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class Reply implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct (
        public int $student_id,
        public ModelsReply $reply 
        ){
    }


    public function broadCastWith() {
        $result=Helper::msgBasicData(
            true,Student::findOrFail($this->student_id)
        );
        $this->reply->makeHidden([
            'student_id','employee_id'
        ]);
        $result['reply']=$this->reply;
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
                Helper::getStudentChannel($this->student_id)
            ),
        ];
    }

    public function broadcastAs(){
        return "new_reply";
    }

}
