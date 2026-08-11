<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Audit\Enums\AuditStatus;
use App\Models\Audit;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Audit>
 */
final class AuditFactory extends Factory
{
    protected $model = Audit::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $url = 'https://'.fake()->unique()->domainName().'/';

        return [
            'uuid' => (string) Str::uuid(),
            'url' => $url,
            'status' => AuditStatus::QUEUED->value,
        ];
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => AuditStatus::COMPLETED->value,
        ]);
    }

    public function failed(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => AuditStatus::FAILED->value,
        ]);
    }
}
