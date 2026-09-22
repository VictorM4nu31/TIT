<?php

declare(strict_types=1);

namespace App\Infrastructure\Security;

final class CsrfToken
{
    public static function generate(): string
    {
        $token = bin2hex(random_bytes(32));
        $_SESSION['csrf_token'] = $token;

        return $token;
    }

    public static function validate(string $token): bool
    {
        /** @var string $stored */
        $stored = $_SESSION['csrf_token'] ?? '';

        return hash_equals($stored, $token);
    }
}
