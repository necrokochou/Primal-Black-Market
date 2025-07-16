<?php
define('BASE_PATH', realpath(__DIR__));
define('COMPONENTS_PATH', BASE_PATH . "/components");
define('TEMPLATES_PATH', BASE_PATH . "/components/templates");
define('HANDLERS_PATH', BASE_PATH . "/handlers");
define('LAYOUTS_PATH', BASE_PATH . "/layouts");
define('PAGES_PATH', BASE_PATH . "/pages");
define('STATICDATAS_PATH', BASE_PATH . "/staticData");
define('DUMMIES_PATH', BASE_PATH . "/staticData/dummies");
define('UTILS_PATH', BASE_PATH . "/utils");
define('ERRORS_PATH', BASE_PATH . "/servers");

chdir(BASE_PATH);

require 'vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(BASE_PATH);
$dotenv->load();