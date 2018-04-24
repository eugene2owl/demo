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
    // принимает пост со страницы
    // возвращает имя рендеримой страницы и параметры для отображения
    // Если была нажата кнопка "dev", то возвращает страницу для development-а.
    // Иначе - обычную страницу

    // Если прилетают данные с dev-страницы, смотрит, что это за радио: добавление / редактирование / удаление
    // Возрвращает имя нужного шаблона и параметры поста, которые в нём (пустые первый раз)
    // сразу выбран код и можно выбрать другую радио
    // Если прилетает первая радио и add кода и вывода, добавляет код с выводом в БД
    // если прилетает вторая радио, то возвращает шаблон для добавления attachment-ов (пока просто абзаца)
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

    private function validateAddingCode(?string $code): ?string
    {
        if (!isset($code)) {
            return null;
        }
        if (strlen($code) > 1) {
            return $code;
        }
        return null;
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

    private function getAvailableArticleAttachingModes(): array
    {
        return [
            1 => "find code",
            2 => "attach to code",
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
            return htmlentities($inputedcode);
        }
        return "";
    }

    private function wasActionCanceled(?string $cancel): bool
    {
        return isset($cancel);
    }

    private function getCurrentAttachedArticle(?string $inputedArticle): ?string
    {
        if (
            is_string($inputedArticle) &&
            strlen($inputedArticle) > 2
        ) {
            return htmlentities($inputedArticle);
        }
        return null;
    }

    private function wasArticleAttachmentSubmitted(?string $attaching): bool
    {
        return isset($attaching);
    }

    private function getCurrentSearchingCode(?string $inputedPattern): ?string
    {
        if (
            is_string($inputedPattern) &&
            strlen($inputedPattern) > 2
        ) {
            return htmlentities("needed code.");
        }
        return null;
    }

    private function getAdminPage(): array
    {
        $availableMainModes = $this->getAvailableMainModes();
        $availableAddModes = $this->getAvailableAddingModes();

        $mainMode = $this->getCurrentMainMode($_POST["main_option"]);
        $addingMode = $this->getCurrentAddingMode($_POST["add_option"]);

        $currentAddingCode = $this->getCurrentAddingCode($_POST["code"]);
        $wasCodeCreated = !empty($currentAddingCode) && !$this->wasActionCanceled($_POST["code_create_cancel"]);

        $currentArticle = $this->getCurrentAttachedArticle($_POST["attaching_article"]) ?? "article is not valid.";
        $currentSearchingCode = $this->getCurrentSearchingCode($_POST["code_pattern"]) ?? "no code found.";
        $wasArticleAttached = (
            $currentArticle !== "article is not valid." &&
            $currentSearchingCode !== "no code found." &&
            $this->wasArticleAttachmentSubmitted($_POST["article_attachment_submit"])
        );
        return ["workshop.tpl.twig", [
            "available_modes"                => $availableMainModes,
            "available_adding_modes"         => $availableAddModes,

            "adding_mode"                    => $addingMode,
            "main_mode"                      => $mainMode,

            "was_code_created"               => $wasCodeCreated,

            "attaching_article"              => $currentArticle,
            "was_article_attached"           => $wasArticleAttached,
            "searching_code"                 => $currentSearchingCode,
        ]];
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