<?php

namespace App\Console\Commands\Temp;

use App\Interfaces\UserRepositoryInterface;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class RenameFolderAttachmentUserCommand extends Command
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

    # ===== NOTE =====
    # this cron only running after up production crm adjustment
    # after that this cron will be removed

    # Purpose:
    # Rename folder attachment user from before adjustment to after adjustment
    # Before adjusment folder name by extension_id, Ex. EMPL-001
    # After adjusment extension_id has deleted and folder name change with id (type uuid), Ex. 11s2d048-2d5a-2d22-abb2-5d732432d947d
    public function handle()
    {
        
        DB::beginTransaction();
        try {

            $users = $this->userRepository->rnGetAllUsers();

            foreach($users as $user){
               if($user->idcard != null || $user->cv != null || $user->tax != null || $user->health_insurance != null || $user->empl_insurance != null){
                    $extended_id = null; #Extended Id column before adjusment
                
                    Storage::move('public/uploaded_file/user/'. $extended_id, 'public/uploaded_file/user/'. $user->id);

               }
            }

            DB::commit();

        } catch (Exception $e) {
            
            DB::rollBack();
            Log::info('Cron rename folder attachment user not working normal. Error : '. $e->getMessage() .' | Line '. $e->getCode());

        }

        return Command::SUCCESS;
        
    }
}

