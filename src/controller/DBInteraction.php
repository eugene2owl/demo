<?php

declare(strict_types = 1);

namespace Demo\Controller;

require_once "../../vendor/autoload.php";
require_once "../service/Contents.php";
require_once "../service/CodeProcessor.php";
require_once "../service/LinkInserter.php";

use Demo\Service\Contents as ContentsService;
use Demo\Service\CodeProcessor;
use Demo\Service\LinkInserter;

$tunnelToDB = new ContentsService();
$contents = $tunnelToDB->getContents(basename(__FILE__));
$titles = $tunnelToDB->getEntityArray("titles");

$codes = $contents["codes"];
$codeProcessor = new CodeProcessor();
$codes = $codeProcessor->processCodes($codes);

$linkInserter = new LinkInserter();
$links = [
    "long way"       => $titles[5]["page"],
    "PDO"            => $contents["links"][0]["link"],
    "mysql\(\)"      => $contents["links"][1]["link"],
    "prepare\(\)"    => $contents["links"][2]["link"],
    "execute\(\)"    => $contents["links"][3]["link"],
    "query\(\)"      => $contents["links"][4]["link"],
    "exec\(\)"       => $contents["links"][5]["link"],
];
$contents["articles"] = $linkInserter->insertLinksIntoArticles($links, $contents["articles"]);

$loader = new \Twig_Loader_Filesystem("../../tpl");
$twig = new \Twig_Environment($loader);

echo $twig->render("DBInteraction.tpl.twig", [
    "title"        => $contents["titles"][0]["title"],
    "header"       => $contents["titles"][0]["title"],
    "articles"     => $contents["articles"],
    "header_1"     => "Details",
    "codes"        => $codes,
    "lists"        => $contents["lists"],
]);