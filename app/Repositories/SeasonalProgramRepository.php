<?php

namespace App\Repositories;

use App\Interfaces\SeasonalProgramRepositoryInterface;
use App\Models\SeasonalProgram;
use Carbon\Carbon;
use DataTables;

class SeasonalProgramRepository implements SeasonalProgramRepositoryInterface
{
    public function getDataTables($model)
    {
        return DataTables::eloquent($model)->
                addColumn('program_name', function ($data) {
                    return $data->program->program_name;
                })->
                addColumn('start_program_date', function ($data) {
                    return $data->start_string;
                })->
                addColumn('end_program_date', function ($data) {
                    return $data->end_string;
                })->
                addColumn('sales_date', function ($data) {
                    return $data->sales_date_string;
                })->
                filterColumn('program_name', function ($query, $keyword) {
                    $query->whereHas('program', function ($subQuery) use ($keyword) {
                        $subQuery->where('program_name', 'like', '%'.$keyword.'%');
                    });
                })->
                make(true);
    }

    public function getSeasonalPrograms($asDatatables = false)
    {
        $query = SeasonalProgram::query();

        return $asDatatables === false ? $query->orderBy('created_at', 'desc')->get() : $query->orderBy('prog_id', 'asc');
    }

    public function getSeasonalProgramById($id)
    {
        return SeasonalProgram::find($id);
    }

    public function storeSeasonalProgram($details)
    {
        return SeasonalProgram::create($details);
    }

    public function updateSeasonalProgram($id, array $newDetails)
    {
        return tap(SeasonalProgram::find($id))->update($newDetails);
    }

    public function deleteSeasonalProgram($id)
    {
        return SeasonalProgram::destroy($id);
    }
}
