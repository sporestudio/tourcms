<?php
    class Router {
        public function dispatch() {
            $controller = isset($_GET['controller']) ? $_GET['controller'] . 'Controller' : 'loginController';
            $action = isset($_GET['action']) ? $_GET['action'] : 'index';

            $controllerFile = __DIR__ . '/../controllers/'. $controller .'.php';
            if (file_exists($controllerFile)) {
                require_once $controllerFile;
                $controllerInstance = new $controller();
                if(method_exists($controllerInstance, $action)) {

                } else {
                    echo "Error: Action not find.";
                }
            } else {
                echo "Error: controller not found.";
            }
        }
    }
?>