<?php

declare(strict_types = 1);

namespace Demo\Service;

require_once "../controller/const.php";
require_once "../../vendor/autoload.php";
require_once "Contents.php";
require_once "CodeProcessor.php";
require_once "ListProcessor.php";

use Demo\Service\Contents as ContentsService;
use Demo\Service\CodeProcessor;
use Demo\Service\ListProcessor;

class AdminProcessor
{
    private $pageName;
    public function __construct(string $pageName = "admin.php")
    {
        $this->pageName = $pageName;
    }

    private function getUsualPage(): array
    {
        $tunnelToDB = new ContentsService();
        $pageContents = $tunnelToDB->getContentsFromPage($this->pageName);

        $codes = $tunnelToDB->getCodesWithAttachmentsFromPage($this->pageName);
        $codeProcessor = new CodeProcessor();
        $codes = $codeProcessor->processCodes($codes);

        $listProcessor = new ListProcessor();
        $codes = $listProcessor->clarifyCodesLists($codes);

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
            3 => "remove",
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
            strlen($inputedcode) > 2
        ) {
            return filter_var($inputedcode, FILTER_SANITIZE_STRING);
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
            strlen($inputedArticle) > 2
        ) {
            return filter_var($inputedArticle, FILTER_SANITIZE_STRING);
        }
        return "";
    }

    private function getCurrentSearchingCode(?string $inputedPattern): string
    {
        if (
            is_string($inputedPattern) &&
            strlen($inputedPattern) > 2
        ) {
            return filter_var($inputedPattern, FILTER_SANITIZE_STRING);
        }
        return "";
    }

    private function showJSMessage(string $message): void
    {
        echo "<script> alert('$message') </script>";
    }

    private function showUserSubmitWarnings(?string $inputedArticle, ?string $inputedCodePattern): bool
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

    private function getSubmitAvailability(?string $searchClick, ?string $inputedArticle, ?string $inputedCodePattern): bool
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

    private function getAdminPage(): array
    {
        $availableMainModes = $this->getAvailableMainModes();
        $availableAddModes = $this->getAvailableAddingModes();

        $mainMode = $this->getCurrentMainMode($_POST["main_option"]);
        $addingMode = $this->getCurrentAddingMode($_POST["add_option"]);

        $currentAddingCode = $this->getCurrentAddingCode($_POST["code"]);
        $wasCodeCreated = !empty($currentAddingCode) && !$this->wasActionSubmited($_POST["code_create_cancel"]);

        $currentArticle = $this->getCurrentAttachedArticle($_POST["attaching_article"]);
        $currentSearchingCode = $this->getCurrentSearchingCode($_POST["code_pattern"]);
        $wasArticleAttached = (
            !empty($currentArticle) &&
            !empty($currentSearchingCode) &&
            $this->wasActionSubmited($_POST["article_attachment_submit"])
        );

        $submitAvailability = $this->getSubmitAvailability(
            $_POST["find_code"],
            $_POST["attaching_article"],
            $_POST["code_pattern"]
        );

        if ($this->wasActionSubmited($_POST["article_attachment_submit"])) {
            $this->showUserSubmitWarnings($_POST["attaching_article"], $_POST["code_pattern"]);
        }

        if ($this->wasActionSubmited($_POST["find_code"])) {
            $this->showUserSearchWarnings($_POST["code_pattern"]);
        }

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

    public function getTemplateNameWithParameters(): array
    {
        return (
            !isset($_POST["switcher"]) &&
            !isset($_POST["main_option"]) &&
            !isset($_POST["add_option"])
        ) ? $this->getUsualPage() : $this->getAdminPage();
    }
}