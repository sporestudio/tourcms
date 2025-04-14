<?php
require_once __DIR__ ."/vendor/autoload.php";
$config = include __DIR__ ."/config.php";

use Core\App;

$app = new App($config);
$app->run();