<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Event;
use App\Models\Lead;
use App\Models\School;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ClientEventControllerTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_stores_data()
    {
        $event = Event::factory()->create();
        $school = School::inRandomOrder()->first();
        $lead = Lead::inRandomOrder()->first();

        $destination_country = array();
        for ($i = 0; $i < rand(1, Tag::count()); $i++) {

            $tag = Tag::inRandomOrder()->first()->id;

            if (!in_array($tag, $destination_country))
                $destination_country[] = $tag;

        }

        $response = $this->
            from(route(name: 'form.event.create'))->
            post(route(name: 'form.event.store'), [
                'event_name' => urlencode($event->event_title),
                'attend_status' => "attend",
                'attend' => rand(1,3),
                'notes' => array(NULL, 'VIP')[rand(0,1)],
                'status' => 'ots',
                'role' => array('parent', 'student')[rand(0,1)],
                'fullname' => [
                    fake()->name(),
                    fake()->name()
                ],
                'email' => [
                    fake()->unique()->email(),
                    fake()->unique()->email()
                ],
                'fullnumber' => [
                    fake()->unique()->phoneNumber(),
                    fake()->unique()->phoneNumber()
                ],
                'school' => $school->sch_id,
                'leadsource' => $lead->lead_id,
                'graduation_year' => rand(2023, 2025),
                'destination_country' => $destination_country
            ]);

        $response->assertStatus(status: 302);
        $response->assertRedirect(route(name: 'form.event.registration.success'));
    }
}
