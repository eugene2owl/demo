<?php

declare(strict_types = 1);

namespace Demo\Controller;

require_once "../../vendor/autoload.php";
require_once "../service/Contents.php";
require_once "../service/CodeProcessor.php";

use Demo\Service\CodeProcessor;
use Demo\Service\Contents as ContentsService;

$tunnelToDB = new ContentsService();
$contents = $tunnelToDB->getContents(basename(__FILE__));

$codes = $contents["codes"];
$codeProcessor = new CodeProcessor();
$codes = $codeProcessor->processCodes($codes);

$loader = new \Twig_Loader_Filesystem("../../tpl/");
$twig = new \Twig_Environment($loader);

echo $twig->render("writingStyle.tpl.twig", [
    "title"       => $contents["titles"][0]["title"],
    "header"      => $contents["titles"][0]["title"],
    "articles"    => $contents["articles"],
    "codes"       => $codes,
    "lists"       => $contents["lists"],
]);