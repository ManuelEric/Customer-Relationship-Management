<?php

namespace Tests\Unit;

use Illuminate\Support\Carbon;

test('calculate graduation year now', function () {
    // Use the trait within the test closure
    $class = new class
    {
        use \App\Http\Traits\GetGradeAndGraduationYear; // Adjust namespace accordingly
    };

    $grade = 12;
    $result = $class->getGraduationYearNow($grade);
    expect($result)->toBe(2026);
});

test('calculate grade now', function () {
    // Use the trait within the test closure
    $class = new class
    {
        use \App\Http\Traits\GetGradeAndGraduationYear; // Adjust namespace accordingly
    };

    $submitted_year = Carbon::parse('2024-03-07 08:32:00')->format('Y');
    $submitted_month = Carbon::parse('2024-03-07 08:32:00')->format('m');
    $grade = 11;
    $result = $class->getRealGrade(date('Y'), $submitted_year, date('m'), $submitted_month, $grade);
    expect($result)->toBe(13);
});
