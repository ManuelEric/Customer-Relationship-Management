<?php

namespace App\Http\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

trait GetGradeAndGraduationYear
{
    public function getRealGrade($current_year, $submitted_year, $current_month, $submitted_month, $grade)
    {
        $expected_grade = null;
        if(($current_month >= 7 && $submitted_month < 7) && ($current_year > $submitted_year)) {
            $expected_grade = ($current_year - $submitted_year) + ($grade + 1);
        }else if (($current_month < 7 && $submitted_month >= 7) && ($current_year > $submitted_year)) {
            $expected_grade = ($current_year - $submitted_year) + ($grade - 1);
        }else if (($current_month >= 7 && $submitted_month < 7) && ($current_year = $submitted_year)) {
            $expected_grade = $grade + 1;  
        }else if (($current_month < 7 && $submitted_month >= 7) && ($current_year = $submitted_year)) {
            $expected_grade = ($current_year - $submitted_year) + ($grade - 1);  
        }else if ((($current_month < 7 && $submitted_month < 7) || ($current_month >= 7 && $submitted_month >= 7)) && ($current_year >= $submitted_year)){
            $expected_grade = ($current_year - $submitted_year) + $grade;
        }else{
            $expected_grade = $grade;  
        } 

        return $expected_grade;
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