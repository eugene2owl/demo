<?php

declare(strict_types = 1);

namespace Demo\Controller;

require_once "const.php";
require_once "../../vendor/autoload.php";
require_once "../service/Contents.php";
require_once "../service/LinkInserter.php";
require_once "../service/CodeProcessor.php";

use Demo\Service\Contents as ContentsService;
use Demo\Service\LinkInserter;
use Demo\Service\CodeProcessor;

$tunnelToDB = new ContentsService();

$pageContents = $tunnelToDB->getContentsFromPage(basename(__FILE__));

$codeProcessor = new CodeProcessor();
$codes = $codeProcessor->processCodes($pageContents["codes"]);

$linkInserter = new LinkInserter();
$externalLinks = $tunnelToDB->getLinksAssociationsFromPage(basename(__FILE__));
$pageContents["articles"] = $linkInserter->insertLinksIntoTexts($externalLinks, $pageContents["articles"]);

$loader = new \Twig_Loader_Filesystem(TEMPLATES_PATH_FOR_TWIG);
$twig = new \Twig_Environment($loader);

echo $twig->render("DBDesign.tpl.twig", [
    "title"                 => $pageContents["titles"][0]["name"],
    "header"                => $pageContents["titles"][0]["name"],
    "articles"              => $pageContents["articles"],
    "DBDesignAdvices"       => $pageContents["lists"]["MySQL_design_advices"],
    "DBPrinciples"          => $pageContents["lists"]["self_DB_principles"],
    "codes"                 => $codes,
    "image_1_src"           => IMAGES_FOLDER_PATH . $pageContents["images"][0]["name"],
    "up_url"                => basename(__FILE__),
    "image_schema"          => IMAGES_FOLDER_PATH . $pageContents["images"][1]["name"],
]);