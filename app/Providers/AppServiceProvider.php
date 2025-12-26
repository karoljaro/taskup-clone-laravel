<?php

namespace App\Providers;

use App\Core\Domain\Repositories\TaskRepository;
use App\Persistence\Eloquent\TaskEloquentModel;
use App\Persistence\Repositories\EloquentTaskRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Bind TaskRepository interface to Eloquent implementation
        $this->app->singleton(TaskRepository::class, function () {
            return new EloquentTaskRepository(new TaskEloquentModel());
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}

