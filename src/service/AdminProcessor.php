<?php

declare(strict_types = 1);

namespace Demo\Service;

require_once "../controller/const.php";
require_once "../../vendor/autoload.php";
require_once "Contents.php";
require_once "CodeProcessor.php";

use Demo\Service\Contents as ContentsService;
use Demo\Service\CodeProcessor;

class AdminProcessor
{
    private $pageName;

    private const CODE_MIN_LENGHT = 5;
    private const ARTICLE_MIN_LENGHT = 5;
    private const PATTERN_MIN_LENGHT = 1;

    public function __construct(string $pageName = "admin.php")
    {
        $this->pageName = $pageName;
    }

    private function getUsualPage(): array
    {
        $tunnelToDB = new ContentsService();
        $pageContents = $tunnelToDB->getContentsFromPage($this->pageName);

        $codeProcessor = new CodeProcessor();
        $codes = $codeProcessor->processCodes($pageContents["codes"]);

        return [
            "admin.tpl.twig",
            [
                "title"      => $pageContents["titles"][0]["name"],
                "header"     => $pageContents["titles"][0]["name"],
                "codes"      => $codes,
            ],
        ];
    }

    private function getAvailableMainModes(): array
    {
        return [
            1 => "add",
            2 => "edit",
        ];
    }

    private function getAvailableAddingModes(): array
    {
        return [
            1 => "add code",
            2 => "add attachment",
        ];
    }

    private function getCurrentMainMode(?string $inputedMainMode): int
    {
        if (in_array(
            (int)$inputedMainMode,
            array_keys($this->getAvailableMainModes())
        )) {
            return (int)$inputedMainMode;
        }
        return 1;
    }

    private function getCurrentAddingMode(?string $inputedAddingMode): int
    {
        if (in_array(
            (int)$inputedAddingMode,
            array_keys($this->getAvailableAddingModes())
        )) {
            return (int)$inputedAddingMode;
        }
        return 1;
    }

    private function getCurrentAddingCode(?string $inputedcode): string
    {
        if (
            is_string($inputedcode) &&
            strlen(trim($inputedcode)) > self::CODE_MIN_LENGHT - 1
        ) {
            return filter_var($inputedcode, FILTER_SANITIZE_STRING);
        }
        return "";
    }

    private function getCurrentAddingOutput(?string $inputedOutput): string
    {
        if (is_string($inputedOutput)) {
            return filter_var($inputedOutput, FILTER_SANITIZE_STRING);
        }
        return "";
    }

    private function wasActionSubmited(?string $buttonName): bool
    {
        return isset($buttonName);
    }

    private function getCurrentAttachedArticle(?string $inputedArticle): string
    {
        if (
            is_string($inputedArticle) &&
            strlen(trim($inputedArticle)) > self::ARTICLE_MIN_LENGHT - 1
        ) {
            return filter_var($inputedArticle, FILTER_SANITIZE_STRING);
        }
        return "";
    }

    private function getCurrentSearchingCode(?string $inputedPattern): string
    {
        if (
            is_string($inputedPattern) &&
            strlen(trim($inputedPattern)) > self::PATTERN_MIN_LENGHT - 1
        ) {
            return filter_var($inputedPattern, FILTER_SANITIZE_STRING);   // query to DB
        }
        return "";
    }

    private function showJSMessage(string $message): void
    {
        echo "<script> alert('$message') </script>";
    }

    private function showUserSubmitWarnings(?string $inputedArticle, ?string $inputedCodePattern): bool ///// the same for crating TO DO
    {
        if (empty($this->getCurrentAttachedArticle($inputedArticle))) {
            $messageList[] = "Article is not valid.";
        }
        if (empty($this->getCurrentSearchingCode($inputedCodePattern))) {
            $messageList[] = "Code is not valid.";
        }
        if (!empty($messageList)) {
            $this->showJSMessage(implode("\\n", $messageList));
            return false;
        }
        return true;
    }

    private function showUserSearchWarnings(?string $inputedCodePattern): bool
    {
        if (empty($this->getCurrentSearchingCode($inputedCodePattern))) {
            $this->showJSMessage("No code found.");
            return false;
        }
        return true;
    }

    private function getSubmitAvailabilityToAttach(?string $searchClick, ?string $inputedArticle, ?string $inputedCodePattern): bool // not code pattern but founded code
    {
        if (!isset($searchClick)) {
            return false;
        }
        if (empty($this->getCurrentAttachedArticle($inputedArticle))) {
            return false;
        }
        if (empty($this->getCurrentSearchingCode($inputedCodePattern))) {
            return false;
        }
        return true;
    }

    private function addCodeToDataBase(string $code, string $output): bool
    {
        $this->showJSMessage("$code ; $output");
        return true;
    }

    private function addAttachmentToDataBase(string $code, string $article): bool
    {
        $this->showJSMessage("$code ; $article");
        return true;
    }

