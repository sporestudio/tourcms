<?php
/* * (c) 2023 Backoffice
 * 
 * This file is responsible for handling user login and session management.
 * It uses the Redis service to store session data and manage user authentication.
 * 
 */

namespace Controller;
use Lib\RedisService;
use Controller\BaseController;

class LoginController extends BaseController 
{
    protected $config;
    
    public function __construct(array $config) 
    {
        $this->config = $config;
        parent::__construct($this->config);
    }


    public function index() 
    {
        return $this->template->render('login.html');
    }

    public function create_session() 
    {
        $login = [
            'mk_id' => $this->config['MARKETPLACE_ID'],
            'api_id' =>$this->config['API_KEY'],
            'ttl' => time() + 600
        ];
        
        $this->redis->storeItemInRedis('LOGIN', json_encode($login), RedisService::REDIS_TYPE_STRING);
    }

    public function logout()
    {
        $this->redis->deleteItemFromRedis('LOGIN', RedisService::REDIS_TYPE_STRING);
        header('Location: /login');
        exit;
    }


    public function process() 
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';

            if ($username === 'admin' && $password === 'admin') {
                error_log("LoginController: Authentication successful for username $username");
                $this->create_session();
                header('Location: /dashboard');
                exit;
            } else {
                $data = ['error' => 'Not valid credentials! Please, try again.'];
                return $this->template->render('login.html', $data);
            }
        }
    }
}