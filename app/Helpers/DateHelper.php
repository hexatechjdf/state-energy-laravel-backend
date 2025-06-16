<?php

use Carbon\Carbon;

if (!function_exists('getDateRangeByFilter')) {
    function getDateRangeByFilter($filter, $customStart = null, $customEnd = null)
    {
        $startTime = now();
        $endTime = now();

        switch ($filter) {
            case 'today':
                $startTime = now()->startOfDay();
                $endTime = now()->endOfDay();
                break;

            case 'this_week':
                $startTime = now()->startOfWeek();
                $endTime = now()->endOfWeek();
                break;

            case 'this_month':
                $startTime = now()->startOfMonth();
                $endTime = now()->endOfMonth();
                break;

            case 'next_7_days':
                $startTime = now();
                $endTime = now()->addDays(7);
                break;

            case 'custom':
                $startTime = $customStart ? Carbon::parse($customStart) : now()->startOfDay();
                $endTime = $customEnd ? Carbon::parse($customEnd) : now()->endOfDay();
                break;

            default:
                $startTime = now()->startOfDay();
                $endTime = now()->endOfDay();
                break;
        }

        // Return timestamps in milliseconds
        return [
            'start' => $startTime->timestamp * 1000,
            'end'   => $endTime->timestamp * 1000,
        ];
    }
}
