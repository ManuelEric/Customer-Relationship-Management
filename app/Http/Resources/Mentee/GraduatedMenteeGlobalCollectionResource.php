<?php

namespace App\Http\Resources\Mentee;

use App\Http\Traits\MentorTypeTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class GraduatedMenteeGlobalCollectionResource extends ResourceCollection
{
    use MentorTypeTrait;

    public $paginate;

    public function __construct($resource, $paginate)
    {
        parent::__construct($resource);
        $this->paginate = $paginate;
    }

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        foreach ($this->collection as $single) {
            $have_university_acceptance = count($single->universityAcceptance) > 0 ? true : false;

            // actually, there will be more than 1 university acceptance
            // but we only need the last one
            // so we take the last one by using count($single->universityAcceptance)-1
            $university_acceptance = $have_university_acceptance ? $single->universityAcceptance[count($single->universityAcceptance) - 1] : null;
            $university_name = $have_university_acceptance ? $university_acceptance->univ_name : null;
            $major_group = $have_university_acceptance && $university_acceptance->pivot->major_group_id !== null ? $university_acceptance->pivot->major_group->mg_name : null;
            $major = $have_university_acceptance ? $university_acceptance->pivot->get_major_name : null;
            $created_university_acceptance_at = $have_university_acceptance
                ? Carbon::parse($university_acceptance->pivot->created_at)->format('Y-m-d H:i:s')
                : null;

            // determine which type of mentor does the user has
            $latest_admission = $single->clientProgram[0];
            // with orderByPivot, it helps get the latest record
            $select_profile_building_mentor = $latest_admission->clientMentor()->first()?->full_name ?? null;
            // $logged_in_mentor_type = $latest_admission->clientMentor()->where('users.id', Auth::guard('api')->user()->id)->orderByPivot('id', 'desc')->get();
            $mapped_mentor_type = collect([
                'code' => 1,
                'alias' => $this->tnDefineMentorType(1),
            ]);

            $collections[] = [
                'id' => $single->id,
                'full_name' => $single->full_name,
                'university_name' => $university_name,
                'major_group' => $major_group,
                'major' => $major,
                'application_year' => $single->application_year,
                'clientprog_id' => $latest_admission->clientprog_id,
                'act_as' => $mapped_mentor_type,
                'code_array' => [1],
                'alias_array' => [$this->tnDefineMentorType(1)],
                'profile_building_mentor' => $select_profile_building_mentor,
                'created_at' => $created_university_acceptance_at,
            ];
        }

        if ($this->paginate != null) {
            return [
                'current_page' => $this->currentPage(),
                'data' => $collections,
                'first_page_url' => $this->url(1),
                'from' => $this->firstItem(),
                'last_page' => $this->lastPage(),
                'last_page_url' => $this->url($this->lastPage()),
                'links' => $this->linkCollection(),
                'next_page_url' => $this->nextPageUrl(),
                'path' => $this->url($this->currentPage()),
                'per_page' => $this->perPage(),
                'prev_page_url' => $this->previousPageUrl(),
                'to' => $this->lastItem(),
                'total' => $this->total(),
            ];
        } else {
            return $collections;
        }
    }

    /**
     * Customize the outgoing response for the resource.
     */
    public function withResponse(Request $request, JsonResponse $response): void
    {
        $response->header('Accept', 'application/json');
    }
}
