<?php

namespace App\Providers;

use App\Rules\IsoValidator;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Validator::extend('iso_date', IsoValidator::class.'@validateIsoDate');
    }
}
