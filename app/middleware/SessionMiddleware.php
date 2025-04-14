<?php
/*
 * (c) 2023 Backoffice
 *
 * This file is responsible for managing user sessions in the backoffice application.
 * It uses Redis to store session data and checks for session validity.
 * If a session is not found or has expired, the user is redirected to the login page.
 *
 * The session timeout is set to 500 seconds.
 *
 * The class is initialized with a configuration array containing Redis connection details.
 * The handle method checks the current request path against a list of excluded routes.
 * If the path is not excluded, it checks for a valid session in Redis.
 * If no session is found or the session has expired, the user is redirected to the login page.
 * If a valid session is found, the session TTL is updated in Redis.
 * The redirectLogin method is used to redirect the user to the login page.
 *
 */

namespace Middleware;

use Lib\RedisManager;
use Lib\RedisService;

class SessionMiddleware 
{
    protected $redis;
    protected const exTime = 500;

    public function __construct(array $config) 
    {
        $this->redis = RedisManager::getInstance(
            $config["redis"]["host"],
            $config["redis"]["port"],
            $config["redis"]["password"]
        );
        session_start();
    }

    public function handle() 
    {
        $excludedRoutes = [
            '/',
            '/login',
            '/login/process',
            '/logout'
        ];
    
        $currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        error_log("SessionMiddleware: Current path is $currentPath");
    
        if (in_array($currentPath, $excludedRoutes)) {
            error_log("SessionMiddleware: Path $currentPath is excluded from session check.");
            return;
        }
    
        $loginJSON = $this->redis->getItemFromRedis('LOGIN', RedisService::REDIS_TYPE_STRING);
        if (!$loginJSON) {
            error_log("SessionMiddleware: No session found. Redirecting to /login.");
            $this->redirectLogin();
        }

        $loginData = json_decode($loginJSON, true);
        if (!isset($loginData['ttl']) || time() > $loginData['ttl']) {
            $this->redis->deleteItemFromRedis('LOGIN', RedisService::REDIS_TYPE_STRING);
            $this->redirectLogin();
        }
        
        $newTtl = time() + self::exTime;
        $loginData['ttl'] = $newTtl;
        $this->redis->storeItemInRedis('LOGIN', json_encode($loginData), RedisService::REDIS_TYPE_STRING);
        $this->redis->expireAt('LOGIN', $newTtl);
    }

    private function redirectLogin() 
    {
        header("Location: /login");
        exit;
    }
}