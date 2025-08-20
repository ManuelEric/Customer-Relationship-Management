<?php

use App\Livewire\AddFeeAndAgreementTutor;
use App\Models\pivot\UserRole;
use App\Models\pivot\UserSubject;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

test('user can store tutor agreement', function () {
    $user = User::find('e133ea8c-dc0e-4157-844f-c82fa03b3d1a');
    $user_id = $user->id;
    $user_role_id = UserRole::where('user_id', $user->id)->inRandomOrder()->first()->user_role_id;
    $subject_id = Subject::inRandomOrder()->first()->id;
    $fake_directory = 'project/crm/user/'.$user_id;
    $fake_filename = 'Agreement-'.str_replace(' ', '_', $user->first_name.'_'.$user->last_name).'-'.$subject_id.'-'.now()->format('Ymdhis').'.pdf';
    $fake_curriculum = collect(['IBDP', 'IB MYP', 'Cambridge ALevel', 'Cambridge IGCSE', 'Advanced Placement', 'National'])->shuffle()->first();
    $fake_startdate = Carbon::now()->addDays(1)->format('Y-m-d');
    $fake_enddate = Carbon::now()->addDays(2)->format('Y-m-d');

    Storage::fake($fake_directory);

    $agreement = UploadedFile::fake()->create($fake_filename, 1024, 'application/pdf');

    Livewire::test(AddFeeAndAgreementTutor::class, ['user' => $user])
        ->set('user_role_id', $user_role_id)
        ->set('subject_id', $subject_id)
        ->set('selectedCurriculums', $fake_curriculum)
        ->set('start_date', $fake_startdate)
        ->set('end_date', $fake_enddate)
        ->set('agreement', $agreement)
        ->set('grade', '9-12')
        ->set('fee_individual', 1000000)
        ->set('fee_group', 2000000)
        ->call('store')
        ->assertSessionHas('success', 'Agreement has been created.');

    $user_subject = UserSubject::where('user_role_id', $user_role_id)
        ->where('subject_id', $subject_id)
        ->first();

    expect($user_subject)
        ->toBeInstanceOf(UserSubject::class)
        ->curriculum->toBe($user_subject->curriculum)
        ->start_date->toBe($fake_startdate)
        ->end_date->toBe($fake_enddate)
        ->agreement->toBe($fake_directory.'/'.$fake_filename)
        ->grade->toBe('9-12')
        ->fee_individual->toBe(1000000)
        ->fee_group->toBe(2000000);

    // Assert agreement was stored
    Storage::disk($fake_directory)->assertExists($user_subject->agreement);
});
