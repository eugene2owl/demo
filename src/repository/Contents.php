<?php

declare(strict_types = 1);

namespace Demo\Repository;

require_once "../service/MyPDO.php";

use Demo\Service\myPDO;

class Contents
{
    private const SCHEMA_NAME = "contents";
    private const NEW_SCHEMA_NAME = "demo";
    private $connection;

    public function __construct()
    {
        $this->connection = myPDO::getConnection();
    }

    private function getTableName(string $entityName, string $schema = self::SCHEMA_NAME): string
    {
        return "$schema.$entityName";
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

    private function getPageId(string $pageName): int
    {
        $tableName = $this->getTableName("pages", self::NEW_SCHEMA_NAME) . "_";
        $statement = $this->connection->prepare("SELECT `id` FROM " . $tableName . " WHERE `name` LIKE :name");
        $queryResult = $statement->execute([
            "name" => $pageName
        ]);
        if (!$queryResult) {
            return -1;
        }
        $pageId = $statement->fetch()["id"];
        return isset($pageId) ? intval($pageId) : -1;
    }

    private function getEntityIds(int $pageId, string $entityName): array
    {
        $tableName = $this->getTableName($entityName . "_page_relation", self::NEW_SCHEMA_NAME);
        $statement = $this->connection->prepare("SELECT " . $entityName . "_id" . " FROM " . $tableName . " WHERE `page_id` = :pageId");
        $queryResult = $statement->execute([
            "pageId"   => $pageId,
        ]);
        if (!$queryResult) {
            return [];
        }
        $ids = [];
        foreach ($statement->fetchAll() as $entity) {
            $ids[] = intval($entity[$entityName . "_id"]);
        }
        return $ids;
    }

    private function getEntities(array $entityIds, string $entityName): array
    {
        $tableName = $this->getTableName($entityName . "s", self::NEW_SCHEMA_NAME) . "_";
        $statement = $this->connection->prepare("SELECT `name` FROM " . $tableName . " WHERE `id` = :entityId");
        $entities = [];
        foreach ($entityIds as $entityId) {
            $queryResult = $statement->execute([
                "entityId" => $entityId,
            ]);
            if (!$queryResult) {
                return [];
            }
            $entities[] = $statement->fetchAll();
        }
        return $entities;
    }

    public function getEntityFromPage(string $pageName, string $entityName): array
    {
        if (-1 === $pageId = $this->getPageId($pageName)) {
            return [];
        }

        $entityIds = $this->getEntityIds($pageId, $entityName);

        $entities = $this->getEntities($entityIds, $entityName);

        return $entities;
    }

    public function getSpouse(): array
    {
        // сюда летит имя списка, я получаю массив его элементов. летит имя кода, я получаю массив его абзацев
        // летит абзац, я получаю массив его кодов
    }
}