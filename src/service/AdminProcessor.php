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

    private function getAdminPage(): array
    {
        return ["workshop.tpl.twig", [
            "mode"                 => $_POST["main_option"],
            "available_modes"      => ["add", "edit", "remove"],
            "available_add_modes"  => ["add code", "add attachment"],
            "add_mode"             => $_POST["add_option"] ?? 1
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