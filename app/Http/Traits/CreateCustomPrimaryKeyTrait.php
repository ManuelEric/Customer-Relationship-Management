<?php
namespace App\Http\Traits;

trait CreateCustomPrimaryKeyTrait {

    public function add_digit($number) {
        $return = '';
        $len = strlen((string) $number);
        $max_len = 4;
        for ($i = $len ; $i < $max_len ; $i++) {
            $return .= 0;
        }
        return $return .= $number;
    }

    public function remove_primarykey_label($id, $characters /* how many characters need to be removed */)
    {
        return substr($id, $characters);
    }
}