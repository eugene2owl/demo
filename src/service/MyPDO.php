<?php

declare(strict_types = 1);

namespace Demo\Service;

class myPDO
{
    private const NO_CONNECTION = "Database is not available.";
    private static $dsn = null;
    private static $username = null;
    private static $password = null;
    private static $dbname = null;
    private static $charset = null;

    private static $connection;

    final private function __construct() { }
    final private function __clone() { }

    private static function setParameters(): void
    {
        $parameters = parse_ini_file("/home/eugene/PhpstormProjects/demo/.env");
        self::$charset = $parameters["charset"];
        self::$dbname = $parameters["dbname"];
        self::$dsn = $parameters["dsn"];
        self::$dsn .= ";dbname=" . self::$dbname;
        self::$dsn .= ";charset=" . self::$charset;
        self::$username = $parameters["username"];
        self::$password = $parameters["password"];
    }

    public static function getConnection(): \PDO
    {
        if (is_null(self::$connection)) {
            self::setParameters();
            try {
                self::$connection = new \PDO(
                    self::$dsn,
                    self::$username,
                    self::$password
                );
            } catch (\Exception $exception) {
                error_log($exception->getMessage());
                die(self::NO_CONNECTION);
            }
        }
        return self::$connection;
    }
}