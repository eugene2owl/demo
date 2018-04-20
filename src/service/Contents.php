<?php

declare(strict_types = 1);

namespace Demo\Service;

require_once "../repository/Contents.php";

use Demo\Repository\Contents as ContentsRepo;

class Contents
{
    private $repository;

    public function __construct()
    {
        $this->repository = new ContentsRepo();
    }

    public function getContents(string $page): array
    {
        $contents = [
            "titles"      => $this->repository->getEntityArrayOnPage("titles", $page),
            "articles"    => $this->repository->getEntityArrayOnPage("articles", $page),
            "images"      => $this->repository->getEntityArrayOnPage("images", $page),
            "links"       => $this->repository->getEntityArrayOnPage("links", $page),
            "codes"       => $this->repository->getEntityArrayOnPage("codes", $page),
            "lists"       => $this->repository->getEntityArrayOnPage("lists", $page),
        ];
        return $contents;
    }

    public function getEntityArray(string $name): array
    {
        return $this->repository->getEntityArray($name);
    }

    public function getEntityFromPage(string $pageName, string $entityName): array
    {
        return $this->repository->getEntityFromPage($pageName, $entityName);
    }
}