<?php

declare(strict_types=1);

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactFormMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public const string SUBJECT = 'Contact Form Mail';

    public function __construct(
        private readonly string $name,
        private readonly string $email,
        private readonly string $message,
    ){}

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address($this->email, $this->name),
            subject: self::SUBJECT,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.contact',
            with: [
                'name' => $this->name,
                'email' => $this->email,
                'message' => $this->message,
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
