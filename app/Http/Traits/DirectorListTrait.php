<?php

namespace App\Http\Traits;

use AshAllenDesign\ShortURL\Facades\ShortURL;

trait DirectorListTrait
{
    protected $directors = [
        [
            'name' => 'Devi Kasih',
            'email' => 'devi.kasih@all-inedu.com'
        ],
        [
            'name' => 'Nicholas Hendra Soepriatna',
            'email' => 'n.hendra@all-inedu.com'
        ]
    ];

    public function getDirectorByEmail(string $email)
    {
        # returned directors name
        $find_index = array_search($email, $this->directors);
        return $this->directors[$find_index]['name'];
    }
}
