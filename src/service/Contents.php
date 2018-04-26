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
            "titles"      => $this->getSpouses($pageName, "page", "title"),
            "articles"    => $this->getSpouses($pageName, "page", "article"),
            "images"      => $this->getSpouses($pageName, "page", "image"),
            "links"       => $this->getSpouses($pageName, "page", "link"),
            "codes"       => $this->getCodesWithAttachmentsFromPage($pageName),
            "lists"       => $this->getListsWithElementsFromPage($pageName),
        ];
        return $contents;
    }

    private function getCodesWithAttachmentsFromPage(string $pageName): array
    {
        return $this->formatCodesWithAttachmentsFromPage(
            $this->repository->getCodesWithAttachmentsFromPage($pageName)
        );
    }

    private function getListsWithElementsFromPage(string $pageName): array
    {
        return $this->formatListsWithElementsFromPage(
            $this->repository->getListsWithElementsFromPage($pageName)
        );
    }

    private function isCodePoor(array $attachments): bool
    {
        $isPoor = false;
        foreach ($attachments as $number => $attachment) {
            $isPoor = ($number > 0 && empty($attachment)) ? true : $isPoor;
        }
        return $isPoor;
    }

    private function formatCodesWithAttachmentsFromPage(array $queryResult): array
    {
        $formattedArray = [];
        foreach ($queryResult as $number => $attachments) {
            if ($this->isCodePoor($attachments)) {
                $formattedArray[$attachments[0]] = null;
            }
            if (!empty($attachments[4])) {
                $formattedArray[$attachments[0]]["output"] = $attachments[4];
            }
            if (!empty($attachments[1])) {
                $formattedArray[$attachments[0]][] = $attachments[1];
            }
            if (!empty($attachments[3])) {
                $formattedArray[$attachments[0]][$attachments[2]][] = $attachments[3];
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

    public function getSpouses(string $knownEntityName, string $knownEntity, string $neededEntity): array
    {
        return $this->repository->getSpouses($knownEntityName, $knownEntity, $neededEntity);
    }

    public function getEntity(string $entity): array
    {
        return $this->repository->getEntity($entity);
    }

    public function getPageTitleCouples(): array
    {
        return $this->formatPageTitleCouples(
            $this->repository->getPageTitleCouples()
        );
    }

    public function searchPagesByPattern(?string $pattern): array
    {
        return $this->formatPageTitleCouples(
            $this->repository->getPagesTitleCouplesByPattern($pattern)
        );
    }

    private function formatPageTitleCouples(array $queryResult): array
    {
        $formattedArray = [];
        foreach ($queryResult as $number => $couple) {
            if (!isset($formattedArray[$couple[0]])) {
                $formattedArray[$couple[0]] = $couple[1];
            }
        }
        return $formattedArray;
    }

    public function getLinksAssociationsFromPage(string $pageName): array
    {
        $links = $this->getSpouses($pageName, "page", "link");
        $linkToSourceCouples = [];
        foreach ($links as $number => $link) {
            $linkToSourceCouples[$link["text"]] = $link["name"];
        }
        return $linkToSourceCouples;
    }
}