<?php
namespace App\Http\Traits;

trait CalculateGradeTrait {

    public function getGradeByGraduationYear($requestedGraduationYear)
    {
        $max_grade = 12;
        $current_year = date('Y');
        $current_month = date('m');

        # when current month greater than july
        # assumed the client has "naik kelas"
        if ($current_month > 7) 
            $grade = $max_grade - ($requestedGraduationYear - $current_year) + 1;
        else 
            $grade = $max_grade - ($requestedGraduationYear -  $current_year);

        return $grade;
    }
}