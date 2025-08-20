<?php

if (! function_exists('toPercentage')) {
    /**
     * Create a new function for the percentage converter.
     */
    function toPercentage($divider, $main): float|int
    {
        if ($divider != 0) {
            $percentage = round(($main / $divider) * 100);
        } else {
            $percentage = $main * 100;
        }

        return $percentage;
    }
}
