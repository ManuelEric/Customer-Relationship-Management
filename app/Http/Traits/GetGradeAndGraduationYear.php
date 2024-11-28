<?php

namespace App\Http\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

trait GetGradeAndGraduationYear
{
    public function getRealGrade($ynow, $yinput, $mnow, $minput, $ginput)
    {
        $gradeNow = null;
        if(($mnow >= 7 && $minput < 7) && ($ynow > $yinput)) {
            $gradeNow = ($ynow - $yinput) + ($ginput + 1);
        }else if (($mnow < 7 && $minput >= 7) && ($ynow > $yinput)) {
            $gradeNow = ($ynow - $yinput) + ($ginput - 1);
        }else if (($mnow >= 7 && $minput < 7) && ($ynow = $yinput)) {
            $gradeNow = $ginput + 1;  
        }else if (($mnow < 7 && $minput >= 7) && ($ynow = $yinput)) {
            $gradeNow = ($ynow - $yinput) + ($ginput - 1);  
        }else if ((($mnow < 7 && $minput < 7) || ($mnow >= 7 && $minput >= 7)) && ($ynow >= $yinput)){
            $gradeNow = ($ynow - $yinput) + $ginput;
        }else{
            $gradeNow = $ginput;  
        } 

        return $gradeNow;
    }

    public function getGradeByGraduationYear($graduationYear)
    {
        $diffYear = $graduationYear - date('Y');
        $grade = 12 - $diffYear;
        $monthNow = date('m');

        if($monthNow >= 7){
            $grade++;
        }
       
        return $grade;
    }

    public function getGraduationYearNow($gradeNow)
    {
        $graduationYearNow = null;
        $yearNow = date('Y');
        $monthNow = date('m');

        if($monthNow >= 7){
            $graduationYearNow = (12-$gradeNow) + $yearNow + 1;
        }else{
            $graduationYearNow = (12-$gradeNow) + $yearNow;
        }

        return $graduationYearNow;
    }
}