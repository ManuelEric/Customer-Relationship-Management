<?php

namespace App\Livewire;

use App\Enum\LogModule;
use App\Interfaces\CurriculumRepositoryInterface;
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

class AddFeeAndAgreementTutor extends Component
{
    use WithFileUploads;

    public $user, $role, $tutor_subjects, $user_role_id, $user_subject_id;
    public $curriculums = ['IBDP', 'IB MYP', 'Cambridge ALevel', 'Cambridge IGCSE', 'Advanced Placement', 'National'];
    public $isEdit = false;
    public $subject_id = []; // will hold multiple values
    public $selectedCurriculums = []; // will hold multiple values
    public $start_date;
    public $end_date;
    public $grade;
    public $fee_individual;
    public $fee_group;
    public $head;
    public $agreement;



    protected SubjectRepositoryInterface $subjectRepository;
    protected $log_service;

    protected function rules()
    {
        return [
            'subject_id.*' => 'required|exists:tbl_subjects,id',
            'selectedCurriculums.*' => 'required',
            'start_date' => 'required',
            'end_date' => 'required|gte:start_date',
            'grade' => 'required',
            'fee_individual' => 'required',
            'fee_group' => 'nullable',
            'head' => 'nullable',
            'agreement' => ['nullable', 'file', 'mimes:pdf', 'max:1024', function ($attribute, $value, $fail) {
                if ( $this->isEdit == false && !$this->agreement )
                    $fail('The :attribute is required');
            }],
        ];
    }

    public function mount(
        $user, 
        SubjectRepositoryInterface $subjectRepository, 
        LogService $log_service)
    {
        $this->user = $user;
        $this->user_role_id = $this->user->roles()->where('role_name', 'Tutor')->first()->pivot->id;
        $this->tutor_subjects = $subjectRepository->getAllSubjects();
        
        /* default value */
        $this->grade = "9-12";
        $this->log_service = $log_service;
    }

    public function resetFields()
    {
        $this->subject_id = [];
        $this->selectedCurriculums = [];
        $this->start_date = null;
        $this->end_date = null;
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
                $fileName = 'Agreement-' . str_replace(' ', '_', $this->user->first_name . '_' . $this->user->last_name . '-' . Carbon::now()->format('Ymdhis'));
                $agreementPath = $fileName.'.'.$this->agreement->getClientOriginalExtension();
                $this->agreement->storeAs('project/crm/user/'.$this->user->id, $agreementPath, 's3');
            }
            
            foreach ($this->selectedCurriculums as $key => $curriculum) 
            {
                foreach ($this->subject_id as $index => $value)
                {
                    UserSubject::create([
                        'user_role_id' => $this->user_role_id,
                        'subject_id' => $value,
                        'curriculum' => $curriculum,
                        'start_date' => $this->start_date,
                        'end_date' => $this->end_date,
                        'agreement' => $agreementPath,
                        'head' => $this->head,
                        'additional_fee' => null,
                        'grade' => $this->grade,
                        'fee_individual' => $this->fee_individual,
                        'fee_group' => $this->fee_group,
                    ]);
                }
            }
    
    
            $this->resetFields();
            $this->dispatch('agreement-created');
            DB::commit();
            return session()->flash('success', 'Agreement has been created.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('[STORE USER AGREEMENT] Failed to store tutor\'s agreement : ' . $e->getMessage(). ' on '.$e->getFile() . ' line '. $e->getLine());// $this->log_service->createErrorLog(LogModule::STORE_USER_AGREEMENT, $e->getMessage(), $e->getLine(), $e->getFile());
            return session()->flash('error', 'Failed to create agreement.');   
        }
    }

    public function edit($user_subject_id)
    {
        $user_subject = UserSubject::findOrFail($user_subject_id);
        $this->user_subject_id = $user_subject->id;
        $this->subject_id = $user_subject->subject_id;
        $this->selectedCurriculums = $user_subject->curriculum;
        $this->start_date = $user_subject->start_date;
        $this->end_date = $user_subject->end_date;
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
                if ( $user_subject->agreement && Storage::disk('s3')->exists('project/crm/user/'.$this->user->id.'/'.$user_subject->agreement)) {
                    // delete file
                    Storage::disk('s3')->delete('project/crm/user/'.$this->user->id.'/'.$user_subject->agreement);
                }
    
                // Store new file
                $fileName = 'Agreement-' . str_replace(' ', '_', $this->user->first_name . '_' . $this->user->last_name . '-' . $this->subject_id . '-' . Carbon::now()->format('Ymdhis') );
                $agreementPath = $fileName.'.'.$this->agreement->getClientOriginalExtension();
                $this->agreement->storeAs('project/crm/user/'.$this->user->id, $agreementPath, 's3');
            } else {
                $agreementPath = $user_subject->agreement;
            }
    
            $user_subject->subject_id = $this->subject_id;
            $user_subject->curriculum = $this->selectedCurriculums;
            $user_subject->start_date = $this->start_date;
            $user_subject->end_date = $this->end_date;
            $user_subject->agreement = $agreementPath;
            $user_subject->head = $this->head;
            $user_subject->additional_fee = null;
            $user_subject->grade = $this->grade;
            $user_subject->fee_individual = $this->fee_individual;
            $user_subject->fee_group = $this->fee_group;
            $user_subject->updated_at = Carbon::now();
            $user_subject->save();
    
            $this->resetFields();
            $this->dispatch('agreement-updated');
            DB::commit();
            return session()->flash('success', 'Agreement has been updated.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('[UPDATE USER AGREEMENT] Failed to update tutor\'s agreement : ' . $e->getMessage(). ' on '.$e->getFile() . ' line '. $e->getLine());
            // $this->log_service->createErrorLog(LogModule::UPDATE_USER_AGREEMENT, $e->getMessage(), $e->getLine(), $e->getFile());
            return session()->flash('error', 'Failed to update agreement.');    
        }

    }

    public function delete($user_subject_id)
    {
        DB::beginTransaction();
        try {
            $user_subject = UserSubject::find($user_subject_id);

            if (Storage::disk('s3')->exists('project/crm/user/'.$this->user->id.'/'.$user_subject->agreement)) {
                // delete file
                Storage::disk('s3')->delete('project/crm/user/'.$this->user->id.'/'.$user_subject->agreement);
            }
            // delete record
            $user_subject->delete();

            DB::commit();
            return session()->flash('success', 'Agreement Deleted Successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('[DELETE USER AGREEMENT] Failed to delete tutor\'s agreement : ' . $e->getMessage(). ' on '.$e->getFile() . ' line '. $e->getLine());
            // $this->log_service->createErrorLog(LogModule::DELETE_USER_AGREEMENT, $e->getMessage(), $e->getLine(), $e->getFile());
            return session()->flash('error', 'Failed to delete agreement.');
        }
    }
        
    public function render()
    {
        return view('livewire.add-fee-and-agreement-tutor');
    }
}
