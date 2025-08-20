<?php

namespace App\Livewire;

use App\Models\pivot\EditorAgreement;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class AddFeeAndAgreementEditor extends Component
{
    use WithFileUploads;

    public $user;

    public $user_role_id;

    public $editor_agreements;

    public $editor_agreement_id;

    public $isEdit = false;

    public $categories = [null, 'Essay Editing', 'Essay Mentoring', 'Program Development'];

    public $category = 'Essay Editing';

    /* form request */
    public $start_date;

    public $end_date;

    public $fee_individual;

    public $agreement;

    protected function rules()
    {
        return [
            'category' => 'required|string',
            'start_date' => 'required',
            'end_date' => 'required|gte:start_date',
            'fee_individual' => 'required',
            'agreement' => ['nullable', 'file', 'mimes:pdf', 'max:1024', function ($attribute, $value, $fail) {
                if ($this->isEdit == false && ! $this->agreement) {
                    $fail('The :attribute is required');
                }
            }],
        ];
    }

    public function mount($user)
    {
        $this->category = 'Essay Editing';
        $this->user = $user;
        $this->user_role_id = $this->user->roles()->where('role_name', 'Editor')->first()->pivot->id;
    }

    public function resetFields()
    {
        $this->category = 'Essay Editing';
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
            if ($this->agreement) {
                $fileName = 'Agreement-'.str_replace(' ', '_', $this->user->first_name.'_'.$this->user->last_name.'-'.$this->editor_agreement_id.'-'.Carbon::now()->format('Ymdhis'));
                $agreementPath = $fileName.'.'.$this->agreement->getClientOriginalExtension();
                $this->agreement->storeAs('project/crm/user/'.$this->user->id, $agreementPath, 's3');
            }

            EditorAgreement::create([
                'user_role_id' => $this->user_role_id,
                'category' => $this->category,
                'start_date' => $this->start_date,
                'end_date' => $this->end_date,
                'fee_individual' => $this->fee_individual,
                'agreement' => $agreementPath,
            ]);

            $this->resetFields();
            $this->dispatch('agreement-created');
            DB::commit();

            return session()->flash('success', 'Agreement has been created.');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('[STORE EDITOR AGREEMENT] Failed to store editor\'s agreement : '.$e->getMessage().' on '.$e->getFile().' line '.$e->getLine());

            // $this->log_service->createErrorLog(LogModule::STORE_USER_AGREEMENT, $e->getMessage(), $e->getLine(), $e->getFile());
            return session()->flash('error', 'Failed to create agreement.');
        }
    }

    public function edit($editor_agreement_id)
    {
        $editor_agreement = EditorAgreement::findOrFail($editor_agreement_id);
        $this->editor_agreement_id = $editor_agreement_id;
        $this->category = $editor_agreement->category;
        $this->start_date = $editor_agreement->start_date;
        $this->end_date = $editor_agreement->end_date;
        $this->fee_individual = $editor_agreement->fee_individual;
        $this->agreement = null; // reset uploaded agreement field
        $this->isEdit = true;
    }

    public function update()
    {
        $this->validate();

        DB::beginTransaction();
        try {

            $editor_agreement = EditorAgreement::findOrFail($this->editor_agreement_id);

            // Handle new file upload
            if ($this->agreement) {
                // Delete old file if it exists
                if ($editor_agreement->agreement && Storage::disk('s3')->exists('project/crm/user/'.$this->user->id.'/'.$editor_agreement->agreement)) {
                    // delete file
                    Storage::disk('s3')->delete('project/crm/user/'.$this->user->id.'/'.$editor_agreement->agreement);
                }

                // Store new file
                $fileName = 'Agreement-'.str_replace(' ', '_', $this->user->first_name.'_'.$this->user->last_name.'-'.$this->editor_agreement_id.'-'.Carbon::now()->format('Ymdhis'));
                $agreementPath = $fileName.'.'.$this->agreement->getClientOriginalExtension();
                $this->agreement->storeAs('project/crm/user/'.$this->user->id, $agreementPath, 's3');
            } else {
                $agreementPath = $editor_agreement->agreement;
            }

            $editor_agreement->category = $this->category;
            $editor_agreement->start_date = $this->start_date;
            $editor_agreement->end_date = $this->end_date;
            $editor_agreement->agreement = $agreementPath;
            $editor_agreement->fee_individual = $this->fee_individual;
            $editor_agreement->updated_at = Carbon::now();
            $editor_agreement->save();

            $this->resetFields();
            $this->dispatch('agreement-updated');
            DB::commit();

            return session()->flash('success', 'Agreement has been updated.');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('[UPDATE EDITOR AGREEMENT] Failed to update editor\'s agreement : '.$e->getMessage().' on '.$e->getFile().' line '.$e->getLine());

            // $this->log_service->createErrorLog(LogModule::UPDATE_USER_AGREEMENT, $e->getMessage(), $e->getLine(), $e->getFile());
            return session()->flash('error', 'Failed to update agreement.');
        }
    }

    public function delete($editor_agreement_id)
    {
        DB::beginTransaction();
        try {
            $editor_agreement = EditorAgreement::find($editor_agreement_id);

            if (Storage::disk('s3')->exists('project/crm/user/'.$this->user->id.'/'.$editor_agreement->agreement)) {
                // delete file
                Storage::disk('s3')->delete('project/crm/user/'.$this->user->id.'/'.$editor_agreement->agreement);
            }
            // delete record
            $editor_agreement->delete();

            DB::commit();

            return session()->flash('success', 'Agreement Deleted Successfully');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('[DELETE USER AGREEMENT] Failed to delete editor\'s agreement : '.$e->getMessage().' on '.$e->getFile().' line '.$e->getLine());

            // $this->log_service->createErrorLog(LogModule::DELETE_USER_AGREEMENT, $e->getMessage(), $e->getLine(), $e->getFile());
            return session()->flash('error', 'Failed to delete agreement.');
        }
    }

    public function render()
    {
        return view('livewire.add-fee-and-agreement-editor');
    }
}
