<?php

namespace Demo\Service;

class myPDO
{
    private static $dsn = null;
    private static $username = null;
    private static $password = null;
    private static $charset = null;

    private static $connection;

    final private function __construct() { }
    final private function __clone() { }

    private static function setParameters(): void
    {
        $parameters = parse_ini_file("/home/eugene/PhpstormProjects/demo/.env");
        self::$charset = $parameters["charset"];
        self::$dsn = $parameters["dsn"];
        self::$dsn .= ";charset=" . self::$charset;
        self::$username = $parameters["username"];
        self::$password = $parameters["password"];
    }

    public static function getConnection(): \PDO
    {
        if (is_null(self::$connection)) {
            self::setParameters();
            self::$connection = new \PDO(
                self::$dsn,
                self::$username,
                self::$password
            );
        }
        return self::$connection;
    }
}