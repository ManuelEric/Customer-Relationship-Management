<?php

namespace Database\Factories;

use App\Http\Traits\CreateCustomPrimaryKeyTrait;
use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

use function PHPSTORM_META\map;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Event>
 */
class EventFactory extends Factory
{
    use CreateCustomPrimaryKeyTrait;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $last_id = Event::max('event_id');
        $group_id_without_label = $last_id ? $this->remove_primarykey_label($last_id, 5) : '00000';
        $eventId = 'EVT-' . $this->add_digit($group_id_without_label + 1, 4);

        return [
            'event_id' => $eventId,
            'event_title' => fake()->domainName(),
            'event_description' => fake()->text(100),
            'event_location' => fake()->streetName(),
            'event_startdate' => fake()->date(),
            'event_enddate' => fake()->date(),
            'event_target' => rand(1, 1000),
            'event_banner' => NULL,
            'status' => 1,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
