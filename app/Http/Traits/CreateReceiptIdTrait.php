<?php
namespace App\Http\Traits;

use Carbon\Carbon;

trait CreateReceiptIdTrait {

    public function getLatestReceiptId($last_id, $prog_id) {

        if($last_id == null){
            $last_id = 0;
        }

        $now = Carbon::now();
        $thisMonth = $now->month;
        $monthOfRoman = $this->numberToRoman($thisMonth);
        $thisYear = substr($now->year, 2, 3);

        $increment = str_pad($last_id+1,4, "0" , STR_PAD_LEFT);
        
        $inv_id = $increment . '/REC-JEI/' . $prog_id . '/' . $monthOfRoman . '/' . $thisYear;

        return $inv_id;
    }

    protected function numberToRoman($number) {
        $map = array('M' => 1000, 'CM' => 900, 'D' => 500, 'CD' => 400, 'C' => 100, 'XC' => 90, 'L' => 50, 'XL' => 40, 'X' => 10, 'IX' => 9, 'V' => 5, 'IV' => 4, 'I' => 1);
        $returnValue = '';
        while ($number > 0) {
            foreach ($map as $roman => $int) {
                if($number >= $int) {
                    $number -= $int;
                    $returnValue .= $roman;
                    break;
                }
            }
        }
        return $returnValue;
    }
}