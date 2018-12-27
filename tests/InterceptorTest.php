<?php

namespace Mozammil\LaravelMessageInterceptor\Test;

use Mockery;
use Swift_Message;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Config;
use Illuminate\Mail\Events\MessageSending;
use Mozammil\LaravelMessageInterceptor\Interceptor;
use Mozammil\LaravelMessageInterceptor\Events\MessageIntercepted;

class InterceptorTest extends TestCase
{
    public function test_it_can_determine_if_it_should_intercept_the_message()
    {
        $interceptor = new Interceptor();

        Config::set('message-interceptor.enabled', true);

        $this->assertTrue($interceptor->shouldInterceptMessage());

        Config::set('message-interceptor.enabled', false);

        $this->assertFalse($interceptor->shouldInterceptMessage());
    }

    public function test_it_can_determine_if_recipient_is_whitelisted()
    {
        $interceptor = new Interceptor();

        Config::set('message-interceptor.whitelist.emails', ['hello@moz.im']);

        $this->assertTrue($interceptor->isRecipientWhitelisted('hello@moz.im'));
        $this->assertFalse($interceptor->isRecipientWhitelisted('mozammil@outlook.com'));

        Config::set('message-interceptor.whitelist.emails', []);
        Config::set('message-interceptor.whitelist.domains', ['moz.im']);

        $this->assertTrue($interceptor->isRecipientWhitelisted('hello@moz.im'));
        $this->assertFalse($interceptor->isRecipientWhitelisted('mozammil@outlook.com'));
    }

    public function test_it_can_determine_the_recipients_of_a_message()
    {
        $interceptor = new Interceptor();
        $message = Mockery::mock(Swift_Message::class);

        Config::set('message-interceptor.to.address', 'me@mozammil.com');
        Config::set('message-interceptor.to.name', 'Mozammil');

        $message->allows()->getTo()->andReturns([
            'hello@moz.im' => null,
            'm@moz.im' => null
        ]);

        $this->assertEquals(
            ['me@mozammil.com' => 'Mozammil'],
            $interceptor->getFilteredRecipients($message)
        );
    }

    public function test_it_can_determine_the_recipients_of_a_message_with_whitelisted_emails()
    {
        $interceptor = new Interceptor();
        $message = Mockery::mock(Swift_Message::class);

        Config::set('message-interceptor.to.address', 'me@mozammil.com');
        Config::set('message-interceptor.to.name', 'Mozammil');

        Config::set('message-interceptor.whitelist.emails', ['hello@moz.im', 'mozammil@outlook.com']);

        $message->allows()->getTo()->andReturns([
            'hello@moz.im' => null,
            'm@moz.im' => null,
            'mozammil@outlook.com' => null
        ]);

        $this->assertEquals(
            [
                'me@mozammil.com' => 'Mozammil',
                'hello@moz.im' => null,
                'mozammil@outlook.com' => null,
            ],
            $interceptor->getFilteredRecipients($message)
        );
    }

    public function test_it_can_determine_the_recipients_of_a_message_with_whitelisted_domains()
    {
        $interceptor = new Interceptor();
        $message = Mockery::mock(Swift_Message::class);

        Config::set('message-interceptor.to.address', 'me@mozammil.com');
        Config::set('message-interceptor.to.name', 'Mozammil');

        Config::set('message-interceptor.whitelist.domains', ['moz.im']);

        $message->allows()->getTo()->andReturns([
            'hello@moz.im' => null,
            'm@moz.im' => null,
            'mozammil@outlook.com' => null
        ]);

        $this->assertEquals(
            [
                'me@mozammil.com' => 'Mozammil',
                'hello@moz.im' => null,
                'm@moz.im' => null
            ],
            $interceptor->getFilteredRecipients($message)
        );
    }

