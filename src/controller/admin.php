<?php

declare(strict_types = 1);

namespace Demo\Controller;

require_once "const.php";
require_once "../../vendor/autoload.php";
require_once "../service/Contents.php";
require_once "../service/CodeProcessor.php";
require_once "../service/ListProcessor.php";
require_once "../service/AdminProcessor.php";

use Demo\Service\Contents as ContentsService;
use Demo\Service\CodeProcessor;
use Demo\Service\ListProcessor;
use Demo\Service\AdminProcessor;

$adminProcessor = new AdminProcessor();
list($templateName, $parameters) = $adminProcessor->getTemplateNameWithParameters();

$loader = new \Twig_Loader_Filesystem(TEMPLATES_PATH_FOR_TWIG . ADMIN_TEMPLATE_DIR);
$twig = new \Twig_Environment($loader);

var_dump($_POST["main_option"]);
var_dump($_POST["add_option"]);

echo $twig->render($templateName, $parameters);
