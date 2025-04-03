<?php
namespace Controller;

use Core\Template;
use Controller\RedisController;

class BaseController 
{
    protected $template;
    protected $redis;

    public function __construct() 
    {
        global $REDIS_HOST, $REDIS_PORT, $REDIS_PASSWORD;

        if (empty($REDIS_HOST) || empty($REDIS_PORT)) {
            throw new \Exception("Redis configuration is missing.");
        }

        $this->template = new Template();
        $this->redis = RedisController::getInstance($REDIS_HOST, $REDIS_PORT, $REDIS_PASSWORD);
    }

    // Aux method to redirect
    public function redirect($url) 
    {
        header("Location: $url");
        exit;
    }
}
