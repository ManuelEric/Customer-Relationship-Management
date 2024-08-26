<?php

namespace App\Http\Traits;

use Carbon\Carbon;

trait CreateReceiptIdTrait
{

    public function getLatestReceiptId($last_id, $prog_id, $receiptDetails,  $bundleInfo = null)
    {

        if ($last_id == null) {
            $last_id = 0;
        }

        // $now = Carbon::now();
        $thisMonth = date('m', strtotime($receiptDetails['receipt_date']));
        $monthOfRoman = $this->numberToRoman($thisMonth);
        $thisYear = substr(date('Y', strtotime($receiptDetails['receipt_date'])), 2, 2);

        $increment = str_pad($last_id + 1, 4, "0", STR_PAD_LEFT);

        $receipt_id = $increment . '/REC-JEI/' . $prog_id . '/' . $monthOfRoman . '/' . $thisYear;
        
        if($bundleInfo != null){
            if($bundleInfo['is_bundle'] != NULL && $bundleInfo['is_bundle'] > 0){
                $receipt_id = $last_id . '/REC-JEI/BDL/' . $monthOfRoman . '/' . $thisYear . '/' . $prog_id;
                
                if($bundleInfo['is_cross_client']){
                    $receipt_id = $last_id . '/REC-JEI/BDL'.$bundleInfo['increment_bundle'].'/' . $monthOfRoman . '/' . $thisYear . '/' . $prog_id;
                }
            }
        }

        return $receipt_id;
    }

    protected function numberToRoman($number)
    {
        $map = array('M' => 1000, 'CM' => 900, 'D' => 500, 'CD' => 400, 'C' => 100, 'XC' => 90, 'L' => 50, 'XL' => 40, 'X' => 10, 'IX' => 9, 'V' => 5, 'IV' => 4, 'I' => 1);
        $returnValue = '';
        while ($number > 0) {
            foreach ($map as $roman => $int) {
                if ($number >= $int) {
                    $number -= $int;
                    $returnValue .= $roman;
                    break;
                }
            }
        }
        return $returnValue;
    }
}
