<?php

declare(strict_types = 1);

namespace Demo\Controller;

require_once "const.php";
require_once "../../vendor/autoload.php";
require_once "../service/Contents.php";
require_once "../service/LinkInserter.php";
require_once "../service/StyleChanger.php";

use Demo\Service\Contents as ContentsService;
use Demo\Service\LinkInserter;
use Demo\Service\StyleChanger;

$tunnelToDB = new ContentsService();
$pageContents = $tunnelToDB->getContentsFromPage(basename(__FILE__));

$linkInserter = new LinkInserter();
$externalLinks = $tunnelToDB->getLinksAssociationsFromPage(basename(__FILE__));
$pageContents["articles"] = $linkInserter->insertLinksIntoTexts($externalLinks, $pageContents["articles"]);

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
    "MVC_scenario_list"    => $pageContents["lists"]["MVC_scenario"],
    "MVC_advantages_list"  => $pageContents["lists"]["MVC_advantages"],
    "radiobuttonValues"    => $radiobuttonValues,
    "image_1_src"          => IMAGES_FOLDER_PATH . $pageContents["images"][0]["name"],
    "image_2_src"          => IMAGES_FOLDER_PATH . $pageContents["images"][1]["name"],
]);