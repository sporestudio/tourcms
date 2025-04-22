<?php
/* 
 * (c) 2023 Backoffice
 * 
 * This file is responsible for the main application logic.
 * It initializes the router and sets up the routes for the application.
 * 
 */

namespace Core;
use Core\Router;

class App 
{
    protected $router;
    protected $config;

    public function __construct(array $config) 
    {
        $this->config = $config;  
        $dependencies = [
            'MARKETPLACE_ID' => $this->config['tourcms']['marketplace_id'],
            'API_KEY' => $this->config['tourcms']['agent_api_key'],
            'BASE_URL' => $this->config['tourcms']['base_url'],
            'TIMEOUT' => $this->config['tourcms']['timeout'],
            'REDIS_HOST' => $this->config['redis']['host'],
            'REDIS_PORT' => $this->config['redis']['port'],
            'REDIS_PASSWORD' => $this->config['redis']['password'],
        ];

        $controllerFactory = new ControllerFactory($dependencies);
        $this->router = new Router($controllerFactory, $this->config);

        // Routes
        $this->router->addRoutes('/', 'Login', 'index');
        $this->router->addRoutes('/login','Login', 'index');
        $this->router->addRoutes('/login/process', 'Login', 'process');
        $this->router->addRoutes('/dashboard','Channel','index');
        $this->router->addRoutes('/dashboard/select', 'Channel', 'select');
        $this->router->addRoutes('/tours','Tour', 'listTours');
        $this->router->addRoutes('/tours/show','Tour', 'showTour');
        $this->router->addRoutes('/tours/availability','Tour', 'availabililty');
        $this->router->addRoutes('/book', 'Booking', 'index');
        $this->router->addRoutes('/book/process', 'Booking', 'processBooking');
        $this->router->addRoutes('/book/commit', 'Booking', 'commitBooking');
        $this->router->addRoutes('/bookings','Booking', 'listBookings');
        $this->router->addRoutes('/book/show','Booking', 'showBooking');
        $this->router->addRoutes('/book/cancel','Booking', 'cancelBooking');
        $this->router->addRoutes('/logout','Login','logout');
    }

    public function run() 
    {
        return $this->router->dispatch();
    }
}