<?php
namespace App\Helpers;

use App\Models\Student;

class AtPerformance{
    public static function assignStudentAcceptanceRate(&$student,$min_percentage=0.2,$make_hidden=false){
        $max=0;
        $got=0;
        $succeeded_subjects=[];
        $failed_subjects=[];
        $error_in=[];
        foreach($student->atMarks as $mark){
            $mark_max=0;
            $mark_got=0;
            foreach($mark->sections as $section){
                $mark_max+=$section->atSection->max_mark;
                $mark_got+=$section->mark;
            }
            $sub=$mark->abilityTest->subject->name;
            if($mark_max>0){
                if($mark_got/($mark_max*0.1)>=$mark->abilityTest->minimum_success_percentage){
                    $succeeded_subjects[]=$sub;
                }else{
                    $failed_subjects[]=$sub;
                }
            }else{
                $error_in[]=$sub;
            }
            $max+=$mark_max;
            $got+=$mark_got;
        }
        sort($succeeded_subjects);
        sort($failed_subjects);
        $student->full_name=$student->full_name();  
        $have_one_test=$max>0;
        if($have_one_test){
            $f=$got/($max*1.0)*100;
            $student->acceptance_rate=number_format($f, 2)+0.0;
        }else {
            $student->acceptance_rate=0;
        }
        $student->succeeded=$have_one_test?
        $student->acceptance_rate>=$min_percentage:"N/A";
        $student->succeeded_in=$succeeded_subjects;
        $student->failed_in=$failed_subjects;
        $student->error_in=$error_in;
        if($make_hidden){
            $student->makeHidden([
                'first_name','last_name','atMarks'
            ]);
            unset($student->atMarks);
        }
        
    }
    
    public static function assignClassAvgAcceptingRate($class,$unsetStudents=false  ){
        $sum=0;
        $ctr=0;
        foreach($class->students as $student){
            AtPerformance::assignStudentAcceptanceRate($student,make_hidden:true);
                $sum+=$student->acceptance_rate;
                $ctr++;
        }
        $class->avg_acceptance_rate=$ctr==0?0: number_format($sum/($ctr*1.0),2)+0;
        $class->new_avg_acceptance_rate=$class->avg_acceptance_rate;
        $class->new_students_count=0;
    }

    public static function sortByAcceptanceRate(&$students,$desc=true){
        $sortBy=$desc?"sortByDesc":"sortBy";
        $students = $students->$sortBy(function ($item) {
            $successes=count($item['succeeded_in']);
            $fails=count($item['failed_in']);
            return [
                $item['acceptance_rate'],
                $successes + $fails==0?0
                :$successes / ($successes + $fails),
                $successes
            ];
        });
        //to maintain the keys (0 -> 1 -> 2 ) but only values swap ...
        $students=$students->values();
        return $students;
    }
    
    }
