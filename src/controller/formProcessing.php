<?php

declare(strict_types = 1);

namespace Demo\Controller;

require_once "const.php";
require_once "../../vendor/autoload.php";
require_once "../service/Contents.php";
require_once "../service/CodeProcessor.php";
require_once "../service/FormProcessorDemo.php";
require_once "../service/ListProcessor.php";

use Demo\Service\Contents as ContentsService;
use Demo\Service\CodeProcessor;
use Demo\Service\FormProcessorDemo;
use Demo\Service\ListProcessor;

$tunnelToDB = new ContentsService();
$pageContents = $tunnelToDB->getContentsFromPage(basename(__FILE__));

$codes = $tunnelToDB->getCodesWithAttachmentsFromPage(basename(__FILE__));
$codeProcessor = new CodeProcessor();
$codes = $codeProcessor->processCodes($codes);

$listProcessor = new ListProcessor();
$codes = $listProcessor->clarifyCodesLists($codes);

$conclusionList = $listProcessor->clarifyList($pageContents["lists"][5]);

$formProcessor = new FormProcessorDemo();
$formResults = $formProcessor->getFormResults();

$loader = new \Twig_Loader_Filesystem(TEMPLATES_PATH_FOR_TWIG);
$twig = new \Twig_Environment($loader);

echo $twig->render("formProcessing.tpl.twig", [
    "title"             => $pageContents["titles"][0]["name"],
    "header"            => $pageContents["titles"][0]["name"],
    "articles"          => $pageContents["articles"],
    "form_results"      => $formResults,
    "codes"             => $codes,
    "conclusion"        => $pageContents["titles"][1]["name"],
    "conclusionList"    => $conclusionList,
]);