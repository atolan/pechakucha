<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Http\Validators\ExtensionValidator;

class ValidatorServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app['validator']->resolver(function($translator, $data, $rules, $messages) {
            return new ExtensionValidator($translator, $data, $rules, $messages);
        });
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
