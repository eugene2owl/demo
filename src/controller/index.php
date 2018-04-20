<?php

declare(strict_types = 1);

namespace Demo\Controller;

require_once "../../vendor/autoload.php";
require_once "../service/Contents.php";
require_once "../service/LinkInserter.php";

use Demo\Service\Contents as ContentsService;
use Demo\Service\LinkInserter;

$tunnelToDB = new ContentsService();
$contents = $tunnelToDB->getContents(basename(__FILE__));
$titles = $tunnelToDB->getEntityArray("titles");

/*              /                 */
$newTitles = $tunnelToDB->getEntityFromPage(basename(__FILE__), "link");
var_dump($newTitles);
/*              /                 */

$linkInserter = new LinkInserter();
$links = [
    "MVC conception"                            => $titles[5]["page"],
    "interaction with a database"               => $titles[4]["page"],
    "form processing"                           => $titles[3]["page"],
    "high level solutions of trivial issues"    => $titles[1]["page"],
    "writing your own functions"                => $titles[2]["page"],
    "issues of database design"                 => $titles[6]["page"],
];
$contents["articles"] = $linkInserter->insertLinksIntoArticles($links, $contents["articles"]);

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