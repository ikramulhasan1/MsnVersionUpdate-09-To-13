<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class StoreAuditRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'url' => ['required', 'url', 'max:2048', 'starts_with:http://,https://'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'url.required' => 'Please enter a website URL to audit.',
            'url.url' => 'Enter a valid URL, including http:// or https://.',
            'url.starts_with' => 'The URL must start with http:// or https://.',
        ];
    }
}
