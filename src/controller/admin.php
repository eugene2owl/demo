<?php

declare(strict_types = 1);

namespace Demo\Controller;

require_once "const.php";
require_once "../../vendor/autoload.php";
require_once "../service/Contents.php";
require_once "../service/CodeProcessor.php";
require_once "../service/AdminProcessor.php";
require_once "../service/AdminAccessor.php";

use Demo\Service\AdminAccessor;
use Demo\Service\AdminProcessor;

$adminProcessor = new AdminProcessor();
$adminAccessor = new AdminAccessor();

$adminDevelopmentAcess = $adminAccessor->verifyPassword($_POST["admin_password"]);
list($templateName, $parameters) = $adminProcessor->getTemplateNameWithParameters($adminDevelopmentAcess);

$loader = new \Twig_Loader_Filesystem(TEMPLATES_PATH_FOR_TWIG . ADMIN_TEMPLATE_DIR);
$twig = new \Twig_Environment($loader);

echo $twig->render($templateName, $parameters);
