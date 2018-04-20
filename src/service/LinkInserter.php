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

    public function insertLinksIntoTexts(array $links, array $texts): array
    {
        foreach ($links as $pattern => $href) {
            foreach ($texts as $number => $text) {
                $texts[$number]["name"] = $this->insertLink(
                    $text["name"],
                    $pattern,
                    $href
                );
            }
        }
        return $texts;
    }
}