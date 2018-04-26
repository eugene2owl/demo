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

    private function processOutput(?string $output, RegExer $regularExpresser): string
    {
        if (empty($output)) {
            return "";
        }
        $output = $regularExpresser->wrapUpInSpan(
            $output,
            self::STRING_REGEX,
            "color: " . self::STRING_COLOR
        );
        return $output;
    }

    public function processCodes(array $codes): array
    {
        $regExer = new RegExer();
        foreach ($codes as $code => $attachments) {
            $codes[$code]["coloredCode"] = $this->processCode($code, $regExer);
            $codes[$code]["output"] = $this->processOutput($attachments["output"], $regExer);
        }
        return $codes;
    }
}