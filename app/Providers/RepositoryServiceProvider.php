<?php

namespace App\Providers;

use App\Interfaces\AcadTutorRepositoryInterface;
use App\Interfaces\AcceptanceRepositoryInterface;
use App\Interfaces\AgendaSpeakerRepositoryInterface;
use App\Interfaces\AlarmRepositoryInterface;
use App\Interfaces\AssetRepositoryInterface;
use App\Interfaces\AssetReturnedRepositoryInterface;
use App\Interfaces\AssetUsedRepositoryInterface;
use App\Interfaces\AxisRepositoryInterface;
use App\Interfaces\ClientEventLogMailRepositoryInterface;
use App\Interfaces\ClientEventRepositoryInterface;
use App\Interfaces\ClientLeadRepositoryInterface;
use App\Interfaces\ClientProgramRepositoryInterface;
use App\Interfaces\ClientRepositoryInterface;
use App\Interfaces\ClientLeadTrackingRepositoryInterface;
use App\Interfaces\ClientProgramLogMailRepositoryInterface;
use App\Interfaces\CorporatePartnerEventRepositoryInterface;
use App\Interfaces\CorporatePicRepositoryInterface;
use App\Interfaces\CorporateRepositoryInterface;
use App\Interfaces\CountryRepositoryInterface;
use App\Interfaces\CurriculumRepositoryInterface;
use App\Interfaces\DepartmentRepositoryInterface;
use App\Interfaces\EditorRepositoryInterface;
use App\Interfaces\EdufLeadRepositoryInterface;
use App\Interfaces\EdufReviewRepositoryInterface;
use App\Interfaces\EmployeeRepositoryInterface;
use App\Interfaces\EventRepositoryInterface;
use App\Interfaces\FollowupRepositoryInterface;
use App\Interfaces\GeneralMailLogRepositoryInterface;
use App\Interfaces\InvoiceAttachmentRepositoryInterface;
use App\Interfaces\InvoiceProgramRepositoryInterface;
use App\Interfaces\InvoiceB2bRepositoryInterface;
use App\Interfaces\InvoiceDetailRepositoryInterface;
use App\Interfaces\InitialProgramRepositoryInterface;
use App\Interfaces\InvoicesRepositoryInterface;
use App\Interfaces\LeadRepositoryInterface;
use App\Interfaces\LeadTargetRepositoryInterface;
use App\Interfaces\MainProgRepositoryInterface;
use App\Interfaces\MajorRepositoryInterface;
use App\Interfaces\MentorRepositoryInterface;
use App\Interfaces\MenuRepositoryInterface;
use App\Interfaces\PartnerRepositoryInterface;
use App\Interfaces\PartnerProgramRepositoryInterface;
use App\Interfaces\PartnerAgreementRepositoryInterface;
use App\Interfaces\PartnerProgramAttachRepositoryInterface;
use App\Interfaces\PartnerProgramCollaboratorsRepositoryInterface;
use App\Interfaces\PositionRepositoryInterface;
use App\Interfaces\ProgramRepositoryInterface;
use App\Interfaces\PurchaseDetailRepositoryInterface;
use App\Interfaces\PurchaseRequestRepositoryInterface;
use App\Interfaces\ReasonRepositoryInterface;
use App\Interfaces\ReceiptAttachmentRepositoryInterface;
use App\Interfaces\ReceiptRepositoryInterface;
use App\Interfaces\ReferralRepositoryInterface;
use App\Interfaces\RefundRepositoryInterface;
use App\Interfaces\RoleRepositoryInterface;
use App\Interfaces\SalesTargetRepositoryInterface;
use App\Interfaces\SchoolCurriculumRepositoryInterface;
use App\Interfaces\SchoolDetailRepositoryInterface;
use App\Interfaces\SchoolEventRepositoryInterface;
use App\Interfaces\SchoolRepositoryInterface;
use App\Interfaces\SchoolProgramRepositoryInterface;
use App\Interfaces\SchoolProgramAttachRepositoryInterface;
use App\Interfaces\SchoolProgramCollaboratorsRepositoryInterface;
use App\Interfaces\SchoolVisitRepositoryInterface;
use App\Interfaces\SeasonalProgramRepositoryInterface;
use App\Interfaces\SubjectRepositoryInterface;
use App\Interfaces\SubProgRepositoryInterface;
use App\Interfaces\TagRepositoryInterface;
use App\Interfaces\TargetTrackingRepositoryInterface;
use App\Interfaces\TargetSignalRepositoryInterface;
use App\Interfaces\UniversityEventRepositoryInterface;
use App\Interfaces\UniversityPicRepositoryInterface;
use App\Interfaces\UniversityRepositoryInterface;
use App\Interfaces\UserRepositoryInterface;
use App\Interfaces\UserTypeRepositoryInterface;
use App\Interfaces\VendorRepositoryInterface;
use App\Interfaces\VendorTypeRepositoryInterface;
use App\Interfaces\VolunteerRepositoryInterface;
use App\Models\ClientLeadTracking;
use App\Repositories\AcadTutorRepository;
use App\Repositories\AcceptanceRepository;
use App\Repositories\AgendaSpeakerRepository;
use App\Repositories\AlarmRepository;
use App\Repositories\AssetRepository;
use App\Repositories\AssetReturnedRepository;
use App\Repositories\AssetUsedRepository;
use App\Repositories\AxisRepository;
use App\Repositories\ClientEventLogMailRepository;
use App\Repositories\ClientEventRepository;
use App\Repositories\ClientLeadRepository;
use App\Repositories\ClientProgramRepository;
use App\Repositories\ClientRepository;
use App\Repositories\ClientLeadTrackingRepository;
use App\Repositories\ClientProgramLogMailRepository;
use App\Repositories\CorporatePartnerEventRepository;
use App\Repositories\CorporatePicRepository;
use App\Repositories\CorporateRepository;
use App\Repositories\CountryRepository;
use App\Repositories\CurriculumRepository;
use App\Repositories\DepartmentRepository;
use App\Repositories\EditorRepository;
use App\Repositories\EdufLeadRepository;
use App\Repositories\EdufReviewRepository;
use App\Repositories\EmployeeRepository;
use App\Repositories\EventRepository;
use App\Repositories\FollowupRepository;
use App\Repositories\GeneralMailLogRepository;
use App\Repositories\InvoiceAttachmentRepository;
use App\Repositories\InvoiceProgramRepository;
use App\Repositories\InvoiceDetailRepository;
use App\Repositories\InvoiceB2bRepository;
use App\Repositories\InitialProgramRepository;
use App\Repositories\InvoicesRepository;
use App\Repositories\LeadRepository;
use App\Repositories\LeadTargetRepository;
use App\Repositories\MainProgRepository;
use App\Repositories\MajorRepository;
use App\Repositories\MentorRepository;
use App\Repositories\MenuRepository;
use App\Repositories\PartnerRepository;
use App\Repositories\PartnerProgramRepository;
use App\Repositories\PartnerAgreementRepository;
use App\Repositories\PartnerProgramAttachRepository;
use App\Repositories\PartnerProgramCollaboratorsRepository;
use App\Repositories\PositionRepository;
use App\Repositories\ProgramRepository;
use App\Repositories\PurchaseDetailRepository;
use App\Repositories\PurchaseRequestRepository;
use App\Repositories\ReasonRepository;
use App\Repositories\ReceiptAttachmentRepository;
use App\Repositories\ReceiptRepository;
use App\Repositories\ReferralRepository;
use App\Repositories\RefundRepository;
use App\Repositories\RoleRepository;
use App\Repositories\SalesTargetRepository;
use App\Repositories\SchoolCurriculumRepository;
use App\Repositories\SchoolDetailRepository;
use App\Repositories\SchoolEventRepository;
use App\Repositories\SchoolRepository;
use App\Repositories\SchoolProgramRepository;
use App\Repositories\SchoolProgramAttachRepository;
use App\Repositories\SchoolProgramCollaboratorsRepository;
use App\Repositories\SchoolVisitRepository;
use App\Repositories\SeasonalProgramRepository;
use App\Repositories\SubjectRepository;
use App\Repositories\SubProgRepository;
use App\Repositories\TagRepository;
use App\Repositories\TargetTrackingRepository;
use App\Repositories\TargetSignalRepository;
use App\Repositories\UniversityEventRepository;
use App\Repositories\UniversityPicRepository;
use App\Repositories\UniversityRepository;
use App\Repositories\UserRepository;
use App\Repositories\UserTypeRepository;
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
        $this->app->bind(CorporatePicRepositoryInterface::class, CorporatePicRepository::class);
        $this->app->bind(DepartmentRepositoryInterface::class, DepartmentRepository::class);
        $this->app->bind(PartnerRepositoryInterface::class, PartnerRepository::class);
        $this->app->bind(UniversityPicRepositoryInterface::class, UniversityPicRepository::class);
        $this->app->bind(EventRepositoryInterface::class, EventRepository::class);
        $this->app->bind(UniversityEventRepositoryInterface::class, UniversityEventRepository::class);
        $this->app->bind(SchoolEventRepositoryInterface::class, SchoolEventRepository::class);
        $this->app->bind(CorporatePartnerEventRepositoryInterface::class, CorporatePartnerEventRepository::class);
        $this->app->bind(SchoolProgramRepositoryInterface::class, SchoolProgramRepository::class);
        $this->app->bind(SchoolProgramAttachRepositoryInterface::class, SchoolProgramAttachRepository::class);
        $this->app->bind(AgendaSpeakerRepositoryInterface::class, AgendaSpeakerRepository::class);
        $this->app->bind(ReferralRepositoryInterface::class, ReferralRepository::class);
        $this->app->bind(ReasonRepositoryInterface::class, ReasonRepository::class);
        $this->app->bind(TagRepositoryInterface::class, TagRepository::class);
        $this->app->bind(SchoolCurriculumRepositoryInterface::class, SchoolCurriculumRepository::class);
        $this->app->bind(PartnerProgramRepositoryInterface::class, PartnerProgramRepository::class);
        $this->app->bind(PartnerProgramAttachRepositoryInterface::class, PartnerProgramAttachRepository::class);
        $this->app->bind(PartnerAgreementRepositoryInterface::class, PartnerAgreementRepository::class);
        $this->app->bind(SalesTargetRepositoryInterface::class, SalesTargetRepository::class);
        $this->app->bind(ClientProgramRepositoryInterface::class, ClientProgramRepository::class);
        $this->app->bind(FollowupRepositoryInterface::class, FollowupRepository::class);
        $this->app->bind(ClientEventRepositoryInterface::class, ClientEventRepository::class);
        $this->app->bind(InvoiceProgramRepositoryInterface::class, InvoiceProgramRepository::class);
        $this->app->bind(InvoiceB2bRepositoryInterface::class, InvoiceB2bRepository::class);
        $this->app->bind(InvoiceDetailRepositoryInterface::class, InvoiceDetailRepository::class);
        $this->app->bind(ReceiptRepositoryInterface::class, ReceiptRepository::class);
        $this->app->bind(RefundRepositoryInterface::class, RefundRepository::class);
        $this->app->bind(UserTypeRepositoryInterface::class, UserTypeRepository::class);
        $this->app->bind(SchoolVisitRepositoryInterface::class, SchoolVisitRepository::class);
        $this->app->bind(InvoiceAttachmentRepositoryInterface::class, InvoiceAttachmentRepository::class);
        $this->app->bind(ReceiptAttachmentRepositoryInterface::class, ReceiptAttachmentRepository::class);
        $this->app->bind(AxisRepositoryInterface::class, AxisRepository::class);
        $this->app->bind(PartnerProgramCollaboratorsRepositoryInterface::class, PartnerProgramCollaboratorsRepository::class);
        $this->app->bind(SchoolProgramCollaboratorsRepositoryInterface::class, SchoolProgramCollaboratorsRepository::class);
        $this->app->bind(AcadTutorRepositoryInterface::class, AcadTutorRepository::class);
        $this->app->bind(LeadTargetRepositoryInterface::class, LeadTargetRepository::class);
        $this->app->bind(SeasonalProgramRepositoryInterface::class, SeasonalProgramRepository::class);
        $this->app->bind(ClientLeadRepositoryInterface::class, ClientLeadRepository::class);
        $this->app->bind(ClientEventLogMailRepositoryInterface::class, ClientEventLogMailRepository::class);
        $this->app->bind(ClientProgramLogMailRepositoryInterface::class, ClientProgramLogMailRepository::class);
        $this->app->bind(GeneralMailLogRepositoryInterface::class, GeneralMailLogRepository::class);
        $this->app->bind(AcceptanceRepositoryInterface::class, AcceptanceRepository::class);
        $this->app->bind(InvoicesRepositoryInterface::class, InvoicesRepository::class);

        $this->app->bind(EmployeeRepositoryInterface::class, EmployeeRepository::class);
        $this->app->bind(PositionRepositoryInterface::class, PositionRepository::class);
        $this->app->bind(MenuRepositoryInterface::class, MenuRepository::class);
        $this->app->bind(InitialProgramRepositoryInterface::class, InitialProgramRepository::class);
        $this->app->bind(ClientLeadTrackingRepositoryInterface::class, ClientLeadTrackingRepository::class);
        $this->app->bind(TargetTrackingRepositoryInterface::class, TargetTrackingRepository::class);
        $this->app->bind(TargetSignalRepositoryInterface::class, TargetSignalRepository::class);
        $this->app->bind(AlarmRepositoryInterface::class, AlarmRepository::class);
        $this->app->bind(SubjectRepositoryInterface::class, SubjectRepository::class);
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
