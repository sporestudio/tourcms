<?php
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
                return $controllerInstance->$action();
            } else {
                error_log("Router: Action $action not found in controller $controller");
                $this->handle404();
            }
        } else {
            error_log("Router: No route found for path $path");
            $this->handle404();
        }
    }

    private function handle404()
    {
        http_response_code(404);
        echo $this->template->render("404.html", []);
        exit;
    }
}