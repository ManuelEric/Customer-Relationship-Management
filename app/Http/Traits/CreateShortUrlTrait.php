<?php

namespace App\Http\Traits;

use AshAllenDesign\ShortURL\Facades\ShortURL;

trait CreateShortUrlTrait
{

    public function createShortUrl($destinationUrl, $key)
    {
        $shortUrl = ShortURL::destinationUrl($destinationUrl)
            ->urlKey($key)
            ->make()
            ->default_short_url;

        return $shortUrl;

    }
}
