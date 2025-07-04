<?php

namespace App\Livewire;

use App\Interfaces\StreamRepositoryInterface;
use App\Models\pivot\UserStream;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class AddFeeAndAgreementExternalMentor extends Component
{
    use WithFileUploads;
    
    public $user, $user_role_id, $streams, $user_stream_id;
    public $isEdit = false;
    public $months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];

    /* engagement type */
    public $packages = ['Professional Sharing 1on1', 'Professional Sharing 2-10 mentees', 'Professional Sharing >10 mentees', 'Competition Mentorship', 'Subject-Specific Project Mentorship', 'Essay Mentoring', 'Essay Program Development'];

    /* form request */
    public $stream_id, $package, $month_start, $month_end, $year, $grade, $fee_individual, $agreement;

    protected function rules()
    {
        return [
            'stream_id' => 'required|exists:streams,id',
            'package' => 'required|string', // refer to packages that manually set
            'month_start' => 'required|min:1|max:12',
            'month_end' => 'required|min:1|max:12|gte:month_start',
            'year' => 'required',
            'grade' => 'required',
            'fee_individual' => 'required',
            'agreement' => ['sometimes', 'file', 'mimes:pdf', 'max:1024', function ($attribute, $value, $fail) {
                if ( $this->isEdit == false && !$this->agreement )
                    $fail('The :attribute is required');
            }],
        ];
    }

    protected function messages()
    {
        return [
            'month_end.gte' => 'The :attribute must be greater than or equal to ' . $this->months[$this->month_start-1]
        ];
    }

    public function mount($user, StreamRepositoryInterface $streamRepository)
    {
        $this->user = $user;
        $this->user_role_id = $this->user->roles()->where('role_name', 'External Mentor')->first()->pivot->id;
        $this->streams = $streamRepository->rnGetAllStreams();

        /* default value */
        $this->grade = '9-12';
        $this->year = Carbon::now()->format('Y');
    }

    public function resetFields()
    {
        $this->stream_id = null;
        $this->package = null;
        $this->month_start = null;
        $this->month_end = null;
        $this->fee_individual = null;
        $this->agreement = null;
        $this->year = Carbon::now()->format('Y');
        $this->isEdit = false;
    }

    public function store()
    {
        $this->validate();

        DB::beginTransaction();
        try {
            
            $agreementPath = null;
            if ( $this->agreement ) {
                $fileName = 'Agreement-' . str_replace(' ', '_', $this->user->first_name . '_' . $this->user->last_name . '-' . $this->stream_id . '-' . Carbon::now()->format('Ymdhis') . '-' . $this->year);
                $agreementPath = $fileName.'.'.$this->agreement->getClientOriginalExtension();
                $this->agreement->storeAs('project/crm/user/'.$this->user->id, $agreementPath, 's3');
            }

            UserStream::create([
                'user_role_id' => $this->user_role_id,
                'stream_id' => $this->stream_id,
                'package' => $this->package,
                'month_start' => $this->month_start,
                'month_end' => $this->month_end,
                'year' => $this->year,
                'grade' => $this->grade,
                'fee_individual' => $this->fee_individual,
                'agreement' => $agreementPath,
            ]);

            $this->resetFields();
            $this->dispatch('agreement-created');
            DB::commit();
            return session()->flash('success', 'Agreement has been created.'); 

        } catch (Exception $e) {
            DB::rollBack();
            // $this->log_service->createErrorLog(LogModule::STORE_USER_AGREEMENT, $e->getMessage(), $e->getLine(), $e->getFile());
            return session()->flash('error', 'Failed to create agreement.'); 
        }
    }

    public function edit($user_stream_id)
    {
        $user_stream = UserStream::findOrFail($user_stream_id);
        $this->user_stream_id = $user_stream_id;
        $this->stream_id = $user_stream->stream_id;
        $this->package = $user_stream->package;
        $this->month_start = $user_stream->month_start;
        $this->month_end = $user_stream->month_end;
        $this->year = $user_stream->year;
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
                    Storage::disk('public')->delete('project/crm/user/'.$this->user->id.'/'.$user_stream->agreement);
                }
    
                // Store new file
                $fileName = 'Agreement-' . str_replace(' ', '_', $this->user->first_name . '_' . $this->user->last_name . '-' . $this->subject_id . '-' . Carbon::now()->format('Ymdhis') . '-' . $this->year);
                $agreementPath = $fileName.'.'.$this->agreement->getClientOriginalExtension();
                $this->agreement->storeAs('project/crm/user/'.$this->user->id, $agreementPath, 'public');
            } else {
                $agreementPath = $user_stream->agreement;
            }
    
            $user_stream->stream_id = $this->stream_id;
            $user_stream->package = $this->package;
            $user_stream->month_start = $this->month_start;
            $user_stream->month_end = $this->month_end;
            $user_stream->year = $this->year;
            $user_stream->agreement = $agreementPath;
            $user_stream->fee_individual = $this->fee_individual;
            $user_stream->save();
    
            $this->resetFields();
            $this->dispatch('agreement-updated');
            DB::commit();
            return session()->flash('success', 'Agreement has been updated.');
        } catch (Exception $e) {

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
            // $this->log_service->createErrorLog(LogModule::DELETE_USER_AGREEMENT, $e->getMessage(), $e->getLine(), $e->getFile());
            return session()->flash('error', 'Failed to delete agreement.');
        }
    }

    public function render()
    {
        return view('livewire.add-fee-and-agreement-external-mentor');
    }
}
