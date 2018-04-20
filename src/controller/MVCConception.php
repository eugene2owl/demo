<?php

declare(strict_types = 1);

namespace Demo\Controller;

require_once "../../vendor/autoload.php";
require_once "../service/Contents.php";
require_once "../service/LinkInserter.php";
require_once "../service/StyleChanger.php";

use Demo\Service\Contents as ContentsService;
use Demo\Service\LinkInserter;
use Demo\Service\StyleChanger;

$tunnelToDB = new ContentsService();
$contents = $tunnelToDB->getContents(basename(__FILE__));

$linkInserter = new LinkInserter();
$links = [

];
$contents["articles"] = $linkInserter->insertLinksIntoTextes($links, $contents["articles"]);

$styleChanger = new StyleChanger();
$radiobuttonValues = $styleChanger->getRadiobuttonValues();
$styleMode = $styleChanger->getStyleMode($_POST["styleMode"]);

$loader = new \Twig_Loader_Filesystem("../../tpl/");
$twig = new \Twig_Environment($loader);

echo $twig->render("MVCConception.tpl.twig", [
    "style"                => $styleMode,
    "title"                => $contents["titles"][0]["title"],
    "header"               => $contents["titles"][0]["title"],
    "articles"             => $contents["articles"],
    "title_1"              => "So what are we getting?",
    "lists"                => $contents["lists"],
    "radiobuttonValues"    => $radiobuttonValues,
]);