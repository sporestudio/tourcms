<?php
require_once __DIR__ ."/vendor/autoload.php";
require_once __DIR__ . "/config.php";

use Core\App;

$app = new App($MARKETPLACE_ID, $AGENT_API_KEY, $BASE_URL, $TIMEOUT);
$app->run();