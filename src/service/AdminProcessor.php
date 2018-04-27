<?php

declare(strict_types = 1);

namespace Demo\Service;

require_once "../controller/const.php";
require_once "../../vendor/autoload.php";
require_once "Contents.php";
require_once "CodeProcessor.php";
<<<<<<< HEAD
require_once "ListProcessor.php";

use Demo\Service\Contents as ContentsService;
use Demo\Service\CodeProcessor;
use Demo\Service\ListProcessor;
=======

use Demo\Service\Contents as ContentsService;
use Demo\Service\CodeProcessor;
>>>>>>> master

class AdminProcessor
{
    private $pageName;
<<<<<<< HEAD
=======

    private const CODE_MIN_LENGHT = 5;
    private const ARTICLE_MIN_LENGHT = 5;
    private const PATTERN_MIN_LENGHT = 1;

>>>>>>> master
    public function __construct(string $pageName = "admin.php")
    {
        $this->pageName = $pageName;
    }

    private function getUsualPage(): array
    {
        $tunnelToDB = new ContentsService();
        $pageContents = $tunnelToDB->getContentsFromPage($this->pageName);

<<<<<<< HEAD
        $codes = $tunnelToDB->getCodesWithAttachmentsFromPage($this->pageName);
        $codeProcessor = new CodeProcessor();
        $codes = $codeProcessor->processCodes($codes);

        $listProcessor = new ListProcessor();
        $codes = $listProcessor->clarifyCodesLists($codes);
=======
        $codeProcessor = new CodeProcessor();
        $codes = $codeProcessor->processCodes($pageContents["codes"]);
>>>>>>> master

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
<<<<<<< HEAD
            3 => "remove",
=======
>>>>>>> master
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
<<<<<<< HEAD
            strlen($inputedcode) > 2
=======
            strlen(trim($inputedcode)) > self::CODE_MIN_LENGHT - 1
>>>>>>> master
        ) {
            return filter_var($inputedcode, FILTER_SANITIZE_STRING);
        }
        return "";
    }

<<<<<<< HEAD
=======
    private function getCurrentAddingOutput(?string $inputedOutput): string
    {
        if (is_string($inputedOutput)) {
            return filter_var($inputedOutput, FILTER_SANITIZE_STRING);
        }
        return "";
    }

>>>>>>> master
    private function wasActionSubmited(?string $buttonName): bool
    {
        return isset($buttonName);
    }

    private function getCurrentAttachedArticle(?string $inputedArticle): string
    {
        if (
            is_string($inputedArticle) &&
<<<<<<< HEAD
            strlen($inputedArticle) > 2
=======
            strlen(trim($inputedArticle)) > self::ARTICLE_MIN_LENGHT - 1
>>>>>>> master
        ) {
            return filter_var($inputedArticle, FILTER_SANITIZE_STRING);
        }
        return "";
    }

    private function getCurrentSearchingCode(?string $inputedPattern): string
    {
        if (
            is_string($inputedPattern) &&
<<<<<<< HEAD
            strlen($inputedPattern) > 2
        ) {
            return filter_var($inputedPattern, FILTER_SANITIZE_STRING);
=======
            strlen(trim($inputedPattern)) > self::PATTERN_MIN_LENGHT - 1
        ) {
            return filter_var($inputedPattern, FILTER_SANITIZE_STRING);   // query to DB
>>>>>>> master
        }
        return "";
    }

    private function showJSMessage(string $message): void
    {
        echo "<script> alert('$message') </script>";
    }

<<<<<<< HEAD
    private function showUserSubmitWarnings(?string $inputedArticle, ?string $inputedCodePattern): bool
=======
    private function showUserSubmitWarnings(?string $inputedArticle, ?string $inputedCodePattern): bool ///// the same for crating TO DO
>>>>>>> master
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

<<<<<<< HEAD
    private function getSubmitAvailability(?string $searchClick, ?string $inputedArticle, ?string $inputedCodePattern): bool
=======
    private function getSubmitAvailabilityToAttach(?string $searchClick, ?string $inputedArticle, ?string $inputedCodePattern): bool // not code pattern but founded code
>>>>>>> master
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

<<<<<<< HEAD
=======
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

>>>>>>> master
    private function getAdminPage(): array
    {
        $availableMainModes = $this->getAvailableMainModes();
        $availableAddModes = $this->getAvailableAddingModes();

        $mainMode = $this->getCurrentMainMode($_POST["main_option"]);
        $addingMode = $this->getCurrentAddingMode($_POST["add_option"]);

        $currentAddingCode = $this->getCurrentAddingCode($_POST["code"]);
<<<<<<< HEAD
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
=======
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
>>>>>>> master
            $_POST["attaching_article"],
            $_POST["code_pattern"]
        );

<<<<<<< HEAD
        if ($this->wasActionSubmited($_POST["article_attachment_submit"])) {
            $this->showUserSubmitWarnings($_POST["attaching_article"], $_POST["code_pattern"]);
        }

        if ($this->wasActionSubmited($_POST["find_code"])) {
            $this->showUserSearchWarnings($_POST["code_pattern"]);
        }
=======
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
>>>>>>> master

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

<<<<<<< HEAD
    public function getTemplateNameWithParameters(): array
=======
    private function isAdminDevState(): bool
>>>>>>> master
    {
        return (
            !isset($_POST["switcher"]) &&
            !isset($_POST["main_option"]) &&
            !isset($_POST["add_option"])
<<<<<<< HEAD
        ) ? $this->getUsualPage() : $this->getAdminPage();
=======
        );
    }

    public function getTemplateNameWithParameters(): array
    {
        return ($this->isAdminDevState()) ? $this->getUsualPage() : $this->getAdminPage();
>>>>>>> master
    }
}