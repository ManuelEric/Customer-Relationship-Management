<?php

namespace App\Providers;

use App\Interfaces\AssetRepositoryInterface;
use App\Interfaces\AssetReturnedRepositoryInterface;
use App\Interfaces\AssetUsedRepositoryInterface;
use App\Interfaces\ClientRepositoryInterface;
use App\Interfaces\CorporateRepositoryInterface;
use App\Interfaces\CountryRepositoryInterface;
use App\Interfaces\CurriculumRepositoryInterface;
use App\Interfaces\DepartmentRepositoryInterface;
use App\Interfaces\EditorRepositoryInterface;
use App\Interfaces\EdufLeadRepositoryInterface;
use App\Interfaces\EdufReviewRepositoryInterface;
use App\Interfaces\EmployeeRepositoryInterface;
use App\Interfaces\LeadRepositoryInterface;
use App\Interfaces\MainProgRepositoryInterface;
use App\Interfaces\MajorRepositoryInterface;
use App\Interfaces\MentorRepositoryInterface;
use App\Interfaces\PositionRepositoryInterface;
use App\Interfaces\ProgramRepositoryInterface;
use App\Interfaces\PurchaseDetailRepositoryInterface;
use App\Interfaces\PurchaseRequestRepositoryInterface;
use App\Interfaces\RoleRepositoryInterface;
use App\Interfaces\SchoolDetailRepositoryInterface;
use App\Interfaces\SchoolRepositoryInterface;
use App\Interfaces\SubProgRepositoryInterface;
use App\Interfaces\UniversityRepositoryInterface;
use App\Interfaces\UserRepositoryInterface;
use App\Interfaces\VendorRepositoryInterface;
use App\Interfaces\VendorTypeRepositoryInterface;
use App\Interfaces\VolunteerRepositoryInterface;
use App\Repositories\AssetRepository;
use App\Repositories\AssetReturnedRepository;
use App\Repositories\AssetUsedRepository;
use App\Repositories\ClientRepository;
use App\Repositories\CorporateRepository;
use App\Repositories\CountryRepository;
use App\Repositories\CurriculumRepository;
use App\Repositories\DepartmentRepository;
use App\Repositories\EditorRepository;
use App\Repositories\EdufLeadRepository;
use App\Repositories\EdufReviewRepository;
use App\Repositories\EmployeeRepository;
use App\Repositories\LeadRepository;
use App\Repositories\MainProgRepository;
use App\Repositories\MajorRepository;
use App\Repositories\MentorRepository;
use App\Repositories\PositionRepository;
use App\Repositories\ProgramRepository;
use App\Repositories\PurchaseDetailRepository;
use App\Repositories\PurchaseRequestRepository;
use App\Repositories\RoleRepository;
use App\Repositories\SchoolDetailRepository;
use App\Repositories\SchoolRepository;
use App\Repositories\SubProgRepository;
use App\Repositories\UniversityRepository;
use App\Repositories\UserRepository;
use App\Repositories\VendorRepository;
use App\Repositories\VendorTypeRepository;
use App\Repositories\VolunteerRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(VendorRepositoryInterface::class, VendorRepository::class);
        $this->app->bind(VendorTypeRepositoryInterface::class, VendorTypeRepository::class);
        $this->app->bind(VolunteerRepositoryInterface::class, VolunteerRepository::class);
        $this->app->bind(AssetRepositoryInterface::class, AssetRepository::class);   
        $this->app->bind(AssetUsedRepositoryInterface::class, AssetUsedRepository::class);
        $this->app->bind(AssetReturnedRepositoryInterface::class, AssetReturnedRepository::class);
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(UniversityRepositoryInterface::class, UniversityRepository::class);
        $this->app->bind(CountryRepositoryInterface::class, CountryRepository::class);
        $this->app->bind(MajorRepositoryInterface::class, MajorRepository::class);
        $this->app->bind(RoleRepositoryInterface::class, RoleRepository::class);
        $this->app->bind(LeadRepositoryInterface::class, LeadRepository::class);
        $this->app->bind(MentorRepositoryInterface::class, MentorRepository::class);
        $this->app->bind(EditorRepositoryInterface::class, EditorRepository::class);
        $this->app->bind(SchoolRepositoryInterface::class, SchoolRepository::class);
        $this->app->bind(CurriculumRepositoryInterface::class, CurriculumRepository::class);
        $this->app->bind(SchoolDetailRepositoryInterface::class, SchoolDetailRepository::class);
        $this->app->bind(ProgramRepositoryInterface::class, ProgramRepository::class);
        $this->app->bind(MainProgRepositoryInterface::class, MainProgRepository::class);
        $this->app->bind(SubProgRepositoryInterface::class, SubProgRepository::class);
        $this->app->bind(ClientRepositoryInterface::class, ClientRepository::class);
        $this->app->bind(CorporateRepositoryInterface::class, CorporateRepository::class);
        $this->app->bind(EdufLeadRepositoryInterface::class, EdufLeadRepository::class);
        $this->app->bind(EdufReviewRepositoryInterface::class, EdufReviewRepository::class);
        $this->app->bind(PurchaseRequestRepositoryInterface::class, PurchaseRequestRepository::class);
        $this->app->bind(PurchaseDetailRepositoryInterface::class, PurchaseDetailRepository::class);

        $this->app->bind(EmployeeRepositoryInterface::class, EmployeeRepository::class);
        $this->app->bind(PositionRepositoryInterface::class, PositionRepository::class);
        $this->app->bind(DepartmentRepositoryInterface::class, DepartmentRepository::class);

    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
