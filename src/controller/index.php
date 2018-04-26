<?php

declare(strict_types = 1);

namespace Demo\Controller;

require_once "const.php";
require_once "../../vendor/autoload.php";
require_once "../service/Contents.php";
require_once "../service/LinkInserter.php";

use Demo\Service\Contents as ContentsService;
use Demo\Service\LinkInserter;

$tunnelToDB = new ContentsService();

$pageContents = $tunnelToDB->getContentsFromPage(basename(__FILE__));

$pageTitleCouples = $tunnelToDB->getPageTitleCouples();

$linkInserter = new LinkInserter();
$externalLinks = $tunnelToDB->getLinksAssociationsFromPage(basename(__FILE__));
$innerLinks = [
    "MVC conception"                            => array_keys($pageTitleCouples)[5],
    "interaction with a database"               => array_keys($pageTitleCouples)[4],
    "form processing"                           => array_keys($pageTitleCouples)[3],
    "high level solutions of trivial issues"    => array_keys($pageTitleCouples)[1],
    "writing your own functions"                => array_keys($pageTitleCouples)[2],
    "issues of database design"                 => array_keys($pageTitleCouples)[7],
];
$pageContents["lists"]["sources"] = $linkInserter->insertLinksIntoTexts($externalLinks, $pageContents["lists"]["sources"]);
$pageContents["articles"] = $linkInserter->insertLinksIntoTexts($innerLinks, $pageContents["articles"]);

$loader = new \Twig_Loader_Filesystem(TEMPLATES_PATH_FOR_TWIG);
$twig = new \Twig_Environment($loader);

echo $twig->render("index.tpl.twig", [
    "title"         => $pageContents["titles"][0]["name"],
    "header"        => $pageContents["titles"][0]["name"],
    "siteBar"       => $pageTitleCouples,
    "article_1"     => $pageContents["articles"][0]["name"],
    "article_2"     => $pageContents["articles"][1]["name"],
    "sources"       => $pageContents["lists"]["sources"],
    "image_1_src"   => IMAGES_FOLDER_PATH . $pageContents["images"][0]["name"],
    "up_url"        => basename(__FILE__),
]);
