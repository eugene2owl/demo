<?php

declare(strict_types = 1);

namespace Demo\Repository;

use Demo\Service\myPDO;

class AdminEdit
{
    private $connection;

    public function __construct()
    {
        $this->connection = myPDO::getConnection();
    }

    private function getArticlesByCodePatternQuery(): string
    {
        return <<< SQL
SELECT article.name, substr(code.name, 1, 15) FROM (articles article, codes code)
   JOIN article_code_relation article_code
     ON article_code.article_id = article.id AND article_code.code_id = code.id AND code.name LIKE :codePattern
   JOIN pages page
     ON page.name = :pageName
   JOIN code_page_relation code_page
     ON code_page.page_id =  page.id AND code_page.code_id = code.id
ORDER BY article.name ASC;
SQL;
    }

    public function getArticlesByCodePattern(string $codePattern, string $pageName): array
    {
        $sql = $this->getArticlesByCodePatternQuery();
        $statement = $this->connection->prepare($sql);
        $success = $statement->execute([
            "codePattern" => $codePattern,
            "pageName"    => $pageName,
        ]);
        $articles = [];
        if ($success) {
            foreach ($statement->fetchAll() as $row) {
                $articles[] = $row["name"];
            }
        }
        return $articles;
    }

    private function getArticlesIdOfCodeOnPageQuery(): string
    {
        return <<< SQL
SELECT article.id FROM (articles article, codes code)
   JOIN article_code_relation article_code
     ON article_code.article_id = article.id AND article_code.code_id = code.id AND code.name LIKE :codePattern
   JOIN pages page
     ON page.name = :pageName
   JOIN code_page_relation code_page
     ON code_page.page_id =  page.id AND code_page.code_id = code.id
ORDER BY article.name ASC;
SQL;

    }

    private function getArticlesIdsOfCodeOnPage(string $codePattern, string $pageName): array
    {
        $sql = $this->getArticlesIdOfCodeOnPageQuery();
        $statement = $this->connection->prepare($sql);
        $success = $statement->execute([
            "codePattern" => $codePattern,
            "pageName"    => $pageName,
        ]);
        $articlesIds = [];
        if ($success) {
            foreach ($statement->fetchAll() as $row) {
                $articlesIds[] = (int)$row["id"];
            }
        }
        return $articlesIds;
    }

    private function updateArticleQuery(): string
    {
        return <<< SQL
UPDATE articles article
SET article.name = :article
WHERE article.id = :articleId;
SQL;
    }

    private function updateArticle(string $article, int $articleId): bool
    {
        $sql = $this->updateArticleQuery();
        $statement = $this->connection->prepare($sql);
        $success = $statement->execute([
            "article"     => $article,
            "articleId"   => $articleId
        ]);
        return $success;
    }

    private function deleteArticleQuery(): string
    {
        return <<< SQL
DELETE article FROM articles article
WHERE article.id = :articleId;
SQL;
    }

    private function deleteArticle(int $articleId): bool
    {
        $sql = $this->deleteArticleQuery();
        $statement = $this->connection->prepare($sql);
        $success = $statement->execute([
            "articleId"   => $articleId
        ]);
        return $success;
    }

    private function updateArticles(array $articles, array $articlesIds): bool
    {
        if (count($articles) != count($articlesIds)) {
            return false;
        }
        foreach ($articles as $number => $article) {
            if (!empty($article)) {
                if (!$this->updateArticle($article, $articlesIds[$number])) {
                    return false;
                }
            } else {
                if (!$this->deleteArticle($articlesIds[$number])) {
                    return false;
                }
            }
        }
        return true;
    }

    private function deleteCodeByNameQuery(): string
    {
        return <<< SQL
DELETE code FROM codes code WHERE code.name = :codeName;
SQL;

    }

    private function deleteCodeByName(string $code): bool
    {
        $sql = $this->deleteCodeByNameQuery();
        $statement = $this->connection->prepare($sql);
        $success = $statement->execute([
            "codeName"   => $code
        ]);
        return $success;
    }

    public function updateArticlesOfCode(string $code, array $articles, string $pageName, bool $deleteCode): bool
    {
        if ($deleteCode) {
            $this->deleteCodeByName($code);
        }
        $articlesIds = $this->getArticlesIdsOfCodeOnPage($code, $pageName);
        return $this->updateArticles($articles, $articlesIds);
    }
}