<?php
namespace Middleware;

use Lib\RedisService;
use Controller\RedisController;

class SessionMiddleware {
    protected $redis;
    protected $exTime = 500;

    public function __construct() {
        global $REDIS_HOST, $REDIS_PORT, $REDIS_PASSWORD;

        $this->redis = RedisController::getInstance($REDIS_HOST, $REDIS_PORT, $REDIS_PASSWORD);
        session_start();
    }

    public function handle() {
        $excludedRoutes = [
            'login/index',
            'login/process'
        ];

        $currentRoute = strtolower($_GET['controller'] ?? 'login') . '/' . strtolower($_GET['action'] ?? 'index');

        if (in_array($currentRoute, $excludedRoutes)) {
            return;
        }

        if (!isset($_SESSION['user'])) {
            $this->redirectLogin();
        }

        $userKey = 'LOGIN' . $_SESSION['user'];
        $loginJSON = $this->redis->getItemFromRedis($userKey, RedisService::REDIS_TYPE_STRING);

        if (!$loginJSON) {
            $this->redirectLogin();
        }

        $loginData = json_decode($loginJSON, true);
        if (!isset($loginData['ttl']) || time() > $loginData['ttl']) {
            session_destroy();
            $this->redirectLogin();
        }

        
        $newTtl = time() + $this->exTime;
        $loginData['ttl'] = $newTtl;
        $this->redis->storeItemInRedis($userKey, json_encode($loginData), RedisService::REDIS_TYPE_STRING);
        $this->redis->expireAt($userKey, $newTtl);
    }

    private function redirectLogin() {
        header("Location: /index.php?controller=login&action=index");
        exit;
    }
}
?>