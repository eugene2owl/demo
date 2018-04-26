<?php

declare(strict_types = 1);

namespace Demo\Controller;

require_once "const.php";
require_once "../../vendor/autoload.php";
require_once "../service/Contents.php";
require_once "../service/CodeProcessor.php";

use Demo\Service\CodeProcessor;
use Demo\Service\Contents as ContentsService;

$tunnelToDB = new ContentsService();
$pageContents = $tunnelToDB->getContentsFromPage(basename(__FILE__));

$codeProcessor = new CodeProcessor();
$codes = $codeProcessor->processCodes($pageContents["codes"]);

$loader = new \Twig_Loader_Filesystem(TEMPLATES_PATH_FOR_TWIG);
$twig = new \Twig_Environment($loader);

echo $twig->render("writingStyle.tpl.twig", [
    "title"       => $pageContents["titles"][0]["name"],
    "header"      => $pageContents["titles"][0]["name"],
    "articles"    => $pageContents["articles"],
    "codes"       => $codes,
    "lists"       => $pageContents["lists"],
]);