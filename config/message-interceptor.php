<?php

return [

    /**
     * Whether or not the emails being sent should
     * be intercepted.
     */
    'enabled' => env('MESSAGE_INTERCEPTOR_ENABLED', false),

    /**
     * The intercepted emails will be sent
     * to this address instead. If the address
     * is not send, an exception will be thrown
     */
    'to' => [
        'address' => env('MESSAGE_INTERCEPTOR_ADDRESS', ''),
        'name' => env('MESSAGE_INTERCEPTOR_NAME', '')
    ],

    /**
     * By default, we will clear the recipients that are
     * cc'ed in the message. If you want to preserve the
     * cc'ed recipients, set this to true
     */
    'preserveCc' => env('MESSAGE_INTERCEPTOR_PRESERVE_CC', false),

    /**
     * By default, we will clear the recipients that are
     * bcc'ed in the message. If you want to preserve the
     * bcc'ed recipients, set this to true
     */
    'preserveBcc' => env('MESSAGE_INTERCEPTOR_PRESERVE_BCC', false),

    /**
     * The following addresses will also be CC'ed
     * when the email is intercepted and sent.
     */
    'cc' => [],

    /**
     * The following addresses will also be BCC'ed
     * when the email is intercepted and sent.
     */
    'bcc' => [],

    /**
     * Whitelisted email recipients.
     */
    'whitelist' => [
        /**
         * Email addresses in the list below will
         * not be filtered out
         */
        'emails' => [],

        /**
         * Email address matching the domain in
         * the list below will not be filtered out
         */
        'domains' => []
    ]
];