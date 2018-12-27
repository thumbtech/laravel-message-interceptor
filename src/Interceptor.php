<?php

namespace Mozammil\LaravelMessageInterceptor;

use Swift_Message;
use Illuminate\Support\Collection;
use Mozammil\LaravelMessageInterceptor\Events\MessageIntercepted;

class Interceptor
{
    /**
     * Interrcepts the message
     *
     * @param \Swift_Message $message
     *
     * @return void
     */
    public function intercept(Swift_Message $message)
    {
        event(new MessageIntercepted($message));

        $message->setTo($this->getFilteredRecipients($message));
        $message->setCc($this->getFilteredCcRecipients($message));
        $message->setBcc($this->getFilteredBccRecipients($message));

        return $message;
    }

    /**
     * If we should intercept the message
     *
     * @return bool
     */
    public function shouldInterceptMessage()
    {
        return config('message-interceptor.enabled', false);
    }

    /**
     * Gets a list of whitelisted recipient
     *
     * @param array $recipients
     *
     * @return \Illuminate\Support\Collection
     */
    public function getFilteredRecipients(Swift_Message $message)
    {
        $recipients = $message->getTo();

        $whitelisted = collect($recipients)
            ->filter(function ($name, $email) {
                return $this->isRecipientWhitelisted($email);
            });

        $to = collect([
            config('message-interceptor.to.address', null) => config('message-interceptor.to.name', null)
        ]);

        return $whitelisted->merge($to)->toArray();
    }

    /**
     * Retrieves CC recipients
     *
     * @param \Swift_Message $message
     *
     * @return array
     */
    public function getFilteredCcRecipients(Swift_Message $message)
    {
        $recipients = config('message-interceptor.preserveCc', false) ? $message->getCc() : [];

        $moreCc = collect(config('message-interceptor.cc', false))->mapWithKeys(function ($email) {
            return [$email => null];
        });

        return collect($recipients)->merge($moreCc)->toArray();
    }

    /**
     * Retrieves BCC recipients
     *
     * @param \Swift_Message $message
     *
     * @return array
     */
    public function getFilteredBccRecipients(Swift_Message $message)
    {
        $recipients = config('message-interceptor.preserveBcc', false) ? $message->getBcc() : [];

        $moreBcc = collect(config('message-interceptor.bcc', false))->mapWithKeys(function ($email) {
            return [$email => null];
        });

        return collect($recipients)->merge($moreBcc)->toArray();
    }

    /**
     * Checks if a recipient has been whitelisted
     *
     * @param string $email
     *
     * @return boolean
     */
    public function isRecipientWhitelisted(string $email)
    {
        $whitelistedEmails  = config('message-interceptor.whitelist.emails', []);
        $whitelistedDomains = config('message-interceptor.whitelist.domains', []);

        $whitelisted = in_array($email, $whitelistedEmails) ||
            in_array($this->getDomain($email), $whitelistedDomains);

        return $whitelisted ? true : false;
    }

    /**
     * Gets the domain of a given email
     *
     * @param string $email
     *
     * @return string
     */
    private function getDomain(string $email)
    {
        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Invalid Email: {$email}");
        }

        return last(explode('@', $email));
    }
}