    private function wasCodeCreated(?string $currentAddingCode, ?string $cancelButtonName): bool
    {
        return (
            !empty($currentAddingCode) &&
            !$this->wasActionSubmited($cancelButtonName)
        );
    }

    private function tryAddCodeToDataBase(
        bool $wasCodeCreated,
        string $currentAddingCode,
        string $currentAddingOutput,
        ?string $cancelButtonName
    ): void
    {
        if ($wasCodeCreated) {
            if ($this->addCodeToDataBase($currentAddingCode, $currentAddingOutput)) {
                $this->showJSMessage("Code was added.");
            } else {
                $this->showJSMessage("Code WAS NOT added because of DB inner problems.");
            }
        }
        if (!$wasCodeCreated && !$this->wasActionSubmited($cancelButtonName) && $this->isAdminDevState()) {
            $this->showJSMessage("Code was not created because of invalid output.");
        }
    }

    private function wasArticleAttached(
        ?string $currentArticle,
        ?string $currentSearchingCode,
        ?string $cancelButtonName
    ): bool
    {
        return (
            !empty($currentArticle) &&
            !empty($currentSearchingCode) &&
            $this->wasActionSubmited($cancelButtonName)
        );
    }

    private function tryAddAttachmentToDataBase(
        bool $wasArticleAttached,
        string $currentSearchingCode,
        string $currentArticle
    ): void
    {
        if ($wasArticleAttached) {
            if ($this->addAttachmentToDataBase($currentSearchingCode, $currentArticle)) {
                $this->showJSMessage("Attached to DB!");
            } else {
                $this->showJSMessage("NOT attached to DB");
            }
        }
        if (!$wasArticleAttached && $this->isAdminDevState()) {
            $this->showJSMessage("NOT attached because of invalid input.");
        }
    }

    private function showPossibleWarnings(
        ?string $article_attachment_submit,
        ?string $attaching_article,
        ?string $code_pattern,
        ?string $find_code_submit
    ): void
    {
        if ($this->wasActionSubmited($article_attachment_submit)) {
            $this->showUserSubmitWarnings($attaching_article, $code_pattern);
        }
        if ($this->wasActionSubmited($find_code_submit)) {
            $this->showUserSearchWarnings($code_pattern);
        }
    }

    private function getAdminPage(): array
    {
        $availableMainModes = $this->getAvailableMainModes();
        $availableAddModes = $this->getAvailableAddingModes();

        $mainMode = $this->getCurrentMainMode($_POST["main_option"]);
        $addingMode = $this->getCurrentAddingMode($_POST["add_option"]);

        $currentAddingCode = $this->getCurrentAddingCode($_POST["code"]);
        $currentAddingOutput = $this->getCurrentAddingOutput($_POST["output"]);
        $wasCodeCreated = $this->wasCodeCreated($currentAddingCode, $_POST["code_create_cancel"]);

        $this->tryAddCodeToDataBase(
            $wasCodeCreated,
            $currentAddingCode,
            $currentAddingOutput,
            $_POST["code_create_cancel"]
        );


        $currentArticle = $this->getCurrentAttachedArticle($_POST["attaching_article"]);
        $currentSearchingCode = $this->getCurrentSearchingCode($_POST["code_pattern"]);

        $submitAvailability = $this->getSubmitAvailabilityToAttach(
            $_POST["find_code_submit"],
            $_POST["attaching_article"],
            $_POST["code_pattern"]
        );

        $this->showPossibleWarnings(
            $_POST["article_attachment_submit"],
            $_POST["attaching_article"],
            $_POST["code_pattern"],
            $_POST["find_code_submit"]
        );

        $wasArticleAttached = $this->wasArticleAttached(
            $currentArticle,
            $currentSearchingCode,
            $_POST["article_attachment_submit"]
        );

        $this->tryAddAttachmentToDataBase(
            $wasArticleAttached,
            $currentSearchingCode,
            $currentArticle
        );

        return [
            "workshop.tpl.twig",
            [
                "available_modes"                => $availableMainModes,
                "available_adding_modes"         => $availableAddModes,

                "adding_mode"                    => $addingMode,
                "main_mode"                      => $mainMode,

                "was_code_created"               => $wasCodeCreated,

                "attaching_article"              => $currentArticle,
                "was_article_attached"           => $wasArticleAttached,
                "searching_code"                 => $currentSearchingCode,
                "send_able"                      => $submitAvailability,
            ],
        ];
    }

    private function isAdminDevState(): bool
    {
        return (
            !isset($_POST["switcher"]) &&
            !isset($_POST["main_option"]) &&
            !isset($_POST["add_option"])
        );
    }

    public function getTemplateNameWithParameters(): array
    {
        return ($this->isAdminDevState()) ? $this->getUsualPage() : $this->getAdminPage();
    }
}