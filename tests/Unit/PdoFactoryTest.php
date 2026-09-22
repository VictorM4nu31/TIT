<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Infrastructure\Database\DatabaseConfig;
use App\Infrastructure\Database\PdoFactory;
use PDO;
use PHPUnit\Framework\TestCase;

final class PdoFactoryTest extends TestCase
{
    public function testCreateWithSqliteMemory(): void
    {
        $config = new DatabaseConfig(host: 'sqlite', dbname: ':memory:', username: '', password: '');
        $pdo = PdoFactory::create($config);
        self::assertInstanceOf(PDO::class, $pdo);
        self::assertSame(PDO::ERRMODE_EXCEPTION, $pdo->getAttribute(PDO::ATTR_ERRMODE));
    }

    public function testCreateForAdminUsesDefaultDb(): void
    {
        $_ENV['DB_HOST'] = 'sqlite';
        $_ENV['DB_NAME'] = ':memory:';
        $_ENV['DB_ADMIN_NAME'] = ':memory:';
        $pdo = PdoFactory::createForAdmin();
        self::assertInstanceOf(PDO::class, $pdo);
        unset($_ENV['DB_HOST'], $_ENV['DB_NAME'], $_ENV['DB_ADMIN_NAME']);
    }

    public function testCreateForAdminUsesEnvWhenSet(): void
    {
        $tmp = sys_get_temp_dir() . '/tranzit_admin_' . bin2hex(random_bytes(4)) . '.db';
        $_ENV['DB_HOST'] = 'sqlite';
        $_ENV['DB_ADMIN_NAME'] = $tmp;
        $pdo = PdoFactory::createForAdmin();
        self::assertInstanceOf(PDO::class, $pdo);
        // mutant would use 'bd_tranzit' instead of $tmp, so $tmp file would not be created
        // force creation of file by creating a table
        $pdo->exec('CREATE TABLE t (id INTEGER)');
        self::assertFileExists($tmp);
        $pdo = null;
        @unlink($tmp);
        @unlink('bd_tranzit');
        unset($_ENV['DB_HOST'], $_ENV['DB_ADMIN_NAME']);
    }

    public function testCreateWithNullUsesFromEnv(): void
    {
        $_ENV['DB_HOST'] = 'sqlite';
        $_ENV['DB_NAME'] = ':memory:';
        $pdo = PdoFactory::create(null);
        self::assertInstanceOf(PDO::class, $pdo);
        unset($_ENV['DB_HOST'], $_ENV['DB_NAME']);
    }

    public function testCreateWithMysqlThrowsWhenUnavailable(): void
    {
        $config = new DatabaseConfig(host: '127.0.0.1', dbname: 'no_such_db', username: 'root', password: 'wrong');
        $this->expectException(\PDOException::class);
        PdoFactory::create($config);
    }

    public function testSqliteDsnUsesDbname(): void
    {
        $tmp1 = sys_get_temp_dir() . '/tranzit_' . bin2hex(random_bytes(4)) . '.db';
        $tmp2 = sys_get_temp_dir() . '/tranzit_' . bin2hex(random_bytes(4)) . '.db';
        $pdo1 = PdoFactory::create(new DatabaseConfig(host: 'sqlite', dbname: $tmp1));
        $pdo2 = PdoFactory::create(new DatabaseConfig(host: 'sqlite', dbname: $tmp2));
        $pdo1->exec('CREATE TABLE t1 (id INTEGER)');
        // if concat mutant removes dbname, both would be 'sqlite:' (memory) and share? Actually both would be memory, not files, so table would not persist to file
        // Check that tmp1 file exists and tmp2 does not have table
        self::assertFileExists($tmp1);
        self::assertFileExists($tmp2);
        // tmp2 should not have t1
        $stmt = $pdo2->query("SELECT name FROM sqlite_master WHERE type='table' AND name='t1'");
        self::assertNotFalse($stmt);
        $row = $stmt->fetch();
        self::assertFalse($row);
        $pdo1 = null;
        $pdo2 = null;
        @unlink($tmp1);
        @unlink($tmp2);
    }
}
