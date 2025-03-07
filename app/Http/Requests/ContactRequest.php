<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContactRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'max:60', 'string'],
            'email' => ['required', 'email:filter', 'max:120'],
            'message' => ['required', 'string', 'max:1000'],
        ];
    }
}
