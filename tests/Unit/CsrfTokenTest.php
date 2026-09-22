<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Infrastructure\Security\CsrfToken;
use PHPUnit\Framework\TestCase;

final class CsrfTokenTest extends TestCase
{
    public function testGenerateAndValidate(): void
    {
        $_SESSION = [];
        $token = CsrfToken::generate();
        self::assertSame(64, \strlen($token));
        self::assertTrue(CsrfToken::validate($token));
        self::assertFalse(CsrfToken::validate('invalid'));
    }
}
