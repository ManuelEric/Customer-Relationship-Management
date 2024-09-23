<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Interfaces\TagRepositoryInterface;
use Illuminate\Http\Request;

class TagController extends Controller
{

    protected TagRepositoryInterface $tagRepository;

    public function __construct(TagRepositoryInterface $tagRepository)
    {
        $this->tagRepository = $tagRepository;
    }

    public function getTags(Request $request)
    {
        $countries = $this->tagRepository->getAllCountries();
        if (!$countries) {
            return response()->json([
                'success' => true,
                'message' => 'No destination country found.'
            ]);
        }

        # remove the `other` tag
        # map the data that being shown to the user
        $mappedTags = $countries->where('name', '!=', 'other')->map(function ($value) {
            return [
                'id' => $value->id,
                'country' => $value->name,
                'tag' => $value->tagCountry->name
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'There are destination country found.',
            'data' => $mappedTags->values()
        ]);
    }
}
