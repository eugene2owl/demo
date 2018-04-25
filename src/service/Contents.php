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
            "codes"       => $this->formatCodesWithAttachmentsFromPage(
                $this->repository->getCodesWithAttachmentsFromPage($pageName)
            ),
            "lists"       => $this->formatListsWithElementsFromPage(
                $this->repository->getListsWithElementsFromPage($pageName)
            ),
            // list или на странице или у кода
            // задача написать две штуки
        ];
        return $contents;
    }

    private function formatCodesWithAttachmentsFromPage(array $queryResult): array
    {
        $formattedArray = [];
        foreach ($queryResult as $number => $attachments) {
            if (!empty($attachments[1])) {
                $formattedArray[$attachments[0]][] = $attachments[1];//абзац
            }
            if (!empty($attachments[3])) {
                $formattedArray[$attachments[0]][$attachments[2]][] = $attachments[3]; //имя списка
            }
        }
        return $formattedArray;
    }

    private function formatListsWithElementsFromPage(array $queryResult): array
    {
        $formattedArray = [];
        foreach ($queryResult as $number => $list_element) {
            $formattedArray[$list_element[0]][] = $list_element[1];
        }
        return $formattedArray;
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
        return $this->repository->getPageTitleCouples();
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
}