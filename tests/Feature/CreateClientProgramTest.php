<?php

use App\Models\ClientProgram;
use App\Models\UserClient;

test('user can access client program page', function () {
    $response = $this->get('/');
    $response->assertStatus(200);
});

test('user can create client program', function() {
    $client = UserClient::factory()->create();

    $request = [
        "prog_id" => "SATPREP",
        "lead_id" => "LS014",
        "first_discuss_date" => "2025-07-02",
        "meeting_notes" => "this is dummy",
        "empl_id" => "908abca2-1808-4e0e-89ca-f6251aca4ef6",
        "success_date" => "2025-07-02",
        "test_date" => "2025-07-02",
        "first_class" => "2025-07-02",
        "last_class" => "2025-07-02",
        "diag_score" => "0",
        "test_score" => "0",
        "tutor_1" => "e89ac079-29e6-435e-99e4-f85c5152117b",
        "tutor_2" => null,
        "timesheet_1" => "https://docs.google.com/spreadsheets/d/1zrcDjzguG3acqsKucvXq1CiHzAHxzaXVUAn1_YAOz2M/edit?usp=drivesdk",
        "timesheet_2" => null,
        "prog_running_status" => "0"
    ];

    $this->post(route('student.program.store', $request))
        ->assertRedirect();

    $latestClientProgram = ClientProgram::latest('clientprog_id')->first();
    expect($latestClientProgram)
        ->prog_id->toBe($request['prog_id'])
        ->lead_id->toBe($request['lead_id'])
        ->first_discuss_date->toBe($request['first_discuss_date'])
});
