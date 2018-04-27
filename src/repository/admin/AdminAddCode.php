<?php

declare(strict_types = 1);

namespace Demo\Repository;

use Demo\Service\myPDO;

class AdminAddCode
{
    private $connection;

    public function __construct()
    {
        $this->connection = myPDO::getConnection();
    }

    public function addCodeToAdminPage(string $code, string $output, string $pageName): bool
    {
        if (!$this->insertCode($code, $output)) {
            return false;
        }
        if (($pageId = $this->getPageId($pageName)) === -1) {
            return false;
        }
        if (($codeId = $this->getCodeId($code)) === -1) {
            return false;
        }
        if (!$this->connectCodeAndPage($codeId, $pageId)) {
            return false;
        }
        return true;
    }

    private function getConnectCodeAndPageQuery(): string
    {
        return <<< SQL
INSERT INTO code_page_relation
(code_id, page_id) VALUES (:codeId, :pageId);        
SQL;
    }

    private function connectCodeAndPage(int $codeId, int $pageId): bool
    {
        $sql = $this->getConnectCodeAndPageQuery();
        $statement = $this->connection->prepare($sql);
        $success = $statement->execute([
            "codeId" => $codeId,
            "pageId"   => $pageId
        ]);
        return $success;
    }

    private function getCodeIdQuery(): string
    {
        return <<< SQL
SELECT id FROM codes
WHERE name = :codeName;
SQL;
    }

    private function getCodeId(string $codeName): int
    {
        $sql = $this->getCodeIdQuery();
        $statement = $this->connection->prepare($sql);
        $success = $statement->execute([
            "codeName" => $codeName
        ]);
        return $success ? (int)$statement->fetch()["id"] : -1;
    }

    private function getPageIdQuery(): string
    {
        return <<< SQL
SELECT id FROM pages
WHERE name = :pageName;
SQL;
    }

    public function getPageId(string $pageName): int
    {
        $sql = $this->getPageIdQuery();
        $statement = $this->connection->prepare($sql);
        $success = $statement->execute([
            "pageName" => $pageName
        ]);
        return $success ? (int)$statement->fetch()["id"] : -1;
    }

    private function getInsertCodeQuery(): string
    {
        return <<< SQL
INSERT INTO codes
(name, output) VALUES (:codeName, :output);
SQL;
    }

    private function insertCode(string $code, string $output): bool
    {
        $sql = $this->getInsertCodeQuery();
        $statement = $this->connection->prepare($sql);
        $success = $statement->execute([
            "codeName" => $code,
            "output"   => $output
        ]);
        return $success;
    }
}