    public function test_it_can_get_cc_recipients_correctly_while_cc_is_preserved()
    {
        $interceptor = new Interceptor();

        $message = Mockery::mock(Swift_Message::class);

        Config::set('message-interceptor.to.address', 'me@mozammil.com');
        Config::set('message-interceptor.to.name', 'Mozammil');
        Config::set('message-interceptor.preserveCc', true);

        Config::set('message-interceptor.cc', [
            'mozammil.kho@gmail.com'
        ]);

        $message->allows()->getCc()->andReturns([
            'hello@moz.im' => null,
            'm@moz.im' => null,
            'mozammil@outlook.com' => null
        ]);

        $this->assertEquals(
            [
                'hello@moz.im' => null,
                'm@moz.im' => null,
                'mozammil@outlook.com' => null,
                'mozammil.kho@gmail.com' => null
            ],
            $interceptor->getFilteredCcRecipients($message)
        );
    }

    public function test_it_can_get_cc_recipients_correctly_while_cc_is_not_preserved()
    {
        $interceptor = new Interceptor();

        $message = Mockery::mock(Swift_Message::class);

        Config::set('message-interceptor.to.address', 'me@mozammil.com');
        Config::set('message-interceptor.to.name', 'Mozammil');
        Config::set('message-interceptor.preserveCc', false);

        Config::set('message-interceptor.cc', [
            'mozammil.kho@gmail.com'
        ]);

        $message->allows()->getCc()->andReturns([
            'hello@moz.im' => null,
            'm@moz.im' => null,
            'mozammil@outlook.com' => null
        ]);

        $this->assertEquals(
            ['mozammil.kho@gmail.com' => null],
            $interceptor->getFilteredCcRecipients($message)
        );
    }

    public function test_it_can_get_bcc_recipients_correctly_while_bcc_is_preserved()
    {
        $interceptor = new Interceptor();

        $message = Mockery::mock(Swift_Message::class);

        Config::set('message-interceptor.to.address', 'me@mozammil.com');
        Config::set('message-interceptor.to.name', 'Mozammil');
        Config::set('message-interceptor.preserveBcc', true);

        Config::set('message-interceptor.bcc', [
            'mozammil.kho@gmail.com'
        ]);

        $message->allows()->getBcc()->andReturns([
            'hello@moz.im' => null,
            'm@moz.im' => null,
            'mozammil@outlook.com' => null
        ]);

        $this->assertEquals(
            [
                'hello@moz.im' => null,
                'm@moz.im' => null,
                'mozammil@outlook.com' => null,
                'mozammil.kho@gmail.com' => null
            ],
            $interceptor->getFilteredBccRecipients($message)
        );
    }

    public function test_it_can_get_bcc_recipients_correctly_while_bcc_is_not_preserved()
    {
        $interceptor = new Interceptor();

        $message = Mockery::mock(Swift_Message::class);

        Config::set('message-interceptor.to.address', 'me@mozammil.com');
        Config::set('message-interceptor.to.name', 'Mozammil');
        Config::set('message-interceptor.preserveBcc', false);

        Config::set('message-interceptor.bcc', [
            'mozammil.kho@gmail.com'
        ]);

        $message->allows()->getBcc()->andReturns([
            'hello@moz.im' => null,
            'm@moz.im' => null,
            'mozammil@outlook.com' => null
        ]);

        $this->assertEquals(
            ['mozammil.kho@gmail.com' => null],
            $interceptor->getFilteredBccRecipients($message)
        );
    }

    public function test_it_can_intercept_message_correctly_and_event_is_dispatched()
    {
        Event::fake();

        $interceptor = new Interceptor();

        $message = Mockery::mock(Swift_Message::class);

        Config::set('message-interceptor.to.address', 'me@mozammil.com');
        Config::set('message-interceptor.to.name', 'Mozammil');

        $recipients = [
            'hello@moz.im' => null
        ];

        $message->allows()->getTo()->andReturns($recipients);

        $message->expects()->setTo([
            'me@mozammil.com' => 'Mozammil'
        ]);

        $message->expects()->setCc([]);
        $message->expects()->setBcc([]);

        $this->assertInstanceOf(Swift_Message::class, $interceptor->intercept($message));

        Event::assertDispatched(MessageIntercepted::class);
    }
}