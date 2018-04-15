<?php
    require_once "../../vendor/autoload.php";

    $loader = new Twig_Loader_Filesystem("../../tpl");
    $twig = new Twig_Environment($loader);

    echo $twig->render("baseTypes.tpl.twig", [

    ]);