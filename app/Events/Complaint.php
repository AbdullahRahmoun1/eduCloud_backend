<?php

namespace App\Events;

use App\Helpers\Helper;
use App\Models\Student;
use App\Models\Complaint as ModelsComplaint;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class Complaint implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public $student;
    public function __construct(
        $studnet_id,
        public ModelsComplaint $complaint
        ){
        $student = Student::find($studnet_id);
        $student->load(['g_class','grade']);
        // $this->student=[
        //     'first_name'=>$student->name,
        //     'last_name'=>$student->last_name,
        //     'grade'=>$student->garde->name,
        //     'class'=>$student->g_class->name,
        // ];
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        $ids=$this->student->g_class->supervisors->pluck('id');
        return $ids->map(
            fn($id)=>new PrivateChannel(
                Helper::getEmployeeChannel($id)
            )
        );
    }
    public function broadcastAs() {
        return 'new_student_complaint';
    }
    
}
