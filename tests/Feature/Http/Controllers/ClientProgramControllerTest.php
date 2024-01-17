<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\ClientEvent;
use App\Models\Corporate;
use App\Models\EdufLead;
use App\Models\Lead;
use App\Models\Program;
use App\Models\User;
use App\Models\UserClient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class ClientProgramControllerTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_stores_client_program_data()
    {
        $student = UserClient::isStudent()->inRandomOrder()->first();
        $program = Program::factory()->create();
        $lead = Lead::inRandomOrder()->first();
        $empl_id = User::isSales()->inRandomOrder()->first();

        # when lead is clientevent
        # then we're going to fetch from the clientevent_id that the user has been joined
        $clientevent_id = $lead->lead_id == 'LS003' ? ClientEvent::where('client_id', $student)->inRandomOrder()->first()->clientevent_id : null;

        # when lead is external edufair
        # then we're going to fetch from tbl_eduf_lead
        $eduf_lead_id = $lead->lead_id == 'LS017' ? EdufLead::inRandomOrder()->first()->id : null;

        # when lead is KOL
        # then we're going to fetch from tbl_lead
        $kol_lead_id = $lead->lead_name == 'KOL' ? Lead::where('main_lead', 'KOL')->inRandomOrder()->first()->lead_id : null;

        # when lead is All-in Partners
        # then we're going to fetch from tbl_corp
        $partner_id = $lead->lead_id == 'LS010' ? Corporate::inRandomOrder()->first()->corp_id : null;


        $response = $this->
            from(route(name: 'student.program.create', parameters: ['student' => $student->id]), )->
            post(route(name: 'student.program.store', parameters: ['student' => $student->id]), [
                'lead_id' => $lead->lead_id,
                'prog_id' => $program->prog_id,
                'clientevent_id' => $clientevent_id,
                'eduf_lead_id' => $eduf_lead_id,
                'kol_lead_id' => $kol_lead_id,
                'partner_id' => $partner_id,
                'first_discuss_date' => Carbon::now(),
                'meeting_notes' => fake()->text(),
                'status' => 0,
                'referral_code' => null,
                'empl_id' => $empl_id
            ]);

        $response->assertStatus(status: 302);
    }
}
