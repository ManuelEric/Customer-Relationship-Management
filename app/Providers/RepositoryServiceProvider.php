<?php

namespace App\Providers;

use App\Interfaces\AssetRepositoryInterface;
use App\Interfaces\CountryRepositoryInterface;
use App\Interfaces\DepartmentRepositoryInterface;
use App\Interfaces\EmployeeRepositoryInterface;
use App\Interfaces\MajorRepositoryInterface;
use App\Interfaces\MentorRepositoryInterface;
use App\Interfaces\RoleRepositoryInterface;
use App\Interfaces\UniversityRepositoryInterface;
use App\Interfaces\UserRepositoryInterface;
use App\Interfaces\VendorRepositoryInterface;
use App\Interfaces\VendorTypeRepositoryInterface;
use App\Interfaces\VolunteerRepositoryInterface;
use App\Repositories\AssetRepository;
use App\Repositories\CountryRepository;
use App\Repositories\DepartmentRepository;
use App\Repositories\EmployeeRepository;
use App\Repositories\MajorRepository;
use App\Repositories\MentorRepository;
use App\Repositories\RoleRepository;
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
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(UniversityRepositoryInterface::class, UniversityRepository::class);
        $this->app->bind(CountryRepositoryInterface::class, CountryRepository::class);
        $this->app->bind(MajorRepositoryInterface::class, MajorRepository::class);
        $this->app->bind(RoleRepositoryInterface::class, RoleRepository::class);
        $this->app->bind(MentorRepositoryInterface::class, MentorRepository::class);

        $this->app->bind(EmployeeRepositoryInterface::class, EmployeeRepository::class);
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
