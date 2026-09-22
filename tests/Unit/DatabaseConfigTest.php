<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Infrastructure\Database\DatabaseConfig;
use PHPUnit\Framework\TestCase;

final class DatabaseConfigTest extends TestCase
{
    public function testDsnContainsCharset(): void
    {
        $cfg = new DatabaseConfig('localhost', 'tit_bd2', 'root', '', 'utf8mb4');
        self::assertStringContainsString('charset=utf8mb4', $cfg->dsn());
        self::assertStringContainsString('dbname=tit_bd2', $cfg->dsn());
    }

    public function testFromEnvDefaults(): void
    {
        $_ENV['DB_HOST'] = '127.0.0.1';
        $_ENV['DB_NAME'] = 'test_db';
        $env = DatabaseConfig::fromEnv();
        self::assertSame('127.0.0.1', $env['host']);
        self::assertSame('test_db', $env['dbname']);
        unset($_ENV['DB_HOST'], $_ENV['DB_NAME']);
    }

    public function testFromEnvDefaultsForUserAndPassword(): void
    {
        unset($_ENV['DB_USER'], $_ENV['DB_PASS']);
        $env = DatabaseConfig::fromEnv();
        self::assertSame('root', $env['username']);
        self::assertSame('', $env['password']);

        $_ENV['DB_USER'] = 'custom';
        $_ENV['DB_PASS'] = 'secret';
        $env = DatabaseConfig::fromEnv();
        self::assertSame('custom', $env['username']);
        self::assertSame('secret', $env['password']);
        unset($_ENV['DB_USER'], $_ENV['DB_PASS']);
    }

    public function testFromEnvWithEmptyStringFallsBack(): void
    {
        $_ENV['DB_HOST'] = '';
        $_ENV['DB_NAME'] = '';
        $env = DatabaseConfig::fromEnv();
        // empty string is still set, so ?? does not fallback; should return ''
        self::assertSame('', $env['host']);
        self::assertSame('', $env['dbname']);
        unset($_ENV['DB_HOST'], $_ENV['DB_NAME']);
    }
}
