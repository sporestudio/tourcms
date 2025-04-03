<?php
namespace Core;
use Core\Router;

class App 
{
    protected $config;
    protected $router;

    public function __construct($MARKETPLACE_ID, $API_KEY, $BASE_URL, $TIMEOUT) 
    {  
        $dependencies = [
            'MARKETPLACE_ID' => $MARKETPLACE_ID,
            'API_KEY' => $API_KEY,
            'BASE_URL' => $BASE_URL,
            'TIMEOUT' => $TIMEOUT
        ];

        $controllerFactory = new ControllerFactory($dependencies);
        $this->router = new Router($controllerFactory);
    }

    public function run() 
    {
        $this->router->dispatch();
    }
}
