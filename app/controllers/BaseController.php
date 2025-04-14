<?php
/* * (c) 2023 Backoffice
 * 
 * This file is responsible for the base controller class.
 * It initializes the template and Redis connection.
 * 
 */

namespace Controller;

use Core\Template;
use Lib\RedisManager;

class BaseController 
{
    protected $template;
    protected $redis;
    protected $config;

    public function __construct(array $config) 
    {
        $this->config = $config;
        $this->template = new Template();
        $this->redis = RedisManager::getInstance(
            $this->config['REDIS_HOST'],
            $this->config['REDIS_PORT'],
            $this->config['REDIS_PASSWORD']
        );
    }

    // Aux method to redirect
    public function redirect($url) 
    {
        header("Location: $url");
        exit;
    }
}
