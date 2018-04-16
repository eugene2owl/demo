<?php

namespace Demo\Controller;

require_once "../../vendor/autoload.php";
require_once "../service/Contents.php";

use Demo\Service\Contents as ContentsService;

    $loader = new \Twig_Loader_Filesystem("../../tpl");
    $twig = new \Twig_Environment($loader);

    echo $twig->render("baseTypes.tpl.twig", [

    ]);