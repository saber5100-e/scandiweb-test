<?php

namespace App\Database;

use mysqli;
use RuntimeException;

class Database {
    private static ?mysqli $conn = null;

    public static function getConnection(): mysqli {
        if (self::$conn === null) {
            self::$conn = new mysqli($_ENV['DB_HOST'],  $_ENV['DB_USER'],  $_ENV['DB_PASS'], $_ENV['DB_NAME']);
            if (self::$conn->connect_error) {
                throw new RuntimeException("Connection failed: " . self::$conn->connect_error);
            }
        }
        return self::$conn;
    }

    public static function close(): void {
        if (self::$conn !== null) {
            self::$conn->close();
            self::$conn = null;
        }
    }
}