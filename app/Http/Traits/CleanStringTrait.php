<?php

namespace App\Http\Traits;

trait CleanStringTrait
{
    function cleanString($input)
    {
        // Step 1: Remove all special characters except letters and digits
        $cleaned = preg_replace('/[^A-Za-z0-9 ]/', '', $input);

        // Step 2: Replace multiple spaces with a single space
        $cleaned = preg_replace('/\s+/', ' ', $cleaned);

        // Step 3: Trim leading and trailing spaces
        $cleaned = trim($cleaned);

        return $cleaned;
    }
}
