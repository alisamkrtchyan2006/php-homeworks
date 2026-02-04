<?php

declare(strict_types=1);

namespace App\Database;

use PDO;
use PDOException;

class Connection
{
    private static ?PDO $instance = null;

    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            $servername = getenv('MYSQL_HOST') ?: 'mysql';
            $username = getenv('MYSQL_USER') ?: 'admin';
            $password = getenv('MYSQL_PASSWORD') ?: 'root12';
            $dbname = getenv('MYSQL_DATABASE') ?: 'learning';

            try {
                self::$instance = new PDO(
                    "mysql:host=$servername;dbname=$dbname",
                    $username,
                    $password
                );
                self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                throw new \RuntimeException("Connection failed: " . $e->getMessage());
            }
        }

        return self::$instance;
    }

    public static function close(): void
    {
        self::$instance = null;
    }
}
