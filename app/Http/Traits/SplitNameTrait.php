<?php
namespace App\Http\Traits;

trait SplitNameTrait {

    public function split(string $fullname)
    {
        $fullname_array = explode(' ', $fullname);
        $fullname_words = count($fullname_array);

        $firstname = $lastname = null;
        if ($fullname_words > 1) {
            $lastname = $fullname[$fullname_words - 1];
            $firstname = implode(" ", $fullname_array);
        } else {
            $firstname = implode(" ", $fullname_array);
        }

        return [
            'first_name' => $firstname,
            'last_name' => $lastname
        ];
    }
}