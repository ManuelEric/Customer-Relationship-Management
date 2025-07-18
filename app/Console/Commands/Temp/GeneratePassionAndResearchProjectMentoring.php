<?php

namespace App\Console\Commands\Temp;

use App\Models\pivot\UserStream;
use App\Models\pivot\UserSubject;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class GeneratePassionAndResearchProjectMentoring extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:passion-research-project-mentoring';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate passion project mentoring and research project mentoring for external mentor agreement based on engagement type "Subject specific"';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $user_streams = UserStream::query()->
        // whereHas('engagement_type', function ($query) {
        //     $query->where('phase_detail_name', 'Subject-Specific Project Mentoring');
        // })->
        where('package', 'Subject-Specific Project Mentoring')->get();

        $bar = $this->output->createProgressBar(count($user_streams));
        $bar->start();
        
        DB::beginTransaction();
        foreach ($user_streams as $user_stream) {

            $this->newLine();
            $this->info("Processing User Stream ID: {$user_stream->id}");
            // continue;
            
            // change that previously was "Subject-Specific Project Mentoring" to "Passion Project Mentoring"
            $user_stream->package = "Passion Project Mentoring";
            $user_stream->save();

            // insert new record for "Research Project Mentoring"
            $new_user_stream = $user_stream->replicate();
            $new_user_stream->package = "Research Project Mentoring";
            $new_user_stream->save();

            $bar->advance();
            DB::commit();
        }

        $bar->finish();
    }
}
