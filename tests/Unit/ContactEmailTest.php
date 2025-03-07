<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Mail\ContactFormMail;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class ContactEmailTest extends TestCase
{
    #[Test]
    public function it_email_has_correct_envelope(): void
    {
        $data = [
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'message' => 'Hello, this is a test message.',
        ];

        $mailable = new ContactFormMail($data['name'], $data['email'], $data['message']);
        $envelope = $mailable->envelope();

        $this->assertEquals($data['email'], $envelope->from->address);
        $this->assertEquals($data['name'], $envelope->from->name);
        $this->assertEquals(ContactFormMail::SUBJECT, $envelope->subject);
    }

    #[Test]
    public function it_email_has_correct_content(): void
    {
        $data = [
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'message' => 'Hello, this is a test message.',
        ];

        $mailable = new ContactFormMail($data['name'], $data['email'], $data['message']);
        $content = $mailable->content();

        $this->assertEquals('emails.contact', $content->markdown);
        $this->assertEquals([
            'name' => $data['name'],
            'email' => $data['email'],
            'message' => $data['message'],
        ], $content->with);
    }
}
