<?php

namespace App\Http\Controllers;

use App\Events\ReturningTripStarted;
use App\Models\Bus;
use App\Helpers\Helper;
use Illuminate\Support\Str;
use App\Helpers\ResponseFormatter as res;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\Rule;

class BusReturningTripController extends Controller
{
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
        Cache::put($key,$link,2.5*60);
        $allowedIds=$bus->students->pluck('id');
        Cache::put($link,$allowedIds,2.5*60);
        //notify parents that returning journey has started and give them the link
        self::notifyParents($bus,$absentIds,$link);
        //success.. now we can return the link 
        //to supervisor to start sharing his location
        res::success(data:[
            'link'=>Cache::get($key)
        ]);
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
        self::forgetKeys($bus);
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
    public function forgetKeys($bus) {
        $data=self::generateBusKeyAndLink($bus);
        $key=$data['key'];
        $link=Cache::get($key);
        Cache::forget($key);
        Cache::forget($link);
    }
    private static function notifyParents($bus,$absentIds,$link) {
        foreach($bus->students as $student){
            $absent=in_array($student->id,$absentIds);
            event(new ReturningTripStarted(
                $student,$absent,$link
            ));
        }
    }
    
}
