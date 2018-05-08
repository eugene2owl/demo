<?php

declare(strict_types = 1);

namespace Demo\Service;

require_once "../controller/const.php";
require_once "../../vendor/autoload.php";
require_once "Contents.php";
require_once "CodeProcessor.php";
require_once "../service/AdminAction.php";

use Demo\Service\Contents as ContentsService;
use Demo\Service\AdminAction as tunnelToRepository;

class AdminProcessor
{
    private $pageName;
    private $tunnelToRepository;

    private const CODE_MIN_LENGTH = 5;
    private const ARTICLE_MIN_LENGTH = 5;
    private const PATTERN_MIN_LENGTH = 1;

    private const INVALID_ARTICLE = "Article is not valid.";
    private const INVALID_CODE = "Code is not valid.";
    private const CODE_NOT_FOUND = "No code found.";
    private const CODE_ADDED = "Code successfully added to page.";
    private const CODE_NOT_ADDED_DB = "Code was not added because of inner database occasion.";
    private const CODE_NOT_ADDED_INPUT = "Code was not added because of not valid input.";
    private const ATTACHED = "Successfully attached to code.";
    private const NOT_ATTACHED_DB = "Not attached because of inner database occasion.";
    private const NOT_ATTACHED_INPUT = "Not attached because of not valid input.";
    private const EDITED = "Successfully edited.";
    private const NOT_EDITED_DB = "Not edited because of inner database occasion.";
    private const NOT_EDITED_INPUT = "Not edited because of not valid input.";

    public function __construct(string $pageName = "admin.php")
    {
        $this->tunnelToRepository = new tunnelToRepository();
        $this->pageName = $pageName;
    }

