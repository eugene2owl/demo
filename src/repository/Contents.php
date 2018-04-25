<?php

declare(strict_types = 1);

namespace Demo\Repository;

require_once "../service/MyPDO.php";

use Demo\Service\myPDO;

class Contents
{
    private const SCHEMA_NAME = "demo";
    private const ENTITY_TABLE_POSTFIX = "s";
    private const BRIDGE = "_";
    private const RELATION_TABLE_POSTFIX = "_relation";
    private $connection;

    private $additionalFields = [
        "code" => ["output"],
        "link" => ["text"],
    ];

    public function __construct()
    {
        $this->connection = myPDO::getConnection();
    }

    private function getEntityTableName(string $entityName, string $schema = self::SCHEMA_NAME): string
    {
        return $entityName . self::ENTITY_TABLE_POSTFIX;
    }

    private function getRelationTableName(string $firstEntityName, string $secondEntityName, string $schema = self::SCHEMA_NAME): string
    {
        return $firstEntityName . self::BRIDGE . $secondEntityName . self::RELATION_TABLE_POSTFIX;
    }

    private function getCurrentAdditionalFields(string $entityName): array
    {
        return (array)$this->additionalFields[$entityName];
    }

    private function getSpousesQuery(string $knownEntity, string $neededEntity): string
    {
        $knownEntityTable  = $this->getEntityTableName($knownEntity);
        $neededEntityTable = $this->getEntityTableName($neededEntity);
        $relationTable     = $this->getRelationTableName($neededEntity, $knownEntity);

        $currentAdditionalFields = implode(", many.", $this->getCurrentAdditionalFields($neededEntity));
        if (!empty($currentAdditionalFields)) {
            $currentAdditionalFields = ", many." . $currentAdditionalFields;
        }

        $sql = "
            SELECT many.name $currentAdditionalFields
            FROM $knownEntityTable one
              JOIN $relationTable many_one
                ON (one.id = many_one.$knownEntity" . "_id AND one.name = :knownEntityName)
              JOIN $neededEntityTable many
                ON (many.id = many_one.$neededEntity" . "_id)
                ";
        return $sql;
    }

    public function getSpouses(string $knownEntityName, string $knownEntity, string $neededEntity): array
    {
        $sql = $this->getSpousesQuery($knownEntity, $neededEntity);
        $statement = $this->connection->prepare($sql);
        $statement->execute([
            "knownEntityName" => $knownEntityName,
        ]);
        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }

    private function getEntityQuery(string $entity): string
    {
        $entityTable = $this->getEntityTableName($entity);
        $currentAdditionalFields = implode("`, `", $this->getCurrentAdditionalFields($entity));
        if (!empty($currentAdditionalFields)) {
            $currentAdditionalFields = "`" . $currentAdditionalFields . "`";
        }
        return "SELECT `name`, $currentAdditionalFields FROM $entityTable LIMIT 300";
    }

    public function getEntity(string $entity): array
    {
        $sql = $this->getEntityQuery($entity);
        $statement = $this->connection->prepare($sql);
        $statement->execute();
        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }

    private function getPageTitleCouplesQuery(): string
    {
        return <<< SQL
SELECT page.name, title.name
FROM pages page
  JOIN title_page_relation title_page
    ON (page.id = title_page.page_id)
  JOIN titles title
    ON (title.id = title_page.title_id);
SQL;
    }

    public function getPageTitleCouples(): array
    {
        $sql = $this->getPageTitleCouplesQuery();
        $statement = $this->connection->prepare($sql);
        $statement->execute();
        return $statement->fetchAll(\PDO::FETCH_NUM);
    }

    private function getListsWithElementsFromPageQuery(): string
    {
        return <<< SQL
SELECT list.name, element.name
FROM pages page
    JOIN list_page_relation list_page
      ON (page.id = list_page.page_id AND page.name = :pageName)
    JOIN lists list
      ON (list.id = list_page.list_id)
    JOIN element_list_relation element_list
      ON (list.id = element_list.list_id AND list.id = list_page.list_id)
    JOIN elements element
      ON (element.id = element_list.element_id)
ORDER BY list.id;
SQL;
    }

    public function getListsWithElementsFromPage(string $pageName): array
    {
        $sql = $this->getListsWithElementsFromPageQuery();
        $statement = $this->connection->prepare($sql);
        $statement->execute([
            "pageName" => $pageName
        ]);
        return $statement->fetchAll(\PDO::FETCH_NUM);
    }

    private function getCodesWithAttachmentsFromPageQuery(): string
    {
        return <<< SQL
SELECT code.name, article.name, list.name, element.name
FROM codes code

  JOIN code_page_relation code_page
    ON (code.id = code_page.code_id)
  JOIN pages page
    ON (page.id = code_page.page_id AND page.name = :pageName)

  LEFT JOIN article_code_relation article_code
    ON (code.id = article_code.code_id)
  LEFT JOIN articles article
    ON (article.id = article_code.article_id)

  LEFT JOIN list_code_relation list_code
    ON (code.id = list_code.code_id)
  LEFT JOIN lists list
    ON (list.id = list_code.list_id)

  LEFT JOIN element_list_relation element_list
    ON (element_list.list_id = list.id)
  LEFT JOIN elements element
    ON (element_list.element_id = element.id);
SQL;
    }

    public function getCodesWithAttachmentsFromPage(string $pageName): array
    {
        $sql = $this->getCodesWithAttachmentsFromPageQuery();
        $statement = $this->connection->prepare($sql);
        $statement->execute([
            "pageName" => $pageName,
        ]);
        return $statement->fetchAll(\PDO::FETCH_NUM);
    }
}