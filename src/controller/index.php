<?php

declare(strict_types = 1);

namespace Demo\Controller;

require_once "../../vendor/autoload.php";
require_once "../service/Contents.php";

use Demo\Service\Contents as ContentsService;

$tunnelToDB = new ContentsService();
$contents = $tunnelToDB->getContents(basename(__FILE__));
$titles = $tunnelToDB->getEntityArray("titles");

$loader = new \Twig_Loader_Filesystem("../../tpl/");
$twig = new \Twig_Environment($loader);

echo $twig->render("index.tpl.twig", [
    "title"         => $contents["titles"][0]["title"],
    "header"        => $contents["titles"][0]["title"],
    "siteBar"       => $titles,
    "article_1"     => $contents["articles"][0]["article"],
    "article_2"     => $contents["articles"][1]["article"],
    "sources"       => $contents["links"],
]);