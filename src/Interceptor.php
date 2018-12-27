<?php

namespace Mozammil\LaravelMessageInterceptor;

use Swift_Message;
use InvalidArgumentException;
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
        $to = [config('message-interceptor.to.address') => config('message-interceptor.to.name')];

        return collect($message->getTo())
            ->filter(function ($name, $email) {
                return $this->isRecipientWhitelisted($email);
            })
            ->merge(collect($to))
            ->toArray();
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
        $preserveCc = config('message-interceptor.preserveCc', false);

        $recipients = $preserveCc ? $message->getCc() : [];

        $moreCc = collect(config('message-interceptor.cc', []))
            ->mapWithKeys(function ($email) {
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
        $preserveBcc = config('message-interceptor.preserveBcc', false);

        $recipients = $preserveBcc ? $message->getBcc() : [];

        $moreBcc = collect(config('message-interceptor.bcc', []))
            ->mapWithKeys(function ($email) {
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
            throw new InvalidArgumentException("Invalid email supplied: {$email}");
        }

        return last(explode('@', $email));
    }
}