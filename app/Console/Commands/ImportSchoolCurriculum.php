<?php

namespace App\Console\Commands;

use App\Models\v1\School;
use Illuminate\Console\Command;

class ImportSchoolCurriculum extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:curriculum';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import curriculum from tbl_sch big data v1';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $schools = School::where('sch_id', '!=', '')->get();
        foreach ($schools as $school) {
            $curriculum = $school->sch_curriculum;
            

        }


        return Command::SUCCESS;
    }
}
