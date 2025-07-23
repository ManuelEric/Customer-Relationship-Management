<?php

namespace App\Livewire;

use App\Interfaces\StreamRepositoryInterface;
use App\Models\PhaseDetail;
use App\Models\pivot\UserStream;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class AddFeeAndAgreementExternalMentor extends Component
{
    use WithFileUploads;
    
    public $user, $user_role_id, $streams, $user_stream_id;
    public $isEdit = false;
 
    public $engagement_types;
    public $packages = ['Professional Sharing 1-on-1', 'Professional Sharing 2-10 Mentees', 'Professional Sharing >10 Mentees', 'Competition Mentoring', 'Subject-Specific Project Mentoring', 'Essay Mentoring', 'Essay Program Development', 'Passion Project Mentoring', 'Research Project Mentoring'];

    /* form request */
    public $stream_id, $engagement_type_id, $start_date, $end_date, $grade, $fee_individual, $agreement;
    public $package = [];

    protected function rules()
    {
        return [
            'stream_id' => 'required|exists:streams,id',
            'engagement_type_id' => 'required|exists:phase_details,id',
            'package.*' => 'required|string', // refer to packages that manually set
            'start_date' => 'required',
            'end_date' => 'required|gte:start_date',
            'grade' => 'required',
            'fee_individual' => 'required',
            'agreement' => ['nullable', 'file', 'mimes:pdf', 'max:1024', function ($attribute, $value, $fail) {
                if ( $this->isEdit == false && !$this->agreement )
                    $fail('The :attribute is required');
            }],
        ];
    }

    public function mount($user, StreamRepositoryInterface $streamRepository)
    {
        $this->user = $user;
        $this->user_role_id = $this->user->roles()->where('role_name', 'External Mentor')->first()->pivot->id;
        /** 
         * select phase detail:
         * - student club
         * - professional sharing
         * - project-based competition mentoring
         * - subject-specific project mentoring
         * - essay editing hours (ID: 11) <- previously used but since editor has agreement as well and essay editing only can be selected from there then it is commented from external mentor
         * - essay program development
         * - essay mentoring <- related to essay editing hours
        */
        $this->engagement_types = PhaseDetail::whereIn('id', [2, 3, 5, 6, /*11,*/ 13, 14])->get();
        $this->streams = $streamRepository->rnGetAllStreams();

        /* default value */
        $this->grade = '9-12';
    }

    public function resetFields()
    {
        $this->stream_id = null;
        $this->engagement_type_id = null;
        $this->package = [];
        $this->start_date = null;
        $this->end_date = null;
        $this->fee_individual = null;
        $this->agreement = null;
        $this->isEdit = false;
    }

    public function store()
    {
        $this->validate();

        DB::beginTransaction();
        try {
            
            $agreementPath = null;
            if ( $this->agreement ) {
                $fileName = 'Agreement-' . str_replace(' ', '_', $this->user->first_name . '_' . $this->user->last_name . '-' . $this->stream_id . '-' . Carbon::now()->format('Ymdhis') );
                $agreementPath = $fileName.'.'.$this->agreement->getClientOriginalExtension();
                $this->agreement->storeAs('project/crm/user/'.$this->user->id, $agreementPath, 's3');
            }

            foreach ( $this->package as $key => $value)
            {
                UserStream::create([
                    'user_role_id' => $this->user_role_id,
                    'stream_id' => $this->stream_id,
                    'engagement_type_id' => $this->engagement_type_id,
                    'package' => $value,
                    'start_date' => $this->start_date,
                    'end_date' => $this->end_date,
                    'grade' => $this->grade,
                    'fee_individual' => $this->fee_individual,
                    'agreement' => $agreementPath,
                ]);
            }


            $this->resetFields();
            $this->dispatch('agreement-created');
            DB::commit();
            return session()->flash('success', 'Agreement has been created.'); 

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('[STORE USER AGREEMENT] Failed to store ext-mentor\'s agreement : ' . $e->getMessage(). ' on '.$e->getFile() . ' line '. $e->getLine());
            // $this->log_service->createErrorLog(LogModule::STORE_USER_AGREEMENT, $e->getMessage(), $e->getLine(), $e->getFile());
            return session()->flash('error', 'Failed to create agreement.'); 
        }
    }

    public function edit($user_stream_id)
    {
        $user_stream = UserStream::findOrFail($user_stream_id);
        $this->user_stream_id = $user_stream_id;
        $this->stream_id = $user_stream->stream_id;
        $this->engagement_type_id = $user_stream->engagement_type_id;
        $this->package = $user_stream->package;
        $this->start_date = $user_stream->start_date;
        $this->end_date = $user_stream->end_date;
        $this->fee_individual = $user_stream->fee_individual;
        $this->agreement = null; // reset uploaded agreement field
        $this->isEdit = true;
    }

    public function update()
    {
        $this->validate();

        DB::beginTransaction();
        try {
            
            $user_stream = UserStream::findOrFail($this->user_stream_id);
    
            // Handle new file upload
            if ( $this->agreement )
            {
                // Delete old file if it exists
                if ( $user_stream->agreement && Storage::disk('s3')->exists('project/crm/user/'.$this->user->id.'/'.$user_stream->agreement)) {
                    // delete file
                    Storage::disk('s3')->delete('project/crm/user/'.$this->user->id.'/'.$user_stream->agreement);
                }
    
                // Store new file
                $fileName = 'Agreement-' . str_replace(' ', '_', $this->user->first_name . '_' . $this->user->last_name . '-' . $this->stream_id . '-' . Carbon::now()->format('Ymdhis'));
                $agreementPath = $fileName.'.'.$this->agreement->getClientOriginalExtension();
                $this->agreement->storeAs('project/crm/user/'.$this->user->id, $agreementPath, 's3');
            } else {
                $agreementPath = $user_stream->agreement;
            }
    
            $user_stream->stream_id = $this->stream_id;
            $user_stream->engagement_type_id = $this->engagement_type_id;
            $user_stream->package = $this->package;
            $user_stream->start_date = $this->start_date;
            $user_stream->end_date = $this->end_date;
            $user_stream->agreement = $agreementPath;
            $user_stream->fee_individual = $this->fee_individual;
            $user_stream->updated_at = Carbon::now();
            $user_stream->save();
    
            $this->resetFields();
            $this->dispatch('agreement-updated');
            DB::commit();
            return session()->flash('success', 'Agreement has been updated.');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('[UPDATE USER AGREEMENT] Failed to update ext-mentor\'s agreement : ' . $e->getMessage(). ' on '.$e->getFile() . ' line '. $e->getLine());
            // $this->log_service->createErrorLog(LogModule::UPDATE_USER_AGREEMENT, $e->getMessage(), $e->getLine(), $e->getFile());
            return session()->flash('error', 'Failed to update agreement.');
        }
    }

    public function delete($user_stream_id)
    {
        DB::beginTransaction();
        try {
            $user_stream = UserStream::find($user_stream_id);

            if (Storage::disk('s3')->exists('project/crm/user/'.$this->user->id.'/'.$user_stream->agreement)) {
                // delete file
                Storage::disk('s3')->delete('project/crm/user/'.$this->user->id.'/'.$user_stream->agreement);
            }
            // delete record
            $user_stream->delete();

            DB::commit();
            return session()->flash('success', 'Agreement Deleted Successfully');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('[DELETE USER AGREEMENT] Failed to delete ext-mentor\'s agreement : ' . $e->getMessage(). ' on '.$e->getFile() . ' line '. $e->getLine());
            // $this->log_service->createErrorLog(LogModule::DELETE_USER_AGREEMENT, $e->getMessage(), $e->getLine(), $e->getFile());
            return session()->flash('error', 'Failed to delete agreement.');
        }
    }

    public function render()
    {
        return view('livewire.add-fee-and-agreement-external-mentor');
    }
}
