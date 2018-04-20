<?php

declare(strict_types = 1);

namespace Demo\Controller;

require_once "const.php";
require_once "../../vendor/autoload.php";
require_once "../service/Contents.php";
require_once "../service/LinkInserter.php";
require_once "../service/StyleChanger.php";
require_once "../service/ListProcessor.php";

use Demo\Service\Contents as ContentsService;
use Demo\Service\LinkInserter;
use Demo\Service\StyleChanger;
use Demo\Service\ListProcessor;

$tunnelToDB = new ContentsService();
$pageContents = $tunnelToDB->getContentsFromPage(basename(__FILE__));

$listProcessor = new ListProcessor();
$lists = $listProcessor->clarifyLists($pageContents["lists"]);

$linkInserter = new LinkInserter();
$links = [

];
$pageContents["articles"] = $linkInserter->insertLinksIntoTexts($links, $pageContents["articles"]);

$styleChanger = new StyleChanger();
$radiobuttonValues = $styleChanger->getRadiobuttonValues();
$styleMode = $styleChanger->getStyleMode($_POST["styleMode"]);

$loader = new \Twig_Loader_Filesystem(TEMPLATES_PATH_FOR_TWIG);
$twig = new \Twig_Environment($loader);

echo $twig->render("MVCConception.tpl.twig", [
    "style"                => $styleMode,
    "title"                => $pageContents["titles"][0]["name"],
    "header"               => $pageContents["titles"][0]["name"],
    "articles"             => $pageContents["articles"],
    "title_1"              => $pageContents["titles"][1]["name"],
    "lists"                => $lists,
    "radiobuttonValues"    => $radiobuttonValues,
    "image_1_src"          => IMAGES_FOLDER_PATH . $pageContents["images"][0]["name"],
    "image_2_src"          => IMAGES_FOLDER_PATH . $pageContents["images"][1]["name"],
]);