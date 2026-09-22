<?php

declare(strict_types=1);

namespace App\Infrastructure\Database;

use PDO;

final class PdoFactory
{
    public static function create(?DatabaseConfig $config = null): PDO
    {
        $cfg = $config ?? new DatabaseConfig(
            ...DatabaseConfig::fromEnv(),
        );

        if ($cfg->host === 'sqlite') {
            $dsn = 'sqlite:' . $cfg->dbname;
            $pdo = new PDO($dsn, null, null, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);

            return $pdo;
        }

        $pdo = new PDO(
            $cfg->dsn(),
            $cfg->username,
            $cfg->password,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ],
        );

        return $pdo;
    }

    /**
     * Crea PDO para BD administrativa (bd_tranzit) si difiere.
     */
    public static function createForAdmin(): PDO
    {
        $env = DatabaseConfig::fromEnv();
        $config = new DatabaseConfig(
            host: $env['host'],
            dbname: $_ENV['DB_ADMIN_NAME'] ?? 'bd_tranzit',
            username: $env['username'],
            password: $env['password'],
        );

        return self::create($config);
    }
}
