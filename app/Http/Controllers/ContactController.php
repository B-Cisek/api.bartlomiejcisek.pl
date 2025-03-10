<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Exceptions\ReCaptchaException;
use App\Http\Requests\ContactRequest;
use App\Mail\ContactFormMail;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

final class ContactController extends Controller
{
    public function __invoke(ContactRequest $request): JsonResponse
    {
        try {
            $attributes = $request->validated();
            $this->validateRecaptchaToken($attributes['recaptcha_token']);
            unset($attributes['recaptcha_token']);

            $to = Config::get('mail.email');

            Mail::to($to)->queue(new ContactFormMail(...$attributes));

            return new JsonResponse();
        } catch (ReCaptchaException $e) {
            return new JsonResponse(['error' => $e->getMessage()], 400);
        }
    }

    private function validateRecaptchaToken(string $token): void
    {
        $params = http_build_query([
            'secret' => env('GOOGLE_RE_CAPTCHA_SECRET'),
            'response' => $token,
        ]);

        $response = Http::post(env('GOOGLE_RE_CAPTCHA_URL') . '?' . $params)->json();

        if ($response['success'] === false) {
            throw new ReCaptchaException();
        }
    }
}
