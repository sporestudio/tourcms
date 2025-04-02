<?php
namespace Core;

use Middleware\SessionMiddleware;

class Router 
{
    public function dispatch() 
    {
        $middleware = new sessionMiddleware();
        $middleware->handle();

        $controller = isset($_GET['controller']) ? ucfirst($_GET['controller']) . 'Controller' : 'LoginController';
        $action = isset($_GET['action']) ? $_GET['action'] : 'index';

        $controllerClass = "\\Controller\\" . $controller;
        $controllerFile = __DIR__ . '/../controllers/' . $controller . '.php';

        if (file_exists($controllerFile)) {
            require_once $controllerFile;

            
            if (class_exists($controllerClass)) {
                $controllerInstance = new $controllerClass();

                if (method_exists($controllerInstance, $action)) 
                {
                    return $controllerInstance->$action();
                } else {
                    throw new \Exception("Action '$action' not found in controller '$controllerClass'.");
                }
            } else {
                throw new \Exception("Controller class '$controllerClass' not found.");
            }
        } else {
            throw new \Exception("Controller file '$controllerFile' not found.");
        }
    }
}