<?php

declare(strict_types = 1);

namespace Demo\Service;

class StyleChanger
{
    public function getRadiobuttonValues(): array
    {
        return ["1", "2"];
    }

    public function getStyleMode(?string $post): string
    {
        return isset($post) ? (string)intval($post) : "1";
    }
}