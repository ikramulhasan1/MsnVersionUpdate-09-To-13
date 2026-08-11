<?php

declare(strict_types=1);

namespace App\Audit\Validation\DTO;

final readonly class UrlValidationResult implements \JsonSerializable
{
    /**
     * @param array<int, string> $redirectChain
     * @param array<int, string> $errors
     */
    public function __construct(
        public string $url,
        public bool $isValid,
        public bool $isFormatValid,
        public bool $isHttps,
        public bool $dnsResolved,
        public bool $domainExists,
        public ?string $ipAddress,
        public bool $reachable,
        public ?int $statusCode,
        public bool $redirected,
        public ?string $finalUrl,
        public array $redirectChain,
        public ?int $responseTimeMs,
        public ?SslInfo $ssl,
        public array $errors,
        public string $checkedAt,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'url' => $this->url,
            'valid' => $this->isValid,
            'format_valid' => $this->isFormatValid,
            'https' => $this->isHttps,
            'dns_resolved' => $this->dnsResolved,
            'domain_exists' => $this->domainExists,
            'ip_address' => $this->ipAddress,
            'reachable' => $this->reachable,
            'status_code' => $this->statusCode,
            'redirected' => $this->redirected,
            'redirect_count' => count($this->redirectChain),
            'final_url' => $this->finalUrl,
            'redirect_chain' => $this->redirectChain,
            'response_time_ms' => $this->responseTimeMs,
            'ssl' => $this->ssl?->toArray(),
            'errors' => $this->errors,
            'checked_at' => $this->checkedAt,
        ];
    }

    public function toJson(int $options = 0): string
    {
        return json_encode($this->toArray(), $options | JSON_THROW_ON_ERROR);
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
