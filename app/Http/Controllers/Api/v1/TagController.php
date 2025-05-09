<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Interfaces\TagRepositoryInterface;
use Illuminate\Http\JsonResponse;
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
        $mapped_tags = $countries->where('name', '!=', 'other')->map(function ($value) {
            return [
                'id' => $value->id,
                'country' => $value->name,
                'tag' => $value->tagCountry->name
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'There are destination country found.',
            'data' => $mapped_tags->values()
        ], JsonResponse::HTTP_OK, [], options: JSON_INVALID_UTF8_IGNORE);
    }
}
