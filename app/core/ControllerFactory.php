<?php
namespace Core;

use Controller\BaseController;
use Controller\ChannelController;
use Controller\LoginController;

class ControllerFactory 
{
    private $dependencies;

    public function __construct($dependencies) 
    {
        $this->dependencies = $dependencies;
    }

    public function create($controllerName)
    {
        switch ($controllerName) {
            case "ChannelController":
                return new ChannelController(
                    $this->dependencies["MARKETPLACE_ID"],
                    $this->dependencies["API_KEY"],
                    $this->dependencies["BASE_URL"],
                    $this->dependencies["TIMEOUT"]
                );
            case "LoginController":
                return new LoginController();
            default:
                throw new \Exception("Controller '$controllerName' not found.");
        }
    }
}