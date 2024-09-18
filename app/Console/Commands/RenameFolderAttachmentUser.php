<?php

namespace App\Console\Commands;

use App\Interfaces\UserRepositoryInterface;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class RenameFolderAttachmentUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rename:folder_attachment_user';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rename folder attachment user for extension_id to id';

    private UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        parent::__construct();
        $this->userRepository = $userRepository;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        
        DB::beginTransaction();
        try {

            $users = $this->userRepository->getAllUsers();

            foreach($users as $user){
               if($user->idcard != null || $user->cv != null || $user->tax != null || $user->health_insurance != null || $user->empl_insurance != null){
                    $extended_id = null; #Extended Id dari skema dari database lama
                
                    Storage::move('public/uploaded_file/user/'. $extended_id, 'public/uploaded_file/user/'. $user->id);

               }
            }

            DB::commit();

        } catch (Exception $e) {
            
            DB::rollBack();
            Log::info('Cron Insert target tracking not working normal. Error : '. $e->getMessage() .' | Line '. $e->getCode());

        }

        return Command::SUCCESS;
        
    }
}

