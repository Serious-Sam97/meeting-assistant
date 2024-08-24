<?php

namespace App\Providers;

use App\Interfaces\CalendarServiceInterface;
use App\Interfaces\PersonServiceInterface;
use App\Mocks\CalendarServiceMock;
use App\Mocks\PersonServiceMock;
use App\Services\CalendarService;
use App\Services\PersonService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $useMocks = config('services.use_mocks');

        $this->app->bind(CalendarServiceInterface::class, function() use ($useMocks) {
            return $useMocks ? new CalendarServiceMock() : new CalendarService();
        });

        $this->app->bind(PersonServiceInterface::class, function() use ($useMocks) {
            return $useMocks ? new PersonServiceMock() : new PersonService();
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
