<?php

namespace Modules\Auth\Emails;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VerifyUserEmail extends Mailable
{
    use Queueable, SerializesModels;

    private array $data;

    public function __construct(
        array $data,
        private readonly ?string $viewFile = null,
        string $subject = 'Email Verification',
    ) {
        $this->subject = $subject;
        $this->data = $data;
    }

    public function envelope(): Envelope
    {
        return new Envelope;
    }

    public function content(): Content
    {
        return new Content(
            $this->view,
            with: [
                'expiresAfter' => $this->data['expire_after'],
                'code' => $this->data['code'],
            ]
        );
    }

    public function build(): VerifyUserEmail
    {
        return $this->view($this->viewFile ?: 'auth::verify-email', ['data' => $this->data]);
    }
}
