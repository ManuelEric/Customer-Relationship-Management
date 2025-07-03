<?php

namespace App\Livewire;

use App\Enum\LogModule;
use App\Interfaces\SubjectRepositoryInterface;
use App\Models\pivot\UserSubject;
use App\Services\Log\LogService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

class AddFeeAndAgreement extends Component
{
    use WithFileUploads;

    public $user, $role, $tutor_subjects, $user_role_id, $user_subject_id;
    public $isEdit = false;
    public $months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];

    #[Validate('required|exists:tbl_subjects,id')]
    public $subject_id;

    #[Validate('required|min:1|max:12')]
    public $month_start;

    #[Validate('required|min:1|max:12')]
    public $month_end;

    #[Validate('required')]
    public $year;

    #[Validate('required')]
    public $grade;

    #[Validate('required')]
    public $fee_individual;

    #[Validate('required')]
    public $fee_group;

    #[Validate('required')]
    public $head;

    #[Validate('nullable|file|mimes:pdf|max:1024')]
    public $agreement;



    protected SubjectRepositoryInterface $subjectRepository;
    protected $log_service;

    public function mount($user, SubjectRepositoryInterface $subjectRepository, LogService $log_service)
    {
        $this->user = $user;
        $this->user_role_id = $this->user->roles()->where('role_name', 'Tutor')->first()->pivot->id;
        $this->tutor_subjects = $subjectRepository->getAllSubjects();
        $this->year = Carbon::now()->format('Y');
        $this->head = 2;
        $this->grade = "9-12";
        $this->log_service = $log_service;
    }

    public function resetFields()
    {
        $this->subject_id = null;
        $this->month_start = null;
        $this->month_end = null;
        $this->fee_individual = null;
        $this->fee_group = null;
        $this->agreement = null;
        $this->isEdit = false;
    }

    /**
     * 
     * LOG SERVICES NOT WORKING!!
     * PLEASE DO RESEARCH ABOUT HOW TO CALL LOG_SERVICES FROM THIS COMPONENT!!
     */

    public function store()
    {
        $this->validate();

        DB::beginTransaction();
        try {

            $agreementPath = null;
            if ( $this->agreement ) {
                $fileName = 'Agreement-' . str_replace(' ', '_', $this->user->first_name . '_' . $this->user->last_name . '-' . $this->subject_id . '-' . Carbon::now()->format('Ymdhis') . '-' . $this->year);
                $agreementPath = $fileName.'.'.$this->agreement->getClientOriginalExtension();
                $this->agreement->storeAs('project/crm/user/'.$this->user->id, $agreementPath, 'public');
            }
            
    
            UserSubject::create([
                'user_role_id' => $this->user_role_id,
                'subject_id' => $this->subject_id,
                'month_start' => $this->month_start,
                'month_end' => $this->month_end,
                'year' => $this->year,
                'agreement' => $agreementPath,
                'head' => $this->head,
                'additional_fee' => null,
                'grade' => $this->grade,
                'fee_individual' => $this->fee_individual,
                'fee_group' => $this->fee_group,
            ]);
    
            $this->resetFields();
            $this->dispatch('agreement-created');
            DB::commit();
            return session()->flash('success', 'Agreement has been created.');
        } catch (\Exception $e) {
            DB::rollBack();
            // $this->log_service->createErrorLog(LogModule::STORE_USER_AGREEMENT, $e->getMessage(), $e->getLine(), $e->getFile());
            return session()->flash('error', 'Failed to create agreement.');   
        }
    }

    public function edit($user_subject_id)
    {
        $user_subject = UserSubject::findOrFail($user_subject_id);
        $this->user_subject_id = $user_subject->id;
        $this->subject_id = $user_subject->subject_id;
        $this->month_start = $user_subject->month_start;
        $this->month_end = $user_subject->month_end;
        $this->year = $user_subject->year;
        $this->grade = $user_subject->grade ?? '9-12';
        $this->fee_individual = $user_subject->fee_individual;
        $this->fee_group = $user_subject->fee_group;
        $this->head = $user_subject->head;
        $this->agreement = null; // reset uploaded agreement field
        $this->isEdit = true;
    }

    public function update()
    {
        
        $this->validate();

        DB::beginTransaction();
        try {
            
            $user_subject = UserSubject::findOrFail($this->user_subject_id);
    
            // Handle new file upload
            if ( $this->agreement )
            {
                // Delete old file if it exists
                if ( $user_subject->agreement && Storage::disk('public')->exists('project/crm/user/'.$this->user->id.'/'.$user_subject->agreement)) {
                    // delete file
                    Storage::disk('public')->delete('project/crm/user/'.$this->user->id.'/'.$user_subject->agreement);
                }
    
                // Store new file
                $fileName = 'Agreement-' . str_replace(' ', '_', $this->user->first_name . '_' . $this->user->last_name . '-' . $this->subject_id . '-' . Carbon::now()->format('Ymdhis') . '-' . $this->year);
                $agreementPath = $fileName.'.'.$this->agreement->getClientOriginalExtension();
                $this->agreement->storeAs('project/crm/user/'.$this->user->id, $agreementPath, 'public');
            } else {
                $agreementPath = $user_subject->agreement;
            }
    
            $user_subject->subject_id = $this->subject_id;
            $user_subject->month_start = $this->month_start;
            $user_subject->month_end = $this->month_end;
            $user_subject->year = $this->year;
            $user_subject->agreement = $agreementPath;
            $user_subject->head = $this->head;
            $user_subject->additional_fee = null;
            $user_subject->grade = $this->grade;
            $user_subject->fee_individual = $this->fee_individual;
            $user_subject->fee_group = $this->fee_group;
            $user_subject->savedd();
    
            $this->resetFields();
            $this->dispatch('agreement-updated');
            DB::commit();
            return session()->flash('success', 'Agreement has been updated.');
        } catch (\Exception $e) {
            DB::rollBack();
            // $this->log_service->createErrorLog(LogModule::UPDATE_USER_AGREEMENT, $e->getMessage(), $e->getLine(), $e->getFile());
            return session()->flash('error', 'Failed to update agreement.');    
        }

    }

    public function delete($user_subject_id)
    {
        DB::beginTransaction();
        try {
            $user_subject = UserSubject::find($user_subject_id);

            if (Storage::disk('public')->exists('project/crm/user/'.$this->user->id.'/'.$user_subject->agreement)) {
                // delete file
                Storage::disk('public')->delete('project/crm/user/'.$this->user->id.'/'.$user_subject->agreement);
            }
            // delete record
            $user_subject->delete();

            DB::commit();
            return session()->flash('success', 'Agreement Deleted Successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            // $this->log_service->createErrorLog(LogModule::DELETE_USER_AGREEMENT, $e->getMessage(), $e->getLine(), $e->getFile());
            return session()->flash('error', 'Failed to delete agreement.');
        }
    }
        
    public function render()
    {
        switch($this->role)
        {
            case "tutor":
                return view('livewire.add-fee-and-agreement-tutor');

            case "external-mentor":
                // return view('livewire.add-fee-and-agreement-ext-mentor');
        }
        
    }
}
