<?php

namespace App\Providers;

use App\Core\Application\Ports\UnitOfWork as UnitOfWorkInterface;
use App\Core\Application\Shared\IdGenerator as IdGeneratorInterface;
use App\Core\Domain\Repositories\TaskRepository;
use App\Core\Domain\Repositories\UserRepository;
use App\Persistence\Eloquent\TaskEloquentModel;
use App\Persistence\Eloquent\UserEloquentModel;
use App\Persistence\Repositories\EloquentTaskRepository;
use App\Persistence\Shared\EloquentUnitOfWork;
use App\Persistence\Shared\UuidGenerator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // ===========================[ ELOQUENT MODELS ] ===========================

        $this->app->singleton(UserEloquentModel::class);
        $this->app->singleton(TaskEloquentModel::class);

        // ==========================[ REPOSITORIES ] ==========================

        // User Repository

        // Token Repository

        // Task Repository
        $this->app->singleton(
            TaskRepository::class,
            function () {
                return new EloquentTaskRepository(new TaskEloquentModel());
            }
        );

        // ==========================[ APPLICATION SERVICES ] ==========================

        // Token Generator Service

        // ==========================[ UNIT OF WORK ] ==========================

        $this->app->singleton(
            UnitOfWorkInterface::class,
            EloquentUnitOfWork::class
        );

        // ==========================[ SHARED SERVICES ] ==========================

        $this->app->singleton(
            IdGeneratorInterface::class,
            UuidGenerator::class
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

