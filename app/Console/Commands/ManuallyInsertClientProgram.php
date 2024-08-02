<?php

namespace App\Console\Commands;

use App\Models\ClientProgram;
use App\Models\UserClient;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ManuallyInsertClientProgram extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'manually:change_client_to_potential';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Change clients into potential';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $sql = DB::table('client as c')->
            where('c.lead_id', 'LS017')->where('c.pic_id', 188)->
            whereRaw('not exists(select * from tbl_client_prog cp where client_id = c.id)')->
            select('id')->get();

        $progressBar = $this->output->createProgressBar(count($sql));
        $progressBar->start();
        Log::debug('clients changed to potential', $sql->pluck('id')->toArray());

        DB::beginTransaction();
        try {

            foreach ($sql as $value) {
                $client_id = $value->id;
    
                ClientProgram::create([
                    'client_id' => $client_id,
                    'prog_id' => 'AAUP',
                    'lead_id' => 'LS008',
                    'eduf_lead_id' => NULL,
                    'first_discuss_date' => '2024-07-20 09:00:00',
                    'status' => 0,
                    'empl_id' => 188,
                    'created_at' => '2024-07-20 09:00:00',
                    'updated_at' => '2024-07-20 09:00:00'
                ]);
    
                UserClient::where('id', $client_id)->update(['category' => 'potential']);
                DB::commit();
                $progressBar->advance();

                $this->info($client_id);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error($e->getMessage());
            return Command::FAILURE;
        }

        $progressBar->finish();

        return Command::SUCCESS;
    }
}
