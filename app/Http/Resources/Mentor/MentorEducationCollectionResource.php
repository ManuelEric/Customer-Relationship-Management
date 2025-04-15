<?php

namespace App\Http\Resources\Mentor;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Http\Resources\Json\ResourceCollection;

class MentorEducationCollectionResource extends ResourceCollection
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        foreach ($this->collection as $single)
        {
            $return[] = [
                'id' => $single->pivot->id ?? null, 
                'university' => $single->univ_name ?? null,
                'major' => $single->major_name ?? null,
                'degree' => $single->pivot->degree ?? null,
                'graduated_at' => $single->pivot->graduation_date ?? null,
            ];
        }

        return $return;
    }

    /**
     * Customize the outgoing response for the resource.
     */
    public function withResponse(HttpRequest $request, JsonResponse $response): void
    {
        $response->header('Accept', 'application/json');
    }
}
