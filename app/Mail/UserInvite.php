<?php

namespace App\Mail;

use App\Option;
use Illuminate\Bus\Queueable;
use Illuminate\Container\Container;
use Illuminate\Contracts\Mail\Mailer as MailerContract;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UserInvite extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * User to whom invitation is sent.
     */
    public $user;

    /**
     * Create a new message instance.
     *
     */
    public function __construct($user)
    {
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $message = $this->subject(__('Welcome to :company_name', ['company_name' => Option::get('company_name')]))
                    ->view('emails/user/user_invite')
                    ->text('emails/user/user_invite_text');

        return $message;
    }
}
