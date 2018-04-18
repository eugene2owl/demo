<?php

declare(strict_types = 1);

namespace Demo\Service;


class RegularExpresser
{
    private const FUNCTION_REGEX = "/[a-zA-Z0-9_]+\(/";

    private function getEntranceArray(string $text, string $patternRegex): array
    {
        $entranceArray = [];
        preg_match_all(
            $patternRegex,
            $text,
            $matchArray,
            PREG_OFFSET_CAPTURE
        );
        foreach ($matchArray[0] as $key => $secondLevelArray) {
            if ($patternRegex !== self::FUNCTION_REGEX) {
                $entranceArray[$key]["length"] = strlen($secondLevelArray[0]);
            } else {
                $entranceArray[$key]["length"] = strlen($secondLevelArray[0]) - 1;
            }
            $entranceArray[$key]["position"] = $secondLevelArray[1];
        }
        return $entranceArray;
    }

    private function insertPatternInString(string $string, string $pattern, int $position): string
    {
        $string = substr_replace($string, $pattern, $position, 0);
        return $string;
    }

    public function wrapUpInSpan(string $text, string $regexPattern, string $style): string
    {
        $positions = $this->getEntranceArray($text, $regexPattern);
        for ($entranceNumber = count($positions) - 1; $entranceNumber > -1; $entranceNumber--) {
            $text = $this->insertPatternInString(
                $text,
                "</span>",
                $positions[$entranceNumber]["position"] + $positions[$entranceNumber]["length"]
            );
            $text = $this->insertPatternInString(
                $text,
                "<span style='$style'>",
                $positions[$entranceNumber]["position"]
            );
        }
        return $text;
    }

    public function wrapUpInA(string $text, string $regexPattern, string $href): string
    {
        $positions = $this->getEntranceArray($text, $regexPattern);
        for ($entranceNumber = count($positions) - 1; $entranceNumber > -1; $entranceNumber--) {
            $text = $this->insertPatternInString(
                $text,
                "</a>",
                $positions[$entranceNumber]["position"] + $positions[$entranceNumber]["length"]
            );
            $text = $this->insertPatternInString(
                $text,
                "<a href='$href'>",
                $positions[$entranceNumber]["position"]
            );
        }
        return $text;
    }
}