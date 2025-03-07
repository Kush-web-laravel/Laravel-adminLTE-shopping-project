<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SendResetPassword extends Mailable
{
    use Queueable, SerializesModels;

    public $token;
    public $email;
    /**
     * Create a new message instance.
     */
    public function __construct($token, $email)
    {
        //
        $this->token = $token;
        $this->email = $email;
    }

    /**
     * Get the message envelope.
     */

    public function build()
    {
        $resetUrl = url('/reset-password?token=' . $this->token . '&email=' . urlencode($this->email));
        return $this->subject('Reset Password')
        ->view('emails.reset-password')
        ->with(['resetUrl' => $resetUrl]);
    }
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Send Reset Password',
        );
    }

    /**
     * Get the message content definition.
     */

}
