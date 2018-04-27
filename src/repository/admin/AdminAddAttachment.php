<?php

declare(strict_types = 1);

namespace Demo\Repository;

use Demo\Service\myPDO;

class AdminAddAttachment
{
    private $connection;

    public function __construct()
    {
        $this->connection = myPDO::getConnection();
    }

    public function attachArticleToCode(string $code, string $article): bool
    {
        if (!$this->insertArticle($article)) {
            return false;
        }
        if (($codeId = $this->getCodeId($code)) === -1) {
            return false;
        }
        if (($articleId = $this->getArticleId($article)) === -1) {
            return false;
        }
        if (!$this->connectArticleAndCode($articleId, $codeId)) {
            return false;
        }
        return true;
    }

    private function getConnectCodeAndPageQuery(): string
    {
        return <<< SQL
INSERT INTO article_code_relation
(code_id, article_id) VALUES (:codeId, :articleId);        
SQL;
    }

    private function connectArticleAndCode(int $articleId, int $codeId): bool
    {
        $sql = $this->getConnectCodeAndPageQuery();
        $statement = $this->connection->prepare($sql);
        $success = $statement->execute([
            "codeId"   => $codeId,
            "articleId"   => $articleId
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

    private function getArticleIdQuery(): string
    {
        return <<< SQL
SELECT id FROM articles
WHERE name = :articleName;
SQL;
    }

    public function getArticleId(string $articleName): int
    {
        $sql = $this->getArticleIdQuery();
        $statement = $this->connection->prepare($sql);
        $success = $statement->execute([
            "articleName" => $articleName
        ]);
        return $success ? (int)$statement->fetch()["id"] : -1;
    }

    private function getInsertArticleQuery(): string
    {
        return <<< SQL
INSERT INTO articles
(name) VALUES (:articleName);
SQL;
    }

    private function insertArticle(string $article): bool
    {
        $sql = $this->getInsertArticleQuery();
        $statement = $this->connection->prepare($sql);
        $success = $statement->execute([
            "articleName" => $article,
        ]);
        return $success;
    }

    private function getCodeByPatternQuery(): string
    {
        return <<< SQL
SELECT code.name
   FROM (pages page, codes code)
   JOIN code_page_relation code_page
     ON code_page.code_id = code.id AND
     code_page.page_id = page.id AND
     code.name LIKE :pattern AND
     page.name = :pageName;
SQL;
    }

    public function getCodeByPattern(string $pattern, string $pageName): string
    {
        $sql = $this->getCodeByPatternQuery();
        $statement = $this->connection->prepare($sql);
        $success = $statement->execute([
            "pattern" => "%$pattern%",
            "pageName"  => $pageName,
        ]);
        return $success ? (string)$statement->fetch()["name"] : "";
    }
}