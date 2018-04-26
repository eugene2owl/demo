<?php

declare(strict_types = 1);

namespace Demo\Controller;

require_once "const.php";
require_once "../../vendor/autoload.php";
require_once "../service/Contents.php";
require_once "../service/CodeProcessor.php";
require_once "../service/LinkInserter.php";

use Demo\Service\Contents as ContentsService;
use Demo\Service\CodeProcessor;
use Demo\Service\LinkInserter;

$tunnelToDB = new ContentsService();
$pageContents = $tunnelToDB->getContentsFromPage(basename(__FILE__));
$pageTitleCouples = $tunnelToDB->getPageTitleCouples();

$codeProcessor = new CodeProcessor();
$codes = $codeProcessor->processCodes($pageContents["codes"]);

$linkInserter = new LinkInserter();
$externalLinks = $tunnelToDB->getLinksAssociationsFromPage(basename(__FILE__));
$innerLinks = [
    "long way"       => array_keys($pageTitleCouples)[5],
];
$links = array_merge($externalLinks, $innerLinks);
$pageContents["articles"] = $linkInserter->insertLinksIntoTexts($links, $pageContents["articles"]);

$loader = new \Twig_Loader_Filesystem(TEMPLATES_PATH_FOR_TWIG);
$twig = new \Twig_Environment($loader);

echo $twig->render("DBInteraction.tpl.twig", [
    "title"        => $pageContents["titles"][0]["name"],
    "header"       => $pageContents["titles"][0]["name"],
    "articles"     => $pageContents["articles"],
    "header_1"     => $pageContents["titles"][1]["name"],
    "codes"        => $codes,
    "lists"        => $pageContents["lists"],
]);