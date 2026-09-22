<?php

declare(strict_types=1);

namespace App\Infrastructure\Database;

final class DatabaseConfig
{
    public function __construct(
        public readonly string $host = 'localhost',
        public readonly string $dbname = 'tit_bd2',
        public readonly string $username = 'root',
        public readonly string $password = '',
        public readonly string $charset = 'utf8mb4',
    ) {
    }

    /** @return array<string,string> */
    public static function fromEnv(): array
    {
        return [
            'host' => $_ENV['DB_HOST'] ?? 'localhost',
            'dbname' => $_ENV['DB_NAME'] ?? 'tit_bd2',
            'username' => $_ENV['DB_USER'] ?? 'root',
            'password' => $_ENV['DB_PASS'] ?? '',
            'charset' => 'utf8mb4',
        ];
    }

    public function dsn(): string
    {
        return \sprintf(
            'mysql:host=%s;dbname=%s;charset=%s',
            $this->host,
            $this->dbname,
            $this->charset,
        );
    }
}
