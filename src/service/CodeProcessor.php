<?php

declare(strict_types = 1);

namespace Demo\Service;

class CodeProcessor
{
    private const STRING_COLOR = "#FF3537";
    private const FUNCTION_COLOR = "#2B50DE";
    private const VARIABLE_COLOR = "#E7A803";

    private const STRING_REGEX = "/((\".*\")|('.*'))/";
    private const FUNCTION_REGEX = "/[a-zA-Z0-9_]+\(/";
    private const VARIABLE_REGEX = "/\$[a-zA-Z0-9_]+/";

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

    private function wrapUpInSpan(string $text, string $regexPattern, string $style): string
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

    private function processCode(string $code): string
    {
        $code = $this->wrapUpInSpan(
            $code,
            self::STRING_REGEX,
            "color: " . self::STRING_COLOR
        );
        $code = $this->wrapUpInSpan(
            $code,
            self::VARIABLE_REGEX,
            "color: " . self::VARIABLE_REGEX
        );
        $code = $this->wrapUpInSpan(
            $code,
            self::FUNCTION_REGEX,
            "color: " . self::FUNCTION_COLOR
        );
        return $code;
    }

    private function processOutput(string $output):string
    {
        $output = $this->wrapUpInSpan(
            $output,
            self::STRING_REGEX,
            "color: #FF3537"
        );
        return $output;
    }

    public function processCodes(array $codes): array
    {
        foreach ($codes as $number => $code) {
            $codes[$number]["code"] = $this->processCode($code["code"]);
            $codes[$number]["output"] = $this->processOutput($code["output"]);
        }
        return $codes;
    }
}