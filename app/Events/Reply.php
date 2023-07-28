<?php

namespace App\Events;

use App\Helpers\Helper;
use App\Models\Employee;
use App\Models\Reply as ModelsReply;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class Reply implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public $employee;
    public function __construct (
        $employee_id,
        public int $student_id,
        public ModelsReply $reply 
        ){
            $employee=Employee::find($employee_id);
            
            
            $this->employee=[
                'employee_first_name'=>$employee->first_name,
                'employee_second_name'=>$employee->second_name,
                // 'employee_role'=>$role
            ];
    }
    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel(
                Helper::getStudentChannel($this->student_id)
            ),
        ];
    }

    public function broadcastAs(){
        return "new_reply";
    }

}
