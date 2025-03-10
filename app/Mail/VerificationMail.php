<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VerificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $verificationUrl;

    /**
     * Create a new message instance.
     */
    public function __construct($user, $verificationUrl)
    {
        $this->user = $user;
        $this->verificationUrl = $verificationUrl;
    }

    /**
     * Build the email message.
     */
    public function build()
    {
        return $this->subject('Verify Your Account')
            ->html("
                <h2>Hello, {$this->user->name}</h2>
                <p>Thank you for registering. Click the link below to verify your email:</p>
                <a href='{$this->verificationUrl}'>Verify My Account</a>
            ");
    }
}
