<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Mail\ContactFormMail;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ContactControllerTest extends TestCase
{
    #[Test]
    public function it_contact_form_can_be_submitted_with_valid_data(): void
    {
        Mail::fake();
        $adminEmail = Config::get('mail.email');
        $validData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'message' => 'This is a test message',
        ];

        $response = $this->postJson('/api/contact', $validData);

        $response->assertStatus(200);
        Mail::assertQueued(function (ContactFormMail $mail) use ($validData, $adminEmail) {
            return $mail->hasTo($adminEmail) &&
                $mail->envelope()->from->address === $validData['email'] &&
                $mail->envelope()->from->name === $validData['name'] &&
                $mail->envelope()->subject === ContactFormMail::SUBJECT;
        });
    }


    #[Test]
    public function it_contact_form_validates_required_fields(): void
    {
        Mail::fake();

        $response = $this->postJson('/api/contact', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email', 'message']);

        Mail::assertNothingQueued();
    }

    #[Test]
    public function it_contact_form_validates_email_format(): void
    {
        Mail::fake();

        $invalidData = [
            'name' => 'John Doe',
            'email' => 'not-an-email',
            'message' => 'This is a test message',
        ];

        $response = $this->postJson('/api/contact', $invalidData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);

        Mail::assertNothingQueued();
    }

    #[Test]
    public function it_contact_form_validates_field_lengths(): void
    {
        Mail::fake();

        $invalidData = [
            'name' => str_repeat('a', 61), // Exceeds max:60
            'email' => str_repeat('a', 110) . '@example.com', // Exceeds max:120
            'message' => str_repeat('a', 1001), // Exceeds max:1000
        ];

        $response = $this->postJson('/api/contact', $invalidData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email', 'message']);

        Mail::assertNothingQueued();
    }
}
