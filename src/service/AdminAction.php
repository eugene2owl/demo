<?php

declare(strict_types = 1);

namespace Demo\Service;

require_once "../repository/admin/AdminAddCode.php";
require_once "../repository/admin/AdminAddAttachment.php";
require_once "../repository/admin/AdminEdit.php";

use Demo\Repository\AdminAddCode;
use Demo\Repository\AdminAddAttachment;
use Demo\Repository\AdminEdit;

class AdminAction
{
    private $repositoryAddingCode;
    private $repositoryAddingAttachment;
    private $repositoryEditing;

    public function __construct()
    {
        $this->repositoryAddingCode = new AdminAddCode();
        $this->repositoryAddingAttachment = new AdminAddAttachment();
        $this->repositoryEditing = new AdminEdit();
    }

    public function addCodeToAdminPage(string $code, string $output, string $pageName): bool
    {
        return $this->repositoryAddingCode->addCodeToAdminPage($code, $output, $pageName);
    }

    public function getCodeByPattern(string $pattern, string $pageName): string
    {
        return $this->repositoryAddingAttachment->getCodeByPattern($pattern, $pageName);
    }

    public function attachArticleToCode(string $code, string $article): bool
    {
        return $this->repositoryAddingAttachment->attachArticleToCode($code, $article);
    }

    public function getArticlesByCodePattern(string $codePattern, string $pageName): array
    {
        return $this->repositoryEditing->getArticlesByCodePattern($codePattern, $pageName);
    }

    public function updateArticlesOfCode(string $code, array $articles, string $pageName, bool $deleteCode): bool
    {
        return $this->repositoryEditing->updateArticlesOfCode($code, $articles, $pageName, $deleteCode);
    }
}