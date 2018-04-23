<?php

declare(strict_types = 1);

namespace Demo\Controller;

require_once "const.php";
require_once "../../vendor/autoload.php";
require_once "../service/Contents.php";
require_once "../service/CodeProcessor.php";
require_once "../service/LinkInserter.php";
require_once "../service/ListProcessor.php";

use Demo\Service\Contents as ContentsService;
use Demo\Service\CodeProcessor;
use Demo\Service\LinkInserter;
use Demo\Service\ListProcessor;

$tunnelToDB = new ContentsService();
$pageContents = $tunnelToDB->getContentsFromPage(basename(__FILE__));
$pageTitleCouples = $tunnelToDB->getPageTitleCouples();

$codes = $tunnelToDB->getCodesWithAttachmentsFromPage(basename(__FILE__));
$codeProcessor = new CodeProcessor();
$codes = $codeProcessor->processCodes($codes);

$listProcessor = new ListProcessor();
$lists = $listProcessor->clarifyLists($pageContents["lists"]);

$linkInserter = new LinkInserter();
$externalLinks = $tunnelToDB->getLinksAssociationsFromPage(basename(__FILE__));
$innerLinks = [
    "long way"       => $pageTitleCouples[5]["page"],
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
    "lists"        => $lists,
]);