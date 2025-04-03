<?php
namespace Core;

use Middleware\SessionMiddleware;

class Router 
{   
    private $controllerFactory;

    public function __construct($controllerFactory)
    {
        $this->controllerFactory = $controllerFactory;
    }
    public function dispatch() 
    {
        $middleware = new sessionMiddleware();
        $middleware->handle();

        $controller = isset($_GET['controller']) ? ucfirst($_GET['controller']) . 'Controller' : 'LoginController';
        $action = isset($_GET['action']) ? $_GET['action'] : 'index';

        try {
            $controllerInstance = $this->controllerFactory->create($controller);

            if (method_exists($controllerInstance, $action)) {
                return $controllerInstance->$action();
            } else {
                throw new \Exception("Action '$action' not found in controller '$controller'.");
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}