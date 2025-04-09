<?php

namespace App\Services\Instance;

use App\Interfaces\ClientRepositoryInterface;
use App\Interfaces\ProgramRepositoryInterface;
use App\Interfaces\ReasonRepositoryInterface;
use App\Interfaces\SchoolRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class SchoolService 
{
    protected ProgramRepositoryInterface $programRepository;
    protected ReasonRepositoryInterface $reasonRepository;
    protected SchoolRepositoryInterface $schoolRepository;
    protected ClientRepositoryInterface $clientRepository;

    public function __construct(ProgramRepositoryInterface $programRepository, ReasonRepositoryInterface $reasonRepository, SchoolRepositoryInterface $schoolRepository, ClientRepositoryInterface $clientRepository) 
    {
        $this->programRepository = $programRepository;
        $this->reasonRepository = $reasonRepository;
        $this->schoolRepository = $schoolRepository;
        $this->clientRepository = $clientRepository;
    }

    public function snSetAttributeSchoolDetail(Array $validated, $is_update = false)
    {
        $school_details = [];

        $represent_max_length = count($validated['schdetail_name']);
        for ($i = 0; $i < $represent_max_length; $i++) {
            if(!$is_update){
                $school_details[] = [
                    'sch_id' => $validated['sch_id'],
                    'schdetail_fullname' => $validated['schdetail_name'][$i],
                    'schdetail_email' => $validated['schdetail_mail'][$i],
                    'schdetail_grade' => $validated['schdetail_grade'][$i],
                    'schdetail_position' => $validated['schdetail_position'][$i],
                    'schdetail_phone' => $validated['schdetail_phone'][$i],
                    'is_pic' => $validated['is_pic'][$i],
                ];
            }else{
                $school_details = [
                    'sch_id' => $validated['sch_id'],
                    'schdetail_fullname' => $validated['schdetail_name'][$i],
                    'schdetail_email' => $validated['schdetail_mail'][$i],
                    'schdetail_grade' => $validated['schdetail_grade'][$i],
                    'schdetail_position' => $validated['schdetail_position'][$i],
                    'schdetail_phone' => $validated['schdetail_phone'][$i],
                    'is_pic' => $validated['is_pic'][$i],
                ];
            }
        }

        return $school_details;
    }

    # purpose:
    # get list school
    # select school name
    # use for filter client student
    public function snGetListSchool($request)
    {
        $grouped =  new Collection();

        if($request->ajax())
        {
            $filter['sch_name'] = trim($request->term);
            $list_school = $this->schoolRepository->rnGetPaginateSchool(['sch_name'], $filter);
    
            $grouped = $list_school->mapToGroups(function ($item, $key) {
                return [
                    $item['data'] . 'results' => [
                        'id' => $item->sch_name,
                        'text' => $item->sch_name
                    ],
                ];
            });
    
            $more_pages=true;
            if (empty($list_school->nextPageUrl())){
                $more_pages=false;
            }
    
            $grouped['pagination'] = [
                'more' => $more_pages
            ];
    
            return $grouped;
         
        }
    }

    public function snDomicileTracker()
    {
        $mentees = $this->clientRepository->getExistingMenteesAPI();
        
        $mapped = $mentees->map(function ($mentee){
            return Collect([
                'client_id' => $mentee->id,
                'domicile' => isset($mentee->school) ? $mentee->school->sch_city : null
            ]);
        });

        $count_by_domicile = $mapped->countBy('domicile');
        
        return $count_by_domicile;
    }
}