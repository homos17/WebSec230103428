<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VerificationEmail extends Mailable
{
    use Queueable, SerializesModels;

    private $link = null;
    private $name = null;

    /**
     * Create a new message instance.
     */
    public function __construct($link, $name) {

        $this->link = $link; $this->name = $name;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Verification Email',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.verification',
            with: [ 'link' => $this->link,'name' => $this->name],
        );
    }
    public function build()
    {
        return $this->view('emails.reset_password')
                    ->with(['link' => $this->link, 'name' => $this->name]);
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
