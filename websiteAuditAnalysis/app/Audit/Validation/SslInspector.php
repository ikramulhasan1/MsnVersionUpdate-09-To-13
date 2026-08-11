<?php

declare(strict_types=1);

namespace App\Audit\Validation;

use App\Audit\Validation\Contracts\SslInspectorInterface;
use App\Audit\Validation\DTO\SslInfo;

final class SslInspector implements SslInspectorInterface
{
    public function inspect(string $host, int $timeoutSeconds): SslInfo
    {
        $context = stream_context_create([
            'ssl' => [
                'capture_peer_cert' => true,
                'verify_peer' => false,
                'verify_peer_name' => false,
                'SNI_enabled' => true,
                'peer_name' => $host,
            ],
        ]);

        $client = @stream_socket_client(
            "ssl://{$host}:443",
            $errno,
            $errstr,
            $timeoutSeconds,
            STREAM_CLIENT_CONNECT,
            $context
        );

        if ($client === false) {
            return SslInfo::unavailable($errstr !== '' ? $errstr : 'Unable to open a TLS connection on port 443.');
        }

        try {
            $params = stream_context_get_params($client);
            $cert = $params['options']['ssl']['peer_certificate'] ?? null;

            if ($cert === null) {
                return SslInfo::unavailable('No peer certificate was presented.');
            }

            $parsed = openssl_x509_parse($cert);

            if ($parsed === false) {
                return SslInfo::unavailable('Unable to parse the peer certificate.');
            }

            $validTo = (int) ($parsed['validTo_time_t'] ?? 0);
            $daysUntilExpiry = $validTo > 0
                ? (int) floor(($validTo - time()) / 86400)
                : null;

            $issuer = $parsed['issuer']['O'] ?? $parsed['issuer']['CN'] ?? null;

            return new SslInfo(
                valid: $daysUntilExpiry !== null && $daysUntilExpiry >= 0,
                issuer: $issuer,
                validFrom: isset($parsed['validFrom_time_t'])
                    ? date(DATE_ATOM, (int) $parsed['validFrom_time_t'])
                    : null,
                validTo: $validTo > 0 ? date(DATE_ATOM, $validTo) : null,
                daysUntilExpiry: $daysUntilExpiry,
            );
        } finally {
            fclose($client);
        }
    }
}
