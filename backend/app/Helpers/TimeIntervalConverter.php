<?php
if (!function_exists('to_interval')) {
    function to_interval($year, $month, $day)
    {
        return $year * 12 * 30 * 24 * 60 * 60 + $month * 30 * 24 * 60 * 60 + $day * 24 * 60 * 60;
    }
}

if (!function_exists('interval_to_array')) {
    function interval_to_array($interval)
    {
        $year = floor($interval / (12 * 30 * 24 * 60 * 60));
        $month = floor(($interval % (12 * 30 * 24 * 60 * 60)) / (30 * 24 * 60 * 60));
        $day = floor((($interval % (12 * 30 * 24 * 60 * 60)) % (30 * 24 * 60 * 60)) / (24 * 60 * 60));
        return [
            'year' => $year,
            'month' => $month,
            'day' => $day,
        ];
    }
}
