<?php

namespace App\Traits;

use Carbon\Carbon;

trait ConvertsDates
{
    /**
     * Convert a given UTC date to the user's timezone.
     *
     * @param  string|\DateTime $date
     * @return string
     */
    public static function convertToUserTimezone($date)
    {
        $timezone = auth()->user()->timezone ?? config('app.timezone');
        return Carbon::parse($date)->timezone($timezone)->format('Y-m-d H:i');
    }
}
