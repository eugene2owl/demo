<?php

namespace Demo\Service;

require_once "../repository/Contents.php";

use Demo\Repository\Contents as ContentsRepo;

class Contents
{
    public function getContents(string $page): array
    {
        $contentsRepo = new ContentsRepo();
        $contents = [
            "titles" => $contentsRepo->getEntityArrayOnPage("titles", $page),
            "articles" => $contentsRepo->getEntityArrayOnPage("articles", $page),
            "images" => $contentsRepo->getEntityArrayOnPage("images", $page),
            "links" => $contentsRepo->getEntityArrayOnPage("links", $page),
            "codes" => $contentsRepo->getEntityArrayOnPage("codes", $page),
        ];
        return $contents;
    }

    public function getEntityArray(string $name)
    {
        $contentsRepo = new ContentsRepo();
        return $contentsRepo->getEntityArray($name);
    }
}