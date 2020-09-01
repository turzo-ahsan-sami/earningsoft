<?php

namespace App\Service;

use Carbon\Carbon;
use DateInterval;
use DateTime;


/**
*
* Date Utitity Class 
*
* @author hafij <hafij.to@gmail.com>
*
*/
class DateUtility
{

    /**
    *
    * Count day by date difference
    *
    * @param  string $from
    * @param  string $to
    * @return string $offset
    *
    */
    public static function howDays($from, $to)
    {
        $first_date = strtotime($from);
        $second_date = strtotime($to);
        $offset = $second_date - $first_date;
        return floor($offset / 60 / 60 / 24);
    }


    /**
    *
    * Get total month from date interval
    *
    * @param  DateInterval $interval
    * @return int
    *
    */
    public static function getTotalMonthFromInterval(DateInterval $interval)
    {
        // dd($interval);
        return intval($interval->days / 30);
    }

    /**
    * Calculates how many months 
    *
    * @param  string $startDate 
    * @param  string $endDate   
    *
    * @return int
    */
    public static function getDateDiffInMonth($startDate, $endDate)
    {
        $date1 = Carbon::parse($startDate);
        $date2 = Carbon::parse($endDate)->addDays(1);

        // Tester
        // dd($date1, $date2,$date1->diffInMonths($date2));
        return $date1->diffInMonths($date2);
    }

    /**
    *
    * convert day to year
    *
    * @param int $days
    * @return int $years_remaining
    *
    */
    public static function daysToYearsCalculate($days)
    {
        //divide by 365 and throw away the remainder
        $years_remaining = intval($days / 365);
        $days_remaining = $days % 365;

        return $years_remaining;
    }

    /**
    *
    * Get Date difference
    *
    * @param string $startDate
    * @param string $endDate
    * @return DateInterval $endDate
    *
    */
    public static function dateDiff($startDate, $endDate)
    {
        $startDate = new DateTime($startDate);
        $endDate = new DateTime($endDate);
        return $endDate->diff($startDate);
    }
}
