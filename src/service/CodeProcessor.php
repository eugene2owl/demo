<?php

declare(strict_types = 1);

namespace Demo\Service;

require_once "RegularExpresser.php";

use Demo\Service\RegularExpresser as RegExer;

class CodeProcessor
{
    private const STRING_COLOR = "#FF3537";
    private const FUNCTION_COLOR = "#2B50DE";

    private const STRING_REGEX = "/((\".*\")|('.*'))/";
    private const FUNCTION_REGEX = "/[a-zA-Z0-9_]+\(/";

    private function processCode(string $code, RegExer $regularExpresser): string
    {
        $code = $regularExpresser->wrapUpInSpan(
            $code,
            self::STRING_REGEX,
            "color: " . self::STRING_COLOR
        );
        $code = $regularExpresser->wrapUpInSpan(
            $code,
            self::FUNCTION_REGEX,
            "color: " . self::FUNCTION_COLOR
        );
        return $code;
    }

    private function processOutput(string $output, RegExer $regularExpresser):string
    {
        $output = $regularExpresser->wrapUpInSpan(
            $output,
            self::STRING_REGEX,
            "color: #FF3537"
        );
        return $output;
    }

    public function processCodes(array $codes): array
    {
        $regExer = new RegExer();
        foreach ($codes as $number => $code) {
            $codes[$number]["code"] = $this->processCode($code["code"], $regExer);
            $codes[$number]["output"] = $this->processOutput($code["output"], $regExer);
        }
        return $codes;
    }
}