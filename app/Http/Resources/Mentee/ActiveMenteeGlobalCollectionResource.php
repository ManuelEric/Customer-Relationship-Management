<?php

namespace App\Http\Resources\Mentee;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Carbon;

class ActiveMenteeGlobalCollectionResource extends ResourceCollection
{ 
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
        foreach ($this->collection as $single)
        {
            // # determine which type of mentor does the user has
            $latest_admission = $single->clientProgram->first(); 
            // # with orderByPivot, it helps get the latest record 
            $select_profile_building_mentor = $latest_admission->clientMentor()->first()?->full_name ?? null;    

            $collections[] = [
                'id' => $single->id,
                'first_name' => $single->first_name,
                'last_name' => $single->last_name,
                'full_name' => $single->full_name,
                'sch_name' => $single->school->sch_name ?? null,
                'grade' => $single->grade_now,
                'application_year' => $single->application_year,
                'mentoring_progress_status' => $single->mentoring_progress_status,
                'joining_year' => Carbon::parse($single->clientProgram()->whereRelation('program.main_prog', 'prog_name', 'Admissions Mentoring')->latest()->first()->success_date)->format('Y'),
                'clientprog_id' => $latest_admission->clientprog_id,
                'program_name' => $latest_admission->invoice_program_name,
                'free_trial' => preg_match("/free trial/i", $latest_admission->package),
                'require' => $latest_admission->program->prog_mentor,
                'package' => $latest_admission->package,
                'curriculum' => $latest_admission->curriculum,
                'invoice_id' => $latest_admission->invoice->inv_id ?? null,
                'profile_building_mentor' => $select_profile_building_mentor
            ];
        }

        if ( $this->paginate )
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
