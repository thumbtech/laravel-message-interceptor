<?php

namespace Mozammil\LaravelMessageInterceptor;

use Illuminate\Support\Facades\Event;
use Illuminate\Mail\Events\MessageSending;
use Mozammil\LaravelMessageInterceptor\Listeners\InterceptMessage;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as BaseEventServiceProvider;

class EventServiceProvider extends BaseEventServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        MessageSending::class => [
            InterceptMessage::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
    }
}
