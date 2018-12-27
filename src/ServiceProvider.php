<?php

namespace Mozammil\LaravelMessageInterceptor;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/message-interceptor.php' => config_path('message-interceptor.php'),
        ]);
    }

    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/message-interceptor.php',
            'message-interceptor'
        );

        $this->app->register(EventServiceProvider::class);
    }
}