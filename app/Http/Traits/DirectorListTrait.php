<?php

namespace App\Http\Traits;

use AshAllenDesign\ShortURL\Facades\ShortURL;

trait DirectorListTrait
{
    protected $directors = [
        [
            'name' => 'Devi Kasih',
            'email' => 'devi.kasih@edu-all.com'
        ],
        [
            'name' => 'Nicholas Hendra Soepriatna',
            'email' => 'n.hendra@edu-all.com'
        ]
    ];

    public function getDirectorByEmail(string $email)
    {
        # returned directors name
        $find_index = array_search($email, array_column($this->directors, 'email'));
        return $this->directors[$find_index]['name'];
    }
}
