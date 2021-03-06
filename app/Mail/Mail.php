<?php

namespace App\Mail;

use App\Mailbox;
use App\Option;

class Mail
{
    /**
     * Reply separators.
     */
    const REPLY_SEPARATOR_HTML = 'fsReplyAbove';
    const REPLY_SEPARATOR_TEXT = '-- Please reply above this line --';

    /**
     * Message-ID prefixes for outgoing emails.
     */
    const MESSAGE_ID_PREFIX_NOTIFICATION = 'notify';
    const MESSAGE_ID_PREFIX_NOTIFICATION_IN_REPLY = 'conversation';
    const MESSAGE_ID_PREFIX_REPLY_TO_CUSTOMER = 'reply';
    const MESSAGE_ID_PREFIX_AUTO_REPLY = 'autoreply';

    /**
     * If reply is not extracted properly from the incoming email, add here new separator.
     * Order is not important.
     */
    public static $alternative_reply_separators = [
        self::REPLY_SEPARATOR_HTML,
        self::REPLY_SEPARATOR_TEXT,
        '<div class="gmail_quote">',
        '<blockquote',
    ];

    /**
     * Configure mail sending parameters.
     *
     * @param App\Mailbox $mailbox
     */
    public static function setMailDriver($mailbox = null, $user_from = null)
    {
        if ($mailbox) {
            // Configure mail driver according to Mailbox settings
            \Config::set('mail.driver', $mailbox->getMailDriverName());
            \Config::set('mail.from', $mailbox->getMailFrom($user_from));

            // SMTP
            if ($mailbox->out_method == Mailbox::OUT_METHOD_SMTP) {
                \Config::set('mail.host', $mailbox->out_server);
                \Config::set('mail.port', $mailbox->out_port);
                \Config::set('mail.username', $mailbox->out_username);
                \Config::set('mail.password', $mailbox->out_password);
                \Config::set('mail.encryption', $mailbox->getOutEncryptionName());
            }
        } else {
            // Use default settings
            \Config::set('mail.driver', env('MAIL_DRIVER'));
            \Config::set('mail.from', ['address' => env('MAIL_FROM_ADDRESS'), 'name' => '']);
        }

        (new \Illuminate\Mail\MailServiceProvider(app()))->register();
    }

    /**
     * Set system mail driver for sending system emails to users.
     *
     * @param App\Mailbox $mailbox
     */
    public static function setSystemMailDriver()
    {
        \Config::set('mail.driver', self::getSystemMailDriver());
        \Config::set('mail.from', [
            'address' => self::getSystemMailFrom(), 
            'name' => Option::get('company_name', \Config::get('app.name'))
        ]);

        (new \Illuminate\Mail\MailServiceProvider(app()))->register();
    }

    /**
     * Replace mail vars in the text.
     */
    public static function replaceMailVars($text, $data = [])
    {
        // Available variables to insert into email in UI.
        $vars = [
            '{%subject%}'             => $data['conversation']->subject,
            '{%mailbox.email%}'       => $data['mailbox']->email,
            '{%mailbox.name%}'        => $data['mailbox']->name,
            '{%conversation.number%}' => $data['conversation']->number,
            '{%customer.fullName%}'   => $data['customer']->getFullName(true),
            '{%customer.firstName%}'  => $data['customer']->getFirstName(true),
            '{%customer.lastName%}'   => $data['customer']->last_name,
            '{%customer.email%}'      => $data['conversation']->customer_email
        ];

        return strtr($text, $vars);
    }

    /**
     * Check if text has vars in it.
     */
    public static function hasVars($text)
    {
        return preg_match("/({%|%})/", $text);
    }

    /**
     * Remove email from a list of emails.
     */
    public static function removeEmailFromArray($list, $email)
    {
        return array_diff($list, [$email]);
    }

    /**
     * From address for sending system emails.
     */
    public static function getSystemMailFrom()
    {
        $mail_from = Option::get('mail_from', env('MAIL_FROM_ADDRESS'));
        if (!$mail_from) {
            $mail_from = 'freescout@'.parse_url(\Config::get('app.url'), PHP_URL_HOST);
        }
        return $mail_from;
    }

    /**
     * Mail driver for sending system emails.
     */
    public static function getSystemMailDriver()
    {
        return Option::get('mail_driver', 'mail');
    }
}
