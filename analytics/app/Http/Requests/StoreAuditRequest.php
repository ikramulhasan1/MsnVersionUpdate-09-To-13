<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Audit\Enums\AuditMode;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class StoreAuditRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'url' => ['required', 'url', 'max:2048', 'starts_with:http://,https://'],
            // Phase K1 (Quick Scan Mode) — nullable, not required: the
            // form always submits a value now (see
            // resources/views/home/index.blade.php's own radio
            // toggle), but this stays optional at the validation layer
            // too, matching CreateAuditData::fromArray()'s own
            // FULL-by-default fallback for any OTHER caller of this
            // request that doesn't submit one.
            'mode' => ['nullable', Rule::enum(AuditMode::class)],
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