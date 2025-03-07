<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\ContactRequest;
use App\Mail\ContactFormMail;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;

final class ContactController extends Controller
{
    public function __invoke(ContactRequest $request): JsonResponse
    {
        $to = Config::get('mail.email');

        Mail::to($to)->queue(new ContactFormMail(...$request->validated()));

        return new JsonResponse();
    }
}
