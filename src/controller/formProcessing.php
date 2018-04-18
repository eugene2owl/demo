<?php

declare(strict_types = 1);

namespace Demo\Controller;

require_once "../../vendor/autoload.php";
require_once "../service/Contents.php";
require_once "../service/CodeProcessor.php";
require_once "../service/FormProcessorDemo.php";
require_once "../service/ListProcessor.php";

use Demo\Service\Contents as ContentsService;
use Demo\Service\CodeProcessor;
use Demo\Service\FormProcessorDemo;
use Demo\Service\ListProcessor;

$tunnelToDB = new ContentsService();
$contents = $tunnelToDB->getContents(basename(__FILE__));

$codes = $contents["codes"];
$codeProcessor = new CodeProcessor();
$codes = $codeProcessor->processCodes($codes);

$lists = $contents["lists"];
$listProcessor = new ListProcessor();
$lists = $listProcessor->distributeLists($lists);

$formProcessor = new FormProcessorDemo();
$formResults = $formProcessor->getFormResults();

$loader = new \Twig_Loader_Filesystem("../../tpl");
$twig = new \Twig_Environment($loader);

echo $twig->render("formProcessing.tpl.twig", [
    "title"        => $contents["titles"][0]["title"],
    "header"       => $contents["titles"][0]["title"],
    "articles"     => $contents["articles"],
    "form_results" => $formResults,
    "codes"        => $codes,
    "lists"        => $lists,
    "conclusion"   => "Summing up form processing rules"
]);