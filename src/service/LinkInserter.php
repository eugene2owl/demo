<?php

declare(strict_types = 1);

namespace Demo\Service;

require_once "RegularExpresser.php";

use Demo\Service\RegularExpresser as RegExer;

class LinkInserter
{
    public function insertLink(string $text, string $pattern, string $href): string
    {
        $regExer = new RegExer();
        $text = $regExer->wrapUpInA($text, "/$pattern/", $href);
        return $text;
    }

    public function insertLinksIntoArticles(array $links, array $articles): array
    {
        foreach ($links as $pattern => $href) {
            foreach ($articles as $number => $article) {
                $articles[$number]["article"] = $this->insertLink(
                    $article["article"],
                    $pattern,
                    $href
                );
            }
        }
        return $articles;
    }
}