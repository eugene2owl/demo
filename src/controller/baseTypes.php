<?php

namespace Demo\Controller;

require_once "../../vendor/autoload.php";
require_once "../service/Contents.php";
require_once "../service/CodeProcessor.php";

use Demo\Service\CodeProcessor;
use Demo\Service\Contents as ContentsService;

$tunnelToDB = new ContentsService();
$contents = $tunnelToDB->getContents(basename(__FILE__));

$codes = $tunnelToDB->getEntityArray("codes");
$codeProcessor = new CodeProcessor();
$codes = $codeProcessor->processCodes($codes);

$loader = new \Twig_Loader_Filesystem("../../tpl");
$twig = new \Twig_Environment($loader);

echo $twig->render("baseTypes.tpl.twig", [
    "title"        => $contents["titles"][0]["title"],
    "header"       => $contents["titles"][0]["title"],
    "article_1"    => "<h1>" . $contents["articles"][0]["article"] . "</h1>",
    "codes"        => $codes,
]);

echo "<span style='text-decoration: underline'>161</span>";