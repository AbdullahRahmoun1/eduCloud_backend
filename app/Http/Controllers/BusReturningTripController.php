<?php

namespace App\Http\Controllers;

use App\Models\Bus;
use App\Helpers\Helper;
use App\Models\Student;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Cache;
use App\Events\BusEvents\BusBrokeDown;
use App\Events\BusEvents\GpsLinkClosed;
use App\Events\BusEvents\BusArrivalSoon;
use App\Helpers\ResponseFormatter as res;
use App\Events\BusEvents\StudentLeftTheBus;
use App\Events\BusEvents\ReturningTripStarted;
use App\Events\BusEvents\GpsLinkInitialization;

class BusReturningTripController extends Controller
{
    public const DataLifeTime=2.5*60;
    public function startTrip(Bus $bus) {
        //is the user allowed to control this trip?
        Helper::tryToControlBusTrips($bus->id);
        //get the absent students
         //is this student subscribed to this bus
        $studentSubscribedToBus=Rule::exists('student_bus','student_id')
        ->where('bus_id',$bus->id);
        //validate
        $data=request()->validate([
            'absentStudentsIds'=>['array','min:1'],
            'absentStudentsIds.*'=>['required','exists:students,id',$studentSubscribedToBus]
        ]);
        $absentIds=$data['absentStudentsIds']??[];
        
        //good.. now get todays key + generate a link
        $data=self::generateBusKeyAndLink($bus);
        $key=$data['key'];
        $link=$data['link'];
        //wait.. we have to make sure that there is no data with this key
        //if there is..then someone called this earlier today..
        if(Cache::has($key)){
            res::error(
                "Trip has already started.",
                [
                    'link'=>Cache::get($key)
                ],
                409
            );
        }        
        //nop.. ok now we save the link for today's journey
        Cache::put($key,$link,self::DataLifeTime);
        //allowed ids are the student who can watch the bus location
        $allowedIds=$bus->students
        ->where('isAbsentToday',false)->pluck('id')
        ->diff($absentIds);
        Cache::put($link,$allowedIds,self::DataLifeTime);
        //initialize the link channel 
        event(new GpsLinkInitialization($link));
        //notify parents that returning journey has started and give them the link
        self::notifyParents($bus,$absentIds,$link);
        //success.. now we can return the link 
        //to supervisor to start sharing his location
        res::success(data:[
            'link'=>Cache::get($key)
        ]);
    }
    public function busBrokeDown(Bus $bus) {
        Helper::tryToControlBusTrips($bus->id);
        $data=self::generateBusKeyAndLink($bus);
        $key=$data['key'];
        $link=Cache::get($key,-1);
        if($link===-1){
            res::error("Start the trip first.");
        }
        $allowedIds=Cache::get($link);
        $students=Student::whereIn('id',$allowedIds)
        ->get();
        foreach($students as $student){
            event(new BusBrokeDown($student));
        }
        res::success();
    }
    public function busWillArriveSoon(Student $student){
        $bus=Helper::validateStudentHasBus($student);
        Helper::tryToControlBusTrips($bus->id);
        event(new BusArrivalSoon($student));
        res::success();
    }
    public function studentLeftTheBus(Student $student){
        $bus=Helper::validateStudentHasBus($student);
        Helper::tryToControlBusTrips($bus);
        $key=self::generateBusKeyAndLink($bus)['key'];
        $link=Cache::get($key,-1);
        if($link==-1){
            res::error(
                "Returning trip hasn't started yet!.",
            );
        }
        $allowedIds=Cache::get($link,-1);
        if($allowedIds===-1){
            res::error(
                "Contact developer.",
                data:"Link found..allowed ids didn't.",
                code:500
            );
        }
        $allowedIds=collect($allowedIds);
        if(!$allowedIds->contains($student->id)){
            res::error(
                !$student->isAbsentToday?"Looks like This student already left the bus!"
                :"Student is absent today! You can't do this action. ",
            );
        }
        Cache::put(
            $link,
            $allowedIds->reject(function($value)use($student){
                return $value==$student->id;
            }),
            self::DataLifeTime
        );
        event(new StudentLeftTheBus($student));
        res::success();
    }
    public function endTrip(Bus $bus) {
        //is he allowed to end trip
        Helper::tryToControlBusTrips($bus->id);
        //get data
        $data=self::generateBusKeyAndLink($bus);
        $key=$data['key'];
        //ok.. is this trip still active?
        if(!Cache::has($key))
        res::error("The trip has already ended.");
        //ok just end it
        $link=Cache::get($key);
        self::forgetKeys($bus);
        //tell connected users that the link has been closed
        event(new GpsLinkClosed($link));
        //return response..
        res::success("Trip ended successfully!");
    }
    public static function generateBusKeyAndLink($bus){
        $today = date('Y-m-d');
        return [
            'key'=>"returning_trip_link/$bus->name($bus->id)_$today",
            'link'=>Str::random(30),
        ];
    }
    private static function notifyParents($bus,$absentIds,$link) {
        //if true then don't send the message
        foreach($bus->students as $student){
            if($student->isAbsentToday)
            continue;
            $absent=in_array($student->id,$absentIds);
            event(new ReturningTripStarted(
                $student,$absent,$link
            ));
        }
    }
    public function forgetKeys($bus) {
        $data=self::generateBusKeyAndLink($bus);
        $key=$data['key'];
        $link=Cache::get($key);
        Cache::forget($key);
        Cache::forget($link);
    }
    
}
