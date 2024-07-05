<?php

namespace App\Services;

use App\Jobs\AnalyticsImportJob;
use App\Jobs\GoogleSheet\ImportClientEvent;
use App\Jobs\GoogleSheet\ImportClientProgram;
use App\Jobs\GoogleSheet\ImportParent;
use App\Jobs\GoogleSheet\ImportStudent;
use App\Jobs\GoogleSheet\ImportTeacher;
use App\Models\Client;
use App\Models\JobBatches;
use Generator;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Log;

class ImportDataService
{
    public function import($data, $type): string
    {
        $batch = Bus::batch([])
            ->name('import-data-' . $type)
            ->dispatch();

        
        $chunks = $data->chunk(10);

        foreach($chunks as $val)
        {
            switch ($type) {
                case 'student':
                    $batch->add(new ImportStudent($val));
                    break;

                case 'parent':
                    $batch->add(new ImportParent($val));
                    break;

                case 'teacher':
                    $batch->add(new ImportTeacher($val));
                    break;

                case 'client-event':
                    $batch->add(new ImportClientEvent($val));
                    break;

                case 'client-program':
                    $batch->add(new ImportClientProgram($val));
                    break;
            }
        }


        return $batch->id;
    }

}