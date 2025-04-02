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
            'login/index',
            'login/process'
        ];

        $currentRoute = strtolower($_GET['controller'] ?? 'login') . '/' . strtolower($_GET['action'] ?? 'index');

        if (in_array($currentRoute, $excludedRoutes)) 
        {
            return;
        }

        $loginJSON = $this->redis->getItemFromRedis('LOGIN', RedisService::REDIS_TYPE_STRING);
        if (!$loginJSON) {
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
        header("Location: /index.php?controller=login&action=index");
        exit;
    }
}