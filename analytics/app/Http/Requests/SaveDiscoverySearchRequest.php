<?php

declare(strict_types=1);

namespace App\Http\Requests;

/**
 * Validates "Save this search" (Phase F3) — the same filter fields
 * SearchDiscoveryRequest already validates for a plain search
 * submission (see that class's own docblock), plus a required `name`
 * to save the search under. Extending rather than duplicating those
 * ~50 lines of filter rules keeps both requests validating the exact
 * same filter shape by construction — a new filter field added to one
 * is automatically validated by the other too.
 */
final class SaveDiscoverySearchRequest extends SearchDiscoveryRequest
{
    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'name' => ['required', 'string', 'max:150'],
        ]);
    }
}