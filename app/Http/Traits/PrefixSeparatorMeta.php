<?php

namespace App\Http\Traits;

trait PrefixSeparatorMeta
{
    public function getPrefix(string $form_name): string
    {
        $explode = explode('-', $form_name);
        return $explode[0];
        // return substr($form_name, 0, 2);
    }

    public function getIdentifier(string $form_name): string
    {
        $prefix = $this->getPrefix($form_name);
        $offset = /*$prefix == "PR" ? 3 : 4*/ 3;
        $form_name_without_prefix = substr($form_name, $offset);
        
        
        /** 
         * assume the form name is "PR-AAUP" or "EV-EVT-001"
         * and without prefix, it would be "AAUP" or "EVT-001"
         * 
         */
        $explode = explode('_', $form_name_without_prefix);
        
        # if the ID is EVT-001-form_code or PR-AAUP-form_name
        # we only want to get the EVT-001-form_name or AAUP-form_name
        # so we need to check if count of explode is higher than 2, otherwise get the first index which should be AAUP
        return $explode[0];
    }
}