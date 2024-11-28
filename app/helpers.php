<?php

if (! function_exists('toPercentage'))
{
    /**
     * Create a new function for the percentage converter.
     *
     * @param  \DateTimeZone|string|null  $tz
     * @return \Illuminate\Support\Str
     */
    function toPercentage($divider, $main): String
    {
        if ($divider != 0)
            $percentage = round(($main / $divider ) * 100);
        else
            $percentage = $main * 100;
    
        return $percentage;
    }
}