    private function getUsualPage(bool $adminDevelopmentAcess): array
    {
        $tunnelToDB = new ContentsService();
        $pageContents = $tunnelToDB->getContentsFromPage($this->pageName);

        $codeProcessor = new CodeProcessor();
        $codes = $codeProcessor->processCodes($pageContents["codes"]);

        return [
            "admin.tpl.twig",
            [
                "title"        => $pageContents["titles"][0]["name"],
                "header"       => $pageContents["titles"][0]["name"],
                "codes"        => $codes,
                "admin_access" => $adminDevelopmentAcess,
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
            strlen(trim($inputedcode)) > self::CODE_MIN_LENGTH - 1
        ) {
            return trim($inputedcode);
        }
        return "";
    }

    private function getCurrentAddingOutput(?string $inputedOutput): string
    {
        if (is_string($inputedOutput)) {
            return trim($inputedOutput);
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
            strlen(trim($inputedArticle)) > self::ARTICLE_MIN_LENGTH - 1
        ) {
            return filter_var($inputedArticle, FILTER_SANITIZE_STRING);
        }
        return "";
    }

    private function getCurrentEditedArticles(?array $inputedArticles, ?string $codePattern,?string $searchMode = null): array
    {
        if ($searchMode) {
            return $this->tunnelToRepository->getArticlesByCodePattern($codePattern, $this->pageName);
        }
        if (is_array($inputedArticles)) {
            return $inputedArticles;
        }
        return [];
    }

    private function getCurrentSearchingCode(?string $inputedPattern): string
    {
        if (
            is_string($inputedPattern) &&
            strlen(trim($inputedPattern)) > self::PATTERN_MIN_LENGTH - 1
        ) {
            return $this->tunnelToRepository->getCodeByPattern($inputedPattern, $this->pageName);
        }
        return "";
    }

    private function showJSMessage(string $message): void
    {
        echo "<script> alert('$message') </script>";
    }

    private function showUserSubmitAttachingWarnings(?string $inputedArticle, ?string $inputedCodePattern): bool
    {
        if (empty($this->getCurrentAttachedArticle($inputedArticle))) {
            $messageList[] = self::INVALID_ARTICLE;
        }
        if (empty($this->getCurrentSearchingCode($inputedCodePattern))) {
            $messageList[] = self::INVALID_CODE;
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
            $this->showJSMessage(self::CODE_NOT_FOUND);
            return false;
        }
        return true;
    }

    private function getSubmitAvailabilityToAttach(?string $searchClick, ?string $inputedArticle, ?string $inputedCodePattern): bool
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

    private function getSubmitAvailabilityToEdit(?string $searchClick, ?string $inputedCodePattern): bool
    {
        if (!isset($searchClick)) {
            return false;
        }
        if (empty($this->getCurrentSearchingCode($inputedCodePattern))) {
            return false;
        }
        return true;
    }

    private function addCodeToDataBase(string $code, string $output): bool
    {
        return $this->tunnelToRepository->addCodeToAdminPage($code, $output, $this->pageName);
    }

    private function addAttachmentToDataBase(string $code, string $article): bool
    {
        return $this->tunnelToRepository->attachArticleToCode($code, $article);
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
                $this->showJSMessage(self::CODE_ADDED);
            } else {
                $this->showJSMessage(self::CODE_NOT_ADDED_DB);
            }
        }
        if (!$wasCodeCreated && !$this->wasActionSubmited($cancelButtonName) && $this->isAdminDevState()) {
            $this->showJSMessage(self::CODE_NOT_ADDED_INPUT);
        }
    }

    private function wasArticleAttached(
        ?string $currentArticle,
        ?string $currentSearchingCode,
        ?string $submitAttaching
    ): bool
    {
        return (
            !empty($currentArticle) &&
            !empty($currentSearchingCode) &&
            $this->wasActionSubmited($submitAttaching)
        );
    }

    private function wasArticlesAndCodeEdited(
        ?array $currentArticles,
        ?string $currentSearchingCode,
        ?string $submitEditing
    ): bool
    {
        if (is_array($currentArticles)) {
            foreach ($currentArticles as $currentArticle) {
                if (!is_string($currentArticle)) {
                    return false;
                }
            }
        } else {
            return false;
        }
        return (
            !empty($currentSearchingCode) &&
            $this->wasActionSubmited($submitEditing)
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
                $this->showJSMessage(self::ATTACHED);
            } else {
                $this->showJSMessage(self::NOT_ATTACHED_DB);
            }
        }
        if (!$wasArticleAttached && $this->isAdminDevState()) {
            $this->showJSMessage(self::NOT_ATTACHED_INPUT);
        }
    }

    private function addEditingToDataBase(string $currentSerchingCode, array $currentArticles, bool $deleteCode): bool
    {
        $this->tunnelToRepository->updateArticlesOfCode($currentSerchingCode, $currentArticles, $this->pageName, $deleteCode);
        return true;
    }

    private function tryAddEditingToDataBase(
        bool $wasArticlesAndCodeEdited,
        string $currentSearchingCode,
        array $currentArticles,
        bool $deleteCode
    ): void
    {
        if ($wasArticlesAndCodeEdited) {
            if ($this->addEditingToDataBase($currentSearchingCode, $currentArticles, $deleteCode)) {
                $this->showJSMessage(self::EDITED);
            } else {
                $this->showJSMessage(self::NOT_EDITED_DB);
            }
        }
        if (!$wasArticlesAndCodeEdited && $this->isAdminDevState()) {
            $this->showJSMessage(self::NOT_EDITED_INPUT);
        }
    }

    private function showPossibleAttachingWarnings(
        ?string $article_attachment_submit,
        ?string $attaching_article,
        ?string $code_pattern,
        ?string $find_code_submit
    ): void
    {
        if ($this->wasActionSubmited($article_attachment_submit)) {
            $this->showUserSubmitAttachingWarnings($attaching_article, $code_pattern);
        }
        if ($this->wasActionSubmited($find_code_submit)) {
            $this->showUserSearchWarnings($code_pattern);
        }
    }

    private function showUserSubmitEditingWarnings(?array $inputedArticle, ?string $inputedCodePattern): bool
    {
        if (empty($this->getCurrentEditedArticles($inputedArticle, $inputedCodePattern))) {
            $messageList[] = self::INVALID_ARTICLE;
        }
        if (empty($this->getCurrentSearchingCode($inputedCodePattern))) {
            $messageList[] = self::INVALID_CODE;
        }
        if (!empty($messageList)) {
            $this->showJSMessage(implode("\\n", $messageList));
            return false;
        }
        return true;
    }

    private function showPossibleEditingWarnings(
        ?string $editing_submit,
        ?array $attaching_articles,
        ?string $code_pattern,
        ?string $find_code_submit
    ): void
    {
        if ($this->wasActionSubmited($editing_submit)) {
            $this->showUserSubmitEditingWarnings($attaching_articles, $code_pattern);
        }
        if ($this->wasActionSubmited($find_code_submit)) {
            $this->showUserSearchWarnings($code_pattern);
        }
    }

    private function processCodeAdding(
        ?string $codePost,
        ?string $outputPost,
        ?string $codeCreateCancelPost
    ): array
    {
        $currentAddingCode = $this->getCurrentAddingCode($codePost);
        $currentAddingOutput = $this->getCurrentAddingOutput($outputPost);
        $wasCodeCreated = $this->wasCodeCreated($currentAddingCode, $codeCreateCancelPost);

        $this->tryAddCodeToDataBase(
            $wasCodeCreated,
            $currentAddingCode,
            $currentAddingOutput,
            $codeCreateCancelPost
        );
        return [];
    }

    private function processAttachmentAdding(
        ?string $findCodeSubmit,
        ?string $attachingArticle,
        ?string $codePattern,
        ?string $articleAttachmentSubmit
    ): array
    {
        $currentArticle = $this->getCurrentAttachedArticle($attachingArticle);

        $currentSearchingCode = $this->getCurrentSearchingCode($codePattern);

        $submitAvailability = $this->getSubmitAvailabilityToAttach(
            $findCodeSubmit,
            $attachingArticle,
            $codePattern
        );

        $this->showPossibleAttachingWarnings(
            $articleAttachmentSubmit,
            $attachingArticle,
            $codePattern,
            $findCodeSubmit
        );

        $wasArticleAttached = $this->wasArticleAttached(
            $currentArticle,
            $currentSearchingCode,
            $articleAttachmentSubmit
        );

        $this->tryAddAttachmentToDataBase(
            $wasArticleAttached,
            $currentSearchingCode,
            $currentArticle
        );
        return [
            "attaching_article"              => $currentArticle,
            "searching_code"                 => $currentSearchingCode,
            "send_able"                      => $submitAvailability,
        ];
    }

    private function processCodeEditing(
        ?string $findCodeSubmit,
        ?array  $editingArticles,
        ?string $codePattern,
        ?string $editingSubmit,
        ?string $inputedOutput,
        ?string $deleteCode
    ): array
    {
        $currentArticles = $this->getCurrentEditedArticles($editingArticles, $codePattern, $findCodeSubmit);

        $currentSearchingCode = $this->getCurrentSearchingCode($codePattern);

        $deleteCode = $this->wasActionSubmited($deleteCode);

        $submitAvailability = $this->getSubmitAvailabilityToEdit(
            $findCodeSubmit,
            $codePattern
        );

        $this->showPossibleEditingWarnings(
            $editingSubmit,
            $editingArticles,
            $codePattern,
            $findCodeSubmit
        );

        $wasArticlesAndCodeEdited = $this->wasArticlesAndCodeEdited(
            $currentArticles,
            $currentSearchingCode,
            $editingSubmit
        );

        $this->tryAddEditingToDataBase(
            $wasArticlesAndCodeEdited,
            $currentSearchingCode,
            $currentArticles,
            $deleteCode
        );
        return [
            "attaching_articles"             => $currentArticles,
            "searching_code"                 => $currentSearchingCode,
            "send_able"                      => $submitAvailability,
        ];
    }

    private function getAdminPage(): array
    {
        $availableMainModes = $this->getAvailableMainModes();
        $availableAddModes = $this->getAvailableAddingModes();

        $mainMode = $this->getCurrentMainMode($_POST["main_option"]);
        $addingMode = $this->getCurrentAddingMode($_POST["add_option"]);

        $renderableValues = [
            "available_modes"                => $availableMainModes,
            "available_adding_modes"         => $availableAddModes,
            "adding_mode"                    => $addingMode,
            "main_mode"                      => $mainMode,
        ];

        if ($mainMode == 1) {
            if ($addingMode == 1) {
                $this->processCodeAdding(
                    $_POST["code"],
                    $_POST["output"],
                    $_POST["code_create_cancel"]
                );
            } elseif ($addingMode == 2) {
                $additionalParameters = $this->processAttachmentAdding(
                    $_POST["find_code_submit"],
                    $_POST["attaching_article"],
                    $_POST["code_pattern"],
                    $_POST["article_attachment_submit"]
                );
                $renderableValues = array_merge($renderableValues, $additionalParameters);
            }
        } elseif ($mainMode == 2) {
            $additionalParameters = $this->processCodeEditing(
                $_POST["find_code_submit"],
                $_POST["attaching_articles"],
                $_POST["code_pattern"],
                $_POST["article_editing_submit"],
                "SOME OUTPUT HERE",
                $_POST["delete_code"]
            );
            $renderableValues = array_merge($renderableValues, $additionalParameters);
        }
        return ["workshop.tpl.twig", $renderableValues];
    }

    private function isAdminDevState(): bool
    {
        return (
            !isset($_POST["switcher"]) &&
            !isset($_POST["main_option"]) &&
            !isset($_POST["add_option"])
        );
    }

    public function getTemplateNameWithParameters(bool $adminDevelopmentAcess): array
    {
        return ($this->isAdminDevState()) ? $this->getUsualPage($adminDevelopmentAcess) : $this->getAdminPage();
    }
}