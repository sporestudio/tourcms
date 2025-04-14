<?php

/*
 * (c) 2023 Backoffice
 * 
 * This file is responsible for routing HTTP requests to the appropriate controllers and actions.
 * It initializes the router with a controller factory and a configuration array.
 * The addRoutes method is used to define routes, and the dispatch method handles incoming requests.
 * If a route is not found, a 404 error page is rendered.
 * 
 */

namespace Core;

use Middleware\SessionMiddleware;
use Core\Template;

class Router 
{   
    private $controllerFactory;
    private $template;
    private $routes = [];
    private $config;

    public function __construct($controllerFactory, array $config)
    {
        $this->controllerFactory = $controllerFactory;
        $this->template = new Template();
        $this->config = $config;
    }

    public function addRoutes($path, $controller, $action)
    {
        $this->routes[$path] = [
            'controller' => $controller,
            'action' => $action,
        ];
    }

    public function dispatch() 
    {
        $middleware = new sessionMiddleware($this->config);
        $middleware->handle();

        $path = $_GET['path'] ?? '/';
        $normalizedPath = '/' . ltrim($path,'/');
        error_log("Router: Dispatching path $normalizedPath");

        if (isset($this->routes[$normalizedPath])) {
            $route = $this->routes[$normalizedPath];
            $controller = $route['controller'];
            $controllerInstance = $this->controllerFactory->create($controller);
            $action = $route['action'];

            error_log("Router: Found route for path $path. Controller: $controller, Action: $action");

            if (method_exists($controllerInstance, $action)) {
                $response = $controllerInstance->$action();
                echo $response;
            } else {
                error_log("Router: Action $action not found in controller $controller");
                $response = $this->handle404();
                echo $response;
            }
        } else {
            error_log("Router: No route found for path $path");
            $response = $this->handle404();
            echo $response;
        }
    }

    private function handle404()
    {
        http_response_code(404);
        return $this->template->render("404.html", []);
    }
}