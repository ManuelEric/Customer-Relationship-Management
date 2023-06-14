<?php

namespace App\Repositories;

use App\Interfaces\AcadTutorRepositoryInterface;
use App\Models\AcadTutorDetail;
use App\Models\Reminder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class AcadTutorRepository implements AcadTutorRepositoryInterface
{
    public function getAllScheduleAcadTutorH1Day()
    {

        $acadTutorHaventReceivedMail = Reminder::
            where('content', 'Academic Tutoring H1')->
            where('sent_status', 1)->
            pluck('foreign_identifier')->toArray();

        // declare new variable
        $now = Carbon::now();
        $h1_days_ahead = $now->addDays(1);
        $h1_days_ahead = $h1_days_ahead->format('Y-m-d');

        return AcadTutorDetail::
            whereNotIn('id', $acadTutorHaventReceivedMail)->
            where('date', $h1_days_ahead)->
            orderBy(
                DB::raw('CONCAT(date, " ", time)', 'ASC')
            )->get();
    }

    public function getAllScheduleAcadTutorT3Hours()
    {
        $acadTutorHaventReceivedMail = Reminder::where('content', 'Academic Tutoring T3')->where('sent_status', 1)->pluck('foreign_identifier')->toArray();

        // declare new variable
        $now = Carbon::now();
        
        $t3_hours_ahead = $now->addHours(3);
        $t3_hours_ahead = $t3_hours_ahead->format('H');

        return AcadTutorDetail::
            whereNotIn('id', $acadTutorHaventReceivedMail)->
            where('date', date('Y-m-d'))->
            whereBetween('time', [$t3_hours_ahead.':00:00', $t3_hours_ahead.':59:59'])->
            orderBy(
                DB::raw('CONCAT(date, " ", time)', 'ASC')
            )->get();
    }
    
    public function markAsSent($sentDetail)
    {
        return Reminder::create($sentDetail);
    }

}
