<?php

namespace App\Http\Resources\Mentee;

use App\Http\Traits\MentorTypeTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class ActiveMenteeCollectionResource extends ResourceCollection
{
    use MentorTypeTrait;

    public $paginate, $filter;
    public function __construct($resource, $paginate, $filter = [])
    {
        parent::__construct($resource);
        $this->paginate = $paginate;
        $this->filter = $filter;
    }
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $collections = [];
        foreach ($this->collection as $single)
        {
            # determine which type of mentor does the user has
            $latest_admission = $single->clientProgram[0];
            # with orderByPivot, it helps get the latest record 
            $logged_in_mentor_type = $latest_admission->clientMentor()->where('users.id', Auth::guard('api')->user()->id)->orderByPivot('id', 'desc')->get();
            $mapped_mentor_type = $logged_in_mentor_type->map(function ($item) {
                return [
                    'code' => $item->pivot->type,
                    'alias' => $this->tnDefineMentorType($item->pivot->type)
                ];
            });

            $collections[] = [
                'id' => $single->id,
                'full_name' => $single->full_name,
                'mail' => $single->mail,
                'phone' => $single->phone,
                'dob' => $single->dob,
                'city' => $single->city,
                'address' => $single->address,
                'sch_name' => $single->school->sch_name ?? null,
                'sch_city' => $single->school->sch_city ?? null,
                // 'grade' => $single->grade_now,
                'grade' => $single->grade_now > 12 ? preg_match('/community college/i', $single->school->sch_name) ? 'Community College' : 'in university/working' : $single->grade_now,
                'application_year' => $single->application_year,
                'mentoring_progress_status' => $single->mentoring_progress_status,
                'clientprog_id' => $latest_admission->clientprog_id,
                'act_as' => $mapped_mentor_type,
                'code_array' => $mapped_mentor_type->pluck('code')->toArray(),
                'alias_array' => $mapped_mentor_type->plucK('alias')->toArray(),
                'latest_update' => count($single->mentoringLogs) > 0 ? $single->mentoringLogs()->latest()->first()->updated_at : null,
                'joining_year' => Carbon::parse($single->clientProgram()->whereRelation('program.main_prog', 'prog_name', 'Admissions Mentoring')->latest()->first()->success_date)->format('Y'),
                'package' => $latest_admission->package,
                'program_name' => $latest_admission->program->program_name,
            ];
        }

        if ( $this->paginate != null )
        {
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
