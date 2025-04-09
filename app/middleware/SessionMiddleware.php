<?php
namespace Middleware;

use Lib\RedisService;
use Controller\RedisController;

class SessionMiddleware 
{
    protected $redis;
    protected $exTime = 500;

    // TODO: pass to the construct redis as an object.
    public function __construct() 
    {
        global $REDIS_HOST, $REDIS_PORT, $REDIS_PASSWORD;

        $this->redis = RedisController::getInstance($REDIS_HOST, $REDIS_PORT, $REDIS_PASSWORD);
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
        
        $newTtl = time() + $this->exTime;
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