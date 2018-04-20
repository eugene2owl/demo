<?php

declare(strict_types = 1);

namespace Demo\Controller;

require_once "const.php";
require_once "../../vendor/autoload.php";
require_once "../service/Contents.php";
require_once "../service/CodeProcessor.php";

use Demo\Service\Contents as ContentsService;
use Demo\Service\CodeProcessor;

$tunnelToDB = new ContentsService();
$pageContents = $tunnelToDB->getContentsFromPage(basename(__FILE__));

$codes = $tunnelToDB->getCodesWithAttachmentsFromPage(basename(__FILE__));
$codeProcessor = new CodeProcessor();
$codes = $codeProcessor->processCodes($codes);

$loader = new \Twig_Loader_Filesystem(TEMPLATES_PATH_FOR_TWIG);
$twig = new \Twig_Environment($loader);

echo $twig->render("baseTypes.tpl.twig", [
    "title"        => $pageContents["titles"][0]["name"],
    "header"       => $pageContents["titles"][0]["name"],
    "article_1"    => $pageContents["articles"][0]["name"],
    "codes"        => $codes,
    "image_1_src"  => IMAGES_FOLDER_PATH . $pageContents["images"][0]["name"]
]);
