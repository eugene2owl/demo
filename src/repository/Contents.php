<?php

declare(strict_types = 1);

namespace Demo\Repository;

require_once "../service/MyPDO.php";

use Demo\Service\myPDO;

class Contents
{
    private const SCHEMA_NAME = "contents";
    private $connection;

    public function __construct()
    {
        $this->connection = myPDO::getConnection();
    }

    private function getTableName(string $name, string $schema = self::SCHEMA_NAME): string
    {
        return "$schema.$name";
    }

    public function getEntityArray(string $name): array
    {
        $tableName = $this->getTableName($name);
        $sql = "SELECT * FROM " . $tableName . " LIMIT 300";
        $statement = $this->connection->prepare($sql);
        $querySuccess = $statement->execute();
        if (!$querySuccess) {
            return [];
        }
        return $statement->fetchAll();
    }

    public function getEntityArrayOnPage(string $name, string $page): array
    {
        $tableName = $this->getTableName($name);
        $sql = "SELECT * FROM " . $tableName . " WHERE `page` = :page LIMIT 300";
        $statement = $this->connection->prepare($sql);
        $querySuccess = $statement->execute([
            "page" => $page,
        ]);
        if (!$querySuccess) {
            return [];
        }
        return $statement->fetchAll();
    }
}