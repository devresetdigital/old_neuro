<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Http\Resources\Json\Resource;
use TCG\Voyager\Facades\Voyager;
use App\Http\FormFields\PercentageFormField;
use App\Http\FormFields\UploaderFormField;
use App\Http\FormFields\CurrencyFormField;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //Remove data from json response
        Resource::withoutWrapping();
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
        Voyager::addFormField(PercentageFormField::class);
        Voyager::addFormField(UploaderFormField::class);
        Voyager::addFormField(CurrencyFormField::class);
    }
}
