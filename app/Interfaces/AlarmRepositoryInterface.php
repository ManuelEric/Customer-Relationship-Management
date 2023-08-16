<?php

namespace App\Interfaces;

interface AlarmRepositoryInterface 
{
    public function getDataTarget($date, $divisi);
    public function setDataTarget($dataTarget, $dataActual);
    public function setDataActual($dataActual);
    public function setAlarmLead();
    public function countAlarm();
    public function notification();

}