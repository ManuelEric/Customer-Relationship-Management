<?php
namespace App\Http\Traits;

trait FindStatusClientTrait {

    public function getStatusClientCode($statusClient)
    {
        switch ($statusClient) {
            case "prospective":
                $status = 0;
                break;

            case "potential":
                $status = 1;
                break;

            case "current":
                $status = 2;
                break;

            case "completed":
                $status = 3;
                break;
        }

        return $status;
    }
}