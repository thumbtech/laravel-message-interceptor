<?php

namespace Mozammil\LaravelMessageInterceptor\Events;

use Swift_Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class MessageIntercepted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The underlying Swift Message
     *
     * @var Swift_Message
     */
    public $message;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Swift_Message $message)
    {
        $this->message = $message;
    }
}
