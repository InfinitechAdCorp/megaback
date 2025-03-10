<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AccountApprovedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;

    public function __construct($user)
    {
        $this->user = $user;
    }

    public function build()
    {
        $htmlContent = "
            <h2>Hello, {$this->user->name}!</h2>
            <p>Your account has been approved successfully.</p>
            <p>You can now log in using your registered email: <strong>{$this->user->email}</strong></p>
            <p>Click the link below to log in:</p>
            <p>
                <a href='http://localhost:3000/auth' 
                   style='display: inline-block; padding: 10px 20px; background-color: #007bff; color: #fff; text-decoration: none;'>
                    Log In
                </a>
            </p>
            <p>Thank you for joining us!</p>
        ";

        return $this->subject('Your Account Has Been Approved')
                    ->html($htmlContent); // Sending inline HTML
    }
}

