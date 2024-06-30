<?php

namespace App\Services;

use App\Jobs\AnalyticsImportJob;
use App\Jobs\Client\ProcessGetTookIA;
use App\Jobs\GoogleSheet\ExportClient;
use App\Jobs\GoogleSheet\ImportClientEvent;
use App\Jobs\GoogleSheet\ImportClientProgram;
use App\Jobs\GoogleSheet\ImportParent;
use App\Jobs\GoogleSheet\ImportStudent;
use App\Jobs\GoogleSheet\ImportTeacher;
use App\Jobs\GoogleSheet\ProcessDefineCategory;
use App\Models\Client;
use App\Models\JobBatches;
use Generator;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Log;

class JobBatchService
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

    public function jobBatch($data, $category, $type, $sizeChunk): string
    {
       
        $batch = Bus::batch([])
            ->name($category . '-' . $type)
            ->dispatch();

        
        $chunks = $data->chunk($sizeChunk);

        foreach($chunks as $val)
        {   
            switch ($category) {
                case 'import':
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
                    break;
                
                case 'process':
                    switch ($type) {
                        case 'took-ia':
                            $batch->add(new ProcessGetTookIA($val));
                            break;
        
                    }

                case 'export':
                    switch ($type) {
                        case 'new-leads':
                            $batch->add(new ExportClient($val, $type));
                            break;
                        case 'potential':
                            $batch->add(new ExportClient($val, $type));
                            break;
                        case 'mentee':
                            $batch->add(new ExportClient($val, $type));
                            break;
                        case 'non-mentee':
                            $batch->add(new ExportClient($val, $type));
                            break;
                        case 'all':
                            $batch->add(new ExportClient($val, $type));
                            break;
                        case 'inactive':
                            $batch->add(new ExportClient($val, $type));
                            break;
        
                    }
                    break;
            }
            
        }


        return $batch->id;
    }

}