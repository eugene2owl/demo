<?php

declare(strict_types = 1);

namespace Demo\Service;

require_once "../repository/admin/AdminAddCode.php";
require_once "../repository/admin/AdminAddAttachment.php";

use Demo\Repository\AdminAddCode;
use Demo\Repository\AdminAddAttachment;

class AdminAction
{
    private $repositoryAddingCode;
    private $repositoryAddingAttachment;

    public function __construct()
    {
        $this->repositoryAddingCode = new AdminAddCode();
        $this->repositoryAddingAttachment = new AdminAddAttachment();
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
}