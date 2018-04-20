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

    private function getKnownEntityId(string $knownEntityName, string $knownEntity): int
    {
        $tableName = $this->getTableName($knownEntity . "s", self::NEW_SCHEMA_NAME) . "_";
        $statement = $this->connection->prepare("SELECT `id` FROM " . $tableName . " WHERE `name` LIKE :name");
        $queryResult = $statement->execute([
            "name" => $knownEntityName
        ]);
        if (!$queryResult) {
            return -1;
        }
        $pageId = $statement->fetch()["id"];
        return isset($pageId) ? intval($pageId) : -1;
    }

    private function getNeededEntityIds(int $knownEntityId, string $knownEntity, string $neededEntity): array
    {
        $tableName = $this->getTableName($neededEntity . "_" . $knownEntity . "_relation", self::NEW_SCHEMA_NAME);
        $statement = $this->connection->prepare("SELECT " . $neededEntity . "_id" . " FROM " . $tableName . " WHERE `" . $knownEntity . "_id` = :knownEntityId");
        $queryResult = $statement->execute([
            "knownEntityId"   => $knownEntityId,
        ]);
        if (!$queryResult) {
            return [];
        }
        $ids = [];
        foreach ($statement->fetchAll() as $entity) {
            $ids[] = intval($entity[$neededEntity . "_id"]);
        }
        return $ids;
    }

    private function getNeededEntities(array $neededEntityIds, string $neededEntity): array
    {
        $tableName = $this->getTableName($neededEntity . "s", self::NEW_SCHEMA_NAME) . "_";
        $statement = $this->connection->prepare("SELECT * FROM " . $tableName . " WHERE `id` = :entityId");
        $entities = [];
        foreach ($neededEntityIds as $number => $entityId) {
            $queryResult = $statement->execute([
                "entityId" => $entityId,
            ]);
            if (!$queryResult) {
                return [];
            }
            $entities[] = $statement->fetchAll()[0];
        }
        return $entities;
    }

    public function getSpouse(string $knownEntityName, string $knownEntity, string $neededEntity): array
    {
        if (-1 === $knownEntityId = $this->getKnownEntityId($knownEntityName, $knownEntity)) { //просто page меняем на имя сущности
            return [];
        }

        $neededEntityIds = $this->getNeededEntityIds($knownEntityId, $knownEntity, $neededEntity); // такая же ерунда

        $neededEntities = $this->getNeededEntities($neededEntityIds, $neededEntity);

        return $neededEntities;
    }

    public function getEntity(string $entity): array
    {
        $tableName = $this->getTableName($entity, SELF::NEW_SCHEMA_NAME) . "s_";
        $sql = "SELECT * FROM " . $tableName . " LIMIT 300";
        $statement = $this->connection->prepare($sql);
        $querySuccess = $statement->execute();
        if (!$querySuccess) {
            return [];
        }
        return $statement->fetchAll();
    }
}