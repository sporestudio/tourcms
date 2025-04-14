<?php
// Add composer autoload
require_once __DIR__ . '/vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Add mustache loader
Mustache_Autoloader::register();
// use .html instead of .mustache for default template extension
$MUSTACHE_OPTIONS =  array('extension' => '.html');

// Redis configuration
$REDIS_HOST = $_ENV['REDIS_HOST'];
$REDIS_PORT = $_ENV['REDIS_PORT'];
$REDIS_PASSWORD = $_ENV['REDIS_PASSWORD'];

// Login credentials
//const USER = 'admin';
//const PASSWD = 'admin';

// Credentials tokens
$API_KEY = $_ENV['API_KEY'];
$AGENT_API_KEY = $_ENV['AGENT_API_KEY'];

// General values
$BASE_URL = $_ENV['BASE_URL'];
$MARKETPLACE_ID = $_ENV['MARKETPLACE_ID'];
$CHANNEL_ID = $_ENV['CHANNEL_ID'];
$TIMEOUT = $_ENV['TIMEOUT'];

return [
    'redis' => [
        'host' => $REDIS_HOST,
        'port' => $REDIS_PORT,
        'password' => $REDIS_PASSWORD,
    ],
    'tourcms' => [
        'api_key' => $API_KEY,
        'agent_api_key' => $AGENT_API_KEY,
        'base_url' => $BASE_URL,
        'marketplace_id' => $MARKETPLACE_ID,
        'channel_id' => $CHANNEL_ID,
        'timeout' => $TIMEOUT,
    ],
];