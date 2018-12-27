<?php

namespace Mozammil\LaravelMessageInterceptor\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Events\MessageSending;
use Mozammil\LaravelMessageInterceptor\Interceptor;
use Mozammil\LaravelMessageInterceptor\Events\MessageIntercepted;

class InterceptMessage
{
    /**
     * Interceptor
     *
     * @var \Mozammil\LaravelMessageInterceptor\Interceptor
     */
    protected $intereptor;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(Interceptor $interceptor)
    {
        $this->interceptor = $interceptor;
    }

    /**
     * Handle the event.
     *
     * @param  \Illuminate\Mail\Events\MessageSending  $event
     *
     * @return void
     */
    public function handle(MessageSending $event)
    {
        $interceptor = $this->interceptor;

        if($interceptor->shouldInterceptMessage()) {
            $event->message = $interceptor->intercept($event->message);
        }
    }
}
