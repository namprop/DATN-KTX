<?php

namespace App\Providers;

use App\Repositories\Contract\ContractRepository;
use App\Repositories\Contract\ContractRepositoryInterface;
use App\Repositories\DepartureRequest\DepartureRequestRepository;
use App\Repositories\DepartureRequest\DepartureRequestRepositoryInterface;
use App\Repositories\Facilities\FacilitiesRepository;
use App\Repositories\Facilities\FacilitiesRepositoryInterface;
use App\Repositories\ParentStudent\ParentStudentRepository;
use App\Repositories\ParentStudent\ParentStudentRepositoryInterface;
use App\Repositories\Payment\PaymentRepository;
use App\Repositories\Payment\PaymentRepositoryInterface;
use App\Repositories\Room\RoomRepository;
use App\Repositories\Room\RoomRepositoryInterface;
use App\Repositories\DormManager\DormManagerRepository;
use App\Repositories\DormManager\DormManagerRepositoryInterface;
use App\Repositories\Student\StudentRepository;
use App\Repositories\Student\StudentRepositoryInterface;
use App\Repositories\User\UserRepository;
use App\Repositories\User\UserRepositoryInterface;
use App\Service\Contract\ContractService;
use App\Service\Contract\ContractServiceInterface;
use App\Service\DepartureRequest\DepartureRequestService;
use App\Service\DepartureRequest\DepartureRequestServiceInterface;
use App\Service\Facilities\FacilitiesService;
use App\Service\Facilities\FacilitiesServiceInterface;
use App\Service\ParentStudent\ParentStudentService;
use App\Service\ParentStudent\ParentStudentServiceInterface;
use App\Service\Payment\PaymentService;
use App\Service\Payment\PaymentServiceInterface;
use App\Service\Room\RoomService;
use App\Service\Room\RoomServiceInterface;
use App\Service\DormManager\DormManagerService;
use App\Service\DormManager\DormManagerServiceInterface;
use App\Service\Student\StudentService;
use App\Service\Student\StudentServiceInterface;
use App\Service\User\UserService;
use App\Service\User\UserServiceInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //

         $this->app->singleton(
            UserRepositoryInterface::class,
            UserRepository::class
        );

        $this->app->singleton(
            UserServiceInterface::class,
            UserService::class
        );

         $this->app->singleton(
            ParentStudentRepositoryInterface::class,
            ParentStudentRepository::class
        );

        $this->app->singleton(
            ParentStudentServiceInterface::class,
            ParentStudentService::class
        );

         $this->app->singleton(
            StudentRepositoryInterface::class,
            StudentRepository::class
        );

        $this->app->singleton(
            StudentServiceInterface::class,
            StudentService::class
        );

         $this->app->singleton(
            DormManagerRepositoryInterface::class,
            DormManagerRepository::class
        );

        $this->app->singleton(
            DormManagerServiceInterface::class,
            DormManagerService::class
        );


         $this->app->singleton(
            RoomRepositoryInterface::class,
            RoomRepository::class
        );

        $this->app->singleton(
            RoomServiceInterface::class,
            RoomService::class
        );

         $this->app->singleton(
            ContractRepositoryInterface::class,
            ContractRepository::class
        );

        $this->app->singleton(
            ContractServiceInterface::class,
            ContractService::class
        );

         $this->app->singleton(
            DepartureRequestRepositoryInterface::class,
            DepartureRequestRepository::class
        );

        $this->app->singleton(
            DepartureRequestServiceInterface::class,
            DepartureRequestService::class
        );

         $this->app->singleton(
            FacilitiesRepositoryInterface::class,
            FacilitiesRepository::class
        );

        $this->app->singleton(
            FacilitiesServiceInterface::class,
            FacilitiesService::class
        );

         $this->app->singleton(
            PaymentRepositoryInterface::class,
            PaymentRepository::class
        );

        $this->app->singleton(
            PaymentServiceInterface::class,
            PaymentService::class
        );

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
