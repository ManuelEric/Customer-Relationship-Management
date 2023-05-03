<?php

namespace Tests\Feature\Http\Controllers;

use App\Http\Traits\StandardizePhoneNumberTrait;
use App\Interfaces\SchoolRepositoryInterface;
use App\Models\UserClient;
use App\Repositories\SchoolRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase;
use Illuminate\Support\Str;
use Mockery;
use Mockery\MockInterface;

class ClientStudentControllerTest extends TestCase
{
    use StandardizePhoneNumberTrait;
    use WithoutMiddleware;

    public function construct()
    {
        parent::__construct();
        $this->mock(SchoolRepositoryInterface::class, function (MockInterface $mock) {
            $mock
                ->shouldReceive('createSchoolIfNotExists')
                ->once()
                ->andReturn(true);
        });
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_example()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        // $response->assertViewIs('student.index');
    }

    public function test_store_data_with_new_school()
    {

        $newSchool = [
            'sch_id' => 'add-new',
            'sch_name' => 'Odaiba School',
            'sch_type' => 'International',
            'sch_score' => 10,
        ];

        $newParent = [
            'pr_id' => 'add-new',
        ];

        $mixed_new_data = array_merge($newSchool, $newParent);

        $parentDetails = [
            'first_name' => 'Parent',
            'last_name' => 'Dummy Test Unit',
            'mail' => 'dummy.parent@example.com',
            'phone' => $this->setPhoneNumber('08962361227'),
            'state' => 'DKI Jakarta',
            'city' => 'Jakarta Barat',
            'postal_code' => null,
            'address' => 'Jl address example no 123',
            'lead_id' => 9,
            'eduf_id' => null,
            'event_id' => null,
            'st_levelinterest' => 'Medium',
            'st_note' => null,
        ];

        $studentDetails = $this->formData($mixed_new_data);

        # case 1
        if (!$studentDetails['sch_id'] = $this->createSchoolIfAddNew($studentDetails))
            $this->assertFalse(true, 'Failed to store new school / already exists');

        # case 2
        if (!$studentDetails['pr_id'] = $this->createParentsIfAddNew($parentDetails, $studentDetails))
            $this->assertFalse(true, 'Failed to store new parent / already exists');

        $this->assertTrue(true);

    }

    private function formData($overrides = [])
    {
        $st_levelinterest = ['Low', 'Medium', 'High'];
        shuffle($st_levelinterest);

        return array_merge([
            'first_name' => 'Dummy',
            'last_name' => 'Unit Test',
            'mail' => 'dummy@unittest.com',
            'phone' => $this->setPhoneNumber(Str::random(11)),
            'st_grade' => Str::random(10, 12),
            'st_levelinterest' => array_slice($st_levelinterest, 0, 1),
            'graduation_year' => Str::random(2022, 2023),
            'st_abryear' => Str::random(2022, 2023),
        ], $overrides);
    }

    private function createParentsIfAddNew(array $parentDetails, array $studentDetails)
    {
        $choosen_parent = $studentDetails['pr_id'];
        if (isset($choosen_parent) && $choosen_parent == "add-new") {

            $parent = $this->clientRepository->createClient('Parent', $parentDetails);
            return $parent->id;
        }

        return $choosen_parent;
    }

    private function createSchoolIfAddNew(array $requestData)
    {
        $choosen_school = $requestData['sch_id'];
        if ($choosen_school == "add-new") {

            $schoolDetails = [
                'sch_name' => $requestData['sch_name'],
                'sch_type' => $requestData['sch_type'],
                'sch_score' => $requestData['sch_score'],
            ];

            $schoolCurriculums = [1, 7];

            # create a new school
            $school = app(SchoolRepository::class)->createSchoolIfNotExists($schoolDetails, $schoolCurriculums);
            return $school->sch_id;
        }

        return $choosen_school;
    }

}
