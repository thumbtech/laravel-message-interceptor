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
     * @return \Swift_Message
     */
    public function intercept(Swift_Message $message): Swift_Message
    {
        event(new MessageIntercepted($message));

        $message->setTo($this->getFilteredToRecipients($message));
        $message->setCc($this->getFilteredCopiedRecipients($message, 'cc'));
        $message->setBcc($this->getFilteredCopiedRecipients($message, 'bcc'));

        return $message;
    }

    /**
     * If we should intercept the message
     *
     * @return bool
     */
    public function shouldInterceptMessage(): bool
    {
        return config('message-interceptor.enabled', false);
    }

    /**
     * Gets a list of whitelisted recipients
     *
     * @param \Swift_Message $message
     *
     * @return array
     */
    public function getFilteredToRecipients(Swift_Message $message): array
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
     * Retrieves CC or BCC recipients
     *
     * @param \Swift_Message $message
     *
     * @return array
     */
    public function getFilteredCopiedRecipients(Swift_Message $message, string $type): array
    {
        $preserve = config('message-interceptor.preserve' . ucfirst($type), false);

        $method = 'get' . ucfirst($type);
        $recipients = $preserve ? $message->{$method}() : [];

        $more = collect(config('message-interceptor.' . strtolower($type), []))
            ->mapWithKeys(function ($email) {
                return [$email => null];
            });

        return collect($recipients)->merge($more)->toArray();
    }

    /**
     * Checks if a recipient has been whitelisted
     *
     * @param string $email
     *
     * @return boolean
     */
    public function isRecipientWhitelisted(string $email): bool
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
    private function getDomain(string $email): string
    {
        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("Invalid email supplied: {$email}");
        }

        return last(explode('@', $email));
    }
}