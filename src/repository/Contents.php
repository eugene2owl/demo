<?php

declare(strict_types = 1);

namespace Demo\Repository;

require_once "../service/MyPDO.php";

use Demo\Service\myPDO;

class Contents
{
    private const SCHEMA_NAME = "demo";
    private const ENTITY_TABLE_POSTFIX = "s_";
    private const BRIDGE = "_";
    private const RELATION_TABLE_POSTFIX = "_relation";
    private $connection;

    public function __construct()
    {
        $this->connection = myPDO::getConnection();
    }

    private function getEntityTableName(string $entityName, string $schema = self::SCHEMA_NAME): string
    {
        return "$schema.$entityName" . self::ENTITY_TABLE_POSTFIX;
    }

    private function getRelationTableName(string $firstEntityName, string $secondEntityName, string $schema = self::SCHEMA_NAME): string
    {
        return "$schema.$firstEntityName" . self::BRIDGE . $secondEntityName . self::RELATION_TABLE_POSTFIX;
    }

    private function getKnownEntityId(string $knownEntityName, string $knownEntity): int
    {
        $tableName = $this->getEntityTableName($knownEntity);
        $statement = $this->connection->prepare(
            "SELECT `id` FROM " .
            $tableName .
            " WHERE `name` LIKE :name"
        );
        $queryResult = $statement->execute([
            "name" => $knownEntityName
        ]);
        if (!$queryResult) {
            return -1;
        }
        $knownEntityId = $statement->fetch()["id"];
        return isset($knownEntityId) ? intval($knownEntityId) : -1;
    }

    private function getNeededEntityIds(int $knownEntityId, string $knownEntity, string $neededEntity): array
    {
        $tableName = $this->getRelationTableName($neededEntity, $knownEntity);
        $statement = $this->connection->prepare(
            "SELECT `" .
            $neededEntity . "_id`" .
            " FROM " .
            $tableName .
            " WHERE `" .
            $knownEntity . "_id` = :knownEntityId"
        );
        $queryResult = $statement->execute([
            "knownEntityId"   => $knownEntityId,
        ]);
        if (!$queryResult) {
            return [];
        }
        $neededEntityIds = [];
        foreach ($statement->fetchAll() as $entity) {
            $neededEntityIds[] = intval($entity[$neededEntity . "_id"]);
        }
        return $neededEntityIds;
    }

    private function getNeededEntities(array $neededEntityIds, string $neededEntity): array
    {
        $tableName = $this->getEntityTableName($neededEntity);
        $statement = $this->connection->prepare(
            "SELECT * FROM " .
            $tableName .
            " WHERE `id` = :entityId"
        );
        $entities = [];
        foreach ($neededEntityIds as $entityId) {
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

    public function getSpouses(string $knownEntityName, string $knownEntity, string $neededEntity): array
    {
        if (-1 === $knownEntityId = $this->getKnownEntityId($knownEntityName, $knownEntity)) {
            return [];
        }
        $neededEntityIds = $this->getNeededEntityIds($knownEntityId, $knownEntity, $neededEntity);
        $neededEntities = $this->getNeededEntities($neededEntityIds, $neededEntity);

        return $neededEntities;
    }

    public function getEntity(string $entity): array
    {
        $tableName = $this->getEntityTableName($entity);
        $sql = "SELECT * FROM " . $tableName . " LIMIT 300";
        $statement = $this->connection->prepare($sql);
        $querySuccess = $statement->execute();
        if (!$querySuccess) {
            return [];
        }
        return $statement->fetchAll();
    }
}