<?php
/* * (c) 2023 Backoffice
 * 
 * This file is responsible for creating instances of controllers based on the provided controller name.
 * It uses a switch statement to determine which controller to instantiate and returns the instance.
 * 
 */

namespace Core;

use Controller\ChannelController;
use Controller\CustomerController;
use Controller\LoginController;
use Controller\TourController;
use Controller\BookingController;

class ControllerFactory 
{
    public const CHANNEL_CONTROLLER = 'Channel';
    public const TOUR_CONTROLLER = 'Tour';
    public const BOOKING_CONTROLLER = 'Booking';
    public const LOGIN_CONTROLLER = 'Login';
    public const CUSTOMER_CONTROLLER = 'Customer';
    private $config;

    public function __construct(array $config) 
    {
        $this->config = $config;
    }

    public function create($controllerName)
    {
        switch ($controllerName) {
            case self::CHANNEL_CONTROLLER:
                return new ChannelController(
                    $this->config
                );
            case self::TOUR_CONTROLLER:
                return new TourController(
                    $this->config
                );
            case self::BOOKING_CONTROLLER:
                return new BookingController(
                    $this->config
                );
            case self::LOGIN_CONTROLLER:
                return new LoginController(
                    $this->config
                );
            case self::CUSTOMER_CONTROLLER:
                return new CustomerController(
                    $this->config
                );
            default:
                throw new \Exception("Controller '$controllerName' not found.");
        }
    }
}