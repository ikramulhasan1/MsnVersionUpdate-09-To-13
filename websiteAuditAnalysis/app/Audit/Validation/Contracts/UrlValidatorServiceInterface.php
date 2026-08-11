<?php

declare(strict_types=1);

namespace App\Audit\Validation\Contracts;

use App\Audit\Validation\DTO\UrlValidationResult;

interface UrlValidatorServiceInterface
{
    public function validate(string $url): UrlValidationResult;
}
