<?php
namespace Core;
use Core\Router;

class App 
{
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

        // Routes
        $this->router->addRoutes('/', 'LoginController', 'index');
        $this->router->addRoutes('/login','LoginController', 'index');
        $this->router->addRoutes('/login/process', 'LoginController', 'process');
        $this->router->addRoutes('/dashboard','ChannelController','index');
        $this->router->addRoutes('/dashboard/select', 'ChannelController', 'select');
        $this->router->addRoutes('/tours','TourController', 'listTours');
        $this->router->addRoutes('/logout','LoginController','logout');
    }

    public function run() 
    {
        $this->router->dispatch();
    }
}
