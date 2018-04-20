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

    public function getContentsFromPage(string $pageName): array
    {
        $contents = [
            "titles"      => $this->repository->getSpouses($pageName, "page", "title"),
            "articles"    => $this->repository->getSpouses($pageName, "page", "article"),
            "images"      => $this->repository->getSpouses($pageName, "page", "image"),
            "links"       => $this->repository->getSpouses($pageName, "page", "link"),
            "codes"       => $this->repository->getSpouses($pageName, "page", "code"),
            "lists"       => $this->repository->getSpouses($pageName, "page", "list"),
        ];
        return $contents;
    }

    public function getSpouse(string $knownEntityName, string $knownEntity, string $neededEntity): array
    {
        return $this->repository->getSpouses($knownEntityName, $knownEntity, $neededEntity);
    }

    public function getEntity(string $entity): array
    {
        return $this->repository->getEntity($entity);
    }

    public function getPageTitleCouples(): array
    {
        $pages = $this->repository->getEntity("page");
        $titles = [];
        foreach ($pages as $number => $page) {
            $titles[] = $this->getSpouse($page["name"], "page", "title");
        }
        $pageTitleCouples = [];
        foreach ($titles as $number => $title) {
            $pageTitleCouples[$number]["page"] = $pages[$number]["name"];
            $pageTitleCouples[$number]["title"] = $title[0]["name"];
        }
        return $pageTitleCouples;
    }

    public function getLinksAssociationsFromPage(string $pageName): array
    {
        $links = $this->getSpouse($pageName, "page", "link");
        $linkToSourceCouples = [];
        foreach ($links as $number => $link) {
            $linkToSourceCouples[$link["text"]] = $link["name"];
        }
        return $linkToSourceCouples;
    }

    public function getCodesWithAttachmentsFromPage(string $pageName): array
    {
        $codes = $this->getCodesFromPage($pageName);
        foreach ($codes as $number => $code) {
            $codes[$number] = $this->getCodeWithAttachments($code);
        }
        return $codes;
    }

    private function getCodesFromPage(string $pageName): array
    {
        return $this->repository->getSpouses($pageName, "page", "code");
    }

    private function getCodeWithAttachments(array $code): array
    {
        $codeArticles = $this->repository->getSpouses($code["name"], "code", "article");
        $codeLists = $this->repository->getSpouses($code["name"], "code", "list");
        return [
            "code"        => $code,
            "articles"    => $codeArticles,
            "lists"        => $codeLists,
        ];
    }
}