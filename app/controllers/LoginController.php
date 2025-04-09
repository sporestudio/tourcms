<?php
namespace Controller;
use Lib\RedisService;
use Controller\BaseController;

class LoginController extends BaseController 
{
    
    public function __construct() 
    {
        parent::__construct();
    }


    public function index() 
    {
        echo $this->template->render('login.html');
    }

    public function create_session() 
    {
        $login = [
            'mk_id' => $GLOBALS['MARKETPLACE_ID'],
            'api_id' =>$GLOBALS['API_KEY'],
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
                echo $this->template->render('login.html', $data);
            }
        }
    }
}