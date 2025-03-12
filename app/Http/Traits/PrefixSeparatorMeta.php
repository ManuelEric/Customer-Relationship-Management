<?php

namespace app\Http\Traits;

trait PrefixSeparatorMeta
{
    public function getPrefix(string $form_name): string
    {
        return substr($form_name, 0, 2);
    }

    public function getIdentifier(string $form_name): string
    {
        $form_name_without_prefix = substr($form_name, 3);
        
        /** 
         * assume the form name is "PR-AAUP" or "EV-EVT-001"
         * and without prefix, it would be "AAUP" or "EVT-001"
         * 
         */
        $explode = explode('-', $form_name_without_prefix);
        
        # if the ID is EVT-001-form_code or PR-AAUP-form_name
        # we only want to get the EVT-001-form_name or AAUP-form_name
        # so we need to check if count of explode is higher than 2, otherwise get the first index which should be AAUP
        return count($explode) > 1 ? $explode[0].'-'.$explode[1] : $explode[0];
    }
}