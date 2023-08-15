<?php

namespace App\Helpers;

use App\Helpers\AtPerformance;
use SplPriorityQueue;

class StudentsDistributer
{
    public static function even_distribution($students,$classes){
        $classesQueue=new SplPriorityQueue('avg_acceptance_rate');
        $assignments=[];
        foreach($classes as $class){
            AtPerformance::assignClassAvgAcceptingRate($class);
            Helper::onlyKeepAttributes($class,[
                'id','name','grade_id','avg_acceptance_rate','new_avg_acceptance_rate','students'
            ]);
            $classesQueue->insert($class,-$class->new_avg_acceptance_rate);
            $class->newStudents=[];
        }
        foreach($students as $student){
            AtPerformance::assignStudentAcceptanceRate($student, 0.2, true);
            Helper::onlyKeepAttributes($student,[
                'id','first_name','last_name','grade_id','acceptance_rate'
            ]);
        }
        //TODO: remember to make it true (after testing)
        AtPerformance::sortByAcceptanceRate($students,false);       
        $newClasses=[];
        foreach($students as $student){
            $class=$classesQueue->extract();
            if(!self::classCanAddStudent($class)){
                $newClasses[$class->id]=$class;
                continue;
            }
            $assignments[$class->id][]=$student;
            StudentsDistributer::updateAvgAcceptanceRate($class,$student->acceptance_rate);
            $classesQueue->insert($class,-$class->new_avg_acceptance_rate);
        }
        
        while(!$classesQueue->isEmpty()){
            $class=$classesQueue->current();
            $newClasses[$class->id]=$class;
            $classesQueue->next();
        }   
        $classes=(array)$newClasses;
        self::even_distribution_improveAssignments($classes,$assignments,1000);
        self::prepareClassesVisualizing($classes,$assignments);
        return $classes;
    }
    private static function even_distribution_improveAssignments(&$classes,&$assignments,$iterations){
        $ctr=0;
        for($i=0;$i<$iterations;$i++){
            //pick two classes
            $c1=$classes[array_rand($classes)];
            $c2=$classes[array_rand($classes)];
            if($c1->id==$c2->id)
            continue;
            if(empty($assignments[$c1->id]) 
            || empty($assignments[$c2->id]))
            continue;
            //pick one student from each
            $s1Key=array_rand($assignments[$c1->id]);
            $s1=$assignments[$c1->id][$s1Key];
            $s2Key=array_rand($assignments[$c2->id]);
            $s2=$assignments[$c2->id][$s2Key];
            //get students count of both classes
            $c1Count=$c1->students()->count()+count($assignments[$c1->id]);
            $c2Count=$c2->students()->count()+count($assignments[$c2->id]);
            //get new classes avgs
            $c1NAAR=$c1->new_avg_acceptance_rate;
            $c2NAAR=$c2->new_avg_acceptance_rate;
            $c1NewAvg=(($c1NAAR*$c1Count)-$s1->acceptance_rate+$s2->acceptance_rate)/$c1Count;
            $c2NewAvg=(($c2NAAR*$c2Count)-$s2->acceptance_rate+$s1->acceptance_rate)/$c2Count;
            $oldDiff=abs($c1NAAR-$c2NAAR);
            $newDiff=abs($c2NewAvg-$c1NewAvg);
            if($newDiff<$oldDiff){
                $assignments[$c1->id][]=$s2;
                $assignments[$c2->id][]=$s1;
                unset($assignments[$c1->id][$s1Key]);
                unset($assignments[$c2->id][$s2Key]);
                $c1->new_avg_acceptance_rate=$c1NewAvg;
                $c2->new_avg_acceptance_rate=$c2NewAvg;
                $ctr++;
            }
        }
    }
    
    public static function priorityDistribution($students,$classes,$reversed) {
        $remaining=$students->count();
        $myMax=ceil(($remaining*1.0)/$classes->count());
        $maxes=[];
        $classI=1;
        $stdI=0;

        //assign acceptance rate to students
        foreach($students as $student){
            AtPerformance::assignStudentAcceptanceRate($student,true);
        }
        //sort students
        AtPerformance::sortByAcceptanceRate($students,false);
        //sort classes
        $orderBy=$reversed?"sortBy":"sortByDesc";
        $classes=$classes->$orderBy('name');
        //loop over classes to assign students
        foreach($classes as $class){
            AtPerformance::assignClassAvgAcceptingRate($class,true);
            $free=$class->max_number-$class->students()->count();
            if($remaining<$myMax){
                $maxes[$class->id]=$remaining;
                $goal=$remaining;
            }
            elseif($free<$myMax){
                $remaining-=$free;
                $myMax=ceil($remaining/($classes->count()-$classI));
                $maxes[$class->id]=$free;
                $goal=$free;
            }else{
                $remaining-=$myMax;
                $maxes[$class->id]=$myMax;
                $goal=$myMax;
            }
            $class->new_avg_acceptance_rate=$class->avg_acceptance_rate;
            while($goal--){
                $student=$students[$stdI++];
                $assignments[$class->id][]=$student;
                self::updateAvgAcceptanceRate($class,$student->acceptance_rate);
            }
            $classI++;
        }
        $students->makeHidden('atMarks');
        self::prepareClassesVisualizing($classes,$assignments);
        return $classes;
    }
    private static function updateAvgAcceptanceRate(&$class,$newValue){
        //requires a class with fields: new_avg_acceptance_rate , new_students_count 
        //student count before adding the new student
        $studentCount=$class->students()->count()
            +$class->new_students_count;
        //get the sum of values before the new student
        $arSum=$class->new_avg_acceptance_rate * $studentCount;
        $newValue=($arSum + $newValue)/($studentCount+1);
        $class->new_avg_acceptance_rate=$newValue;
        //got the new avg.. 
        //now add this student to class's new students count
        $class->new_students_count++;
    }

    private static function classCanAddStudent($class):bool{
        return $class->students()->count()+
        $class->new_students_count<$class->max_number;
    }

    private static function prepareClassesVisualizing(&$classes,$assignments,$reverse=true){
        foreach($classes as $class){
            $class->newStudents=$assignments[$class->id]??[];
            $n='new_avg_acceptance_rate';
            $class->$n=
            number_format($class->$n,2)+0;
        }
        $classes=collect($classes)->sortByDesc('new_avg_acceptance_rate')->values();
    }

}
