<?php

namespace App\Http\Controllers;

use App\Models\Bus;
use App\Helpers\Helper;
use App\Models\Student;
use Illuminate\Support\Str;
use App\Events\BusEvents\BusWillSkip;
use Illuminate\Support\Facades\Cache;
use App\Events\BusEvents\BusBrokeDown;
use App\Events\BusEvents\GpsLinkClosed;
use App\Events\BusEvents\BusArrivalSoon;
use App\Helpers\ResponseFormatter as res;
use App\Events\BusEvents\LeavingTripStarted;
use App\Events\BusEvents\StudentBoardedTheBus;
use App\Events\BusEvents\GpsLinkInitialization;

class BusLeavingTripController extends Controller
{
    public const DataLifeTime=2.5*60;

    public function startTrip(Bus $bus) {
        //is the user allowed to control this trip?
        Helper::tryToControlBusTrips($bus->id);
        //good.. now get todays key + generate a link
        $data=self::generateBusKeysAndLink($bus);
        $key=$data['key'];
        $link=$data['link'];
        $onBoardKey=$data['onBoardKey'];
        //make sure there is no returning trip active
        $Leaving=BusReturningTripController::generateBusKeyAndLink($bus);
        if(Cache::has($Leaving['key']))
        res::error("Returning trip is still active. End it first.");
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
        $allowedIds=$bus->students->pluck('id');
        Cache::put($link,$allowedIds,self::DataLifeTime);
        Cache::put($onBoardKey,[],self::DataLifeTime);
        //initialize the link channel 
        event(new GpsLinkInitialization($link));
        //notify parents that returning journey has started and give them the link
        self::notifyParents($bus,$link);
        //success.. now we can return the link 
        //to supervisor to start sharing his location
        res::success(data:[
            'link'=>Cache::get($key)
        ]);
    }
    public function busWillArriveSoon(Student $student){
        $bus=Helper::validateStudentHasBus($student);
        Helper::tryToControlBusTrips($bus->id);
        event(new BusArrivalSoon($student));
        res::success();
    }
    public function StudentBoardedTheBus(Student $student){
        //fix this line if you found a way to make it return only one
        $bus=Helper::validateStudentHasBus($student);
        Helper::tryToControlBusTrips($bus);
        $data=self::generateBusKeysAndLink($bus);
        if(!Cache::has($data['key'])){
            res::error(
                "Leaving trip hasn't started yet!.",
            );
        }
        $onBoardKey=$data['onBoardKey'];
        if(!Cache::has($onBoardKey)){
            res::error(
                "Contact developer.",
                data:"Link found..allowed ids didn't.",
                code:500
            );
        }
        $onBoard=Cache::get($onBoardKey);
        $onBoard=collect($onBoard);
        if($onBoard->contains($student->id)){
            res::error(
                "Looks like This student already boarded the bus!",
            );
        }
        $onBoard->push($student->id);
        Cache::put(
            $onBoardKey,
            $onBoard,
            self::DataLifeTime
        );
        event(new StudentBoardedTheBus($student));
        res::success();
    }
    public function busWillSkipStudent(Student $student) {
        $bus=Helper::validateStudentHasBus($student);
        Helper::tryToControlBusTrips($bus->id);
        event(new BusWillSkip($student));
        res::success();
    }
    public function busBrokeDown(Bus $bus) {
        Helper::tryToControlBusTrips($bus->id);
        $data=self::generateBusKeysAndLink($bus);
        $key=$data['key'];
        $link=Cache::get($key,-1);
        if($link==-1){
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
    public function endTrip(Bus $bus) {
        //is he allowed to end trip
        Helper::tryToControlBusTrips($bus->id);
        //get data
        $data=self::generateBusKeysAndLink($bus);
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
    public static function generateBusKeysAndLink($bus){
        $today = date('Y-m-d');
        return [
            'key'=>"leaving_trip_link/$bus->name($bus->id)_$today",
            'link'=>Str::random(30),
            'onBoardKey'=>"leaving_trip_onBoard/$bus->name($bus->id)_$today"
        ];
    }
    private static function notifyParents($bus,$link) {
        foreach($bus->students as $student){
            event(new LeavingTripStarted(
                $student,$link
            ));
        }
    }
    public function forgetKeys($bus) {
        $data=self::generateBusKeysAndLink($bus);
        $key=$data['key'];
        $link=Cache::get($key);
        Cache::forget($key);
        Cache::forget($link);
        Cache::forget($data['onBoardKey']);
    }
    
}
