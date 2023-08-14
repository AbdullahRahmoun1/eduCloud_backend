<?php

namespace App\Events;

use App\Helpers\Helper;
use App\Models\Student;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use App\Models\Complaint as ModelsComplaint;
use App\Models\Employee;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class Complaint implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public $student;
    public function __construct(
        $student_id,
        public ModelsComplaint $complaint
        ){
            $this->student=Student::findOrFail($student_id);
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        $sups=$this->student->g_class->supervisors->pluck('id');
        $prins=Employee::whereHas(
            'roles',
            fn($query)=>$query->whereName(config('roles.principal'))
        )->get()->pluck('id');
        $ids=array_unique(array_merge($prins->toArray(),$sups->toArray()));
        return array_map(
            fn($id)=>new PrivateChannel(
                Helper::getEmployeeChannel($id)
            ),$ids
        );;
    }
    public function broadcastWith() {
        $student = $this->student;
        $student->load(['g_class','g_class.grade']);
        $student->class=$student->g_class->name;
        $student->grade=$student->g_class->grade->name;
        Helper::onlyKeepAttributes($student,[
            'id',
            'first_name','last_name',
            'grade','class'
        ]);
        unset($student['g_class']);
        $this->complaint->makeHidden('student_id');
        $result=[
            'student'=>$student,
            'complaint'=>$this->complaint
        ];
        return $result;
    }
    public function broadcastAs() {
        return 'new_student_complaint';
    }
    
}
