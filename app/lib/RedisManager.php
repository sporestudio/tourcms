<?php
/*
* Singleton class to manage Redis connections 
* and provide a single instance of RedisService.
* Backoffice project.
*/

namespace Lib;

use Lib\RedisService;

class RedisManager extends RedisService
{
    private static $instance = null;
    protected $redis;

    private function __construct($host, $port, $passwd) 
    {
        parent::__construct($host, $port, $passwd);
        $this->redis = new RedisService($host, $port, $passwd);
    }

    public static function getInstance($host, $port, $passwd) 
    {
        if (self::$instance === null) {
            self::$instance = new self($host, $port, $passwd);
        }
        return self::$instance->redis;
    }
}