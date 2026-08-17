<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Audit\Enums\AuditMode;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Phase K3 (Bulk Audit) — backs BulkAuditController::store(), the one
 * endpoint both of the create-form's own submission paths (a pasted
 * URL-per-line textarea, or a CSV upload) post to. Exactly one of
 * `urls`/`csv` is required — see rules()'s own comment for why that's
 * enforced with `required_without` on both rather than a single
 * `required` on either.
 */
final class StoreBulkAuditRequest extends FormRequest
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
            'name' => ['nullable', 'string', 'max:255'],
            'mode' => ['required', Rule::enum(AuditMode::class)],
            // required_without each other — the form always shows
            // both the textarea and the file input (Phase K3's own
            // create.blade.php), but a person only ever fills in one;
            // requiring EITHER (not BOTH, and not just one unconditionally)
            // is what actually enforces "at least one of these two".
            'urls' => ['required_without:csv', 'nullable', 'string'],
            'csv' => ['required_without:urls', 'nullable', 'file', 'mimes:csv,txt', 'max:2048'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'urls.required_without' => 'Paste at least one URL, or upload a CSV file instead.',
            'csv.required_without' => 'Upload a CSV file, or paste at least one URL instead.',
            'csv.mimes' => 'The uploaded file must be a .csv or .txt file.',
        ];
    }
}