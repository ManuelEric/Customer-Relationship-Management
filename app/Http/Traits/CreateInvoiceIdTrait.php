<?php
namespace App\Http\Traits;

use Carbon\Carbon;
use Illuminate\Support\Facades\Date;

trait CreateInvoiceIdTrait {

    public function getInvoiceId($last_id, $prog_id, $requestDate = NULL, $bundleInfo = null) {

        if($last_id == null){
            $last_id = 0;
        }

        $dateForInvoice = $requestDate !== NULL ? Carbon::createFromFormat('Y-m-d', $requestDate) : Carbon::now();
        $thisMonth = $dateForInvoice->month;
        $monthOfRoman = $this->numberToRoman($thisMonth);
        $thisYear = substr($dateForInvoice->year, 2, 3);

        $increment = str_pad($last_id+1,4, "0" , STR_PAD_LEFT);
        
        $inv_id = $increment . '/INV-JEI/' . $prog_id . '/' . $monthOfRoman . '/' . $thisYear;
        
        if($bundleInfo != null){
            if($bundleInfo['is_bundle'] != NULL && $bundleInfo['is_bundle'] > 0){
                $inv_id = $last_id . '/INV-JEI/BDL/' . $monthOfRoman . '/' . $thisYear . '/' . $prog_id;
                
                if($bundleInfo['is_cross_client']){
                    $inv_id = $last_id . '/INV-JEI/BDL'.$bundleInfo['increment_bundle'].'/' . $monthOfRoman . '/' . $thisYear . '/' . $prog_id;
                }
            }
        }

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