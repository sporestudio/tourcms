<?php
namespace Core;
use Core\Router;

class App 
{
    protected $config;
    protected $router;

    public function __construct() 
    {
        
        global $config;

        $this->config = $config;

        $this->router = new Router();
    }

    public function run() 
    {
        $this->router->dispatch();
    }
}
