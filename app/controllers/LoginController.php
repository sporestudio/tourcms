<?php
    namespace Controller;
    use Lib\RedisService;
    use Controller\BaseController;

    class LoginController extends BaseController {
        
        public function __construct() {
           parent::__construct();
        }

        public function index() {
            echo $this->template->render('login.html');
        }

        public function create_session() {
            global $MARKETPLACE_ID, $API_KEY;

            $login = [
                'mk_id' => $MARKETPLACE_ID,
                'api_id' => $API_KEY,
                'ttl' => time() + 600
            ];
            
            
            $this->redis->storeItemInRedis('LOGIN', json_encode($login), RedisService::REDIS_TYPE_STRING);
        }


        public function process() {
            error_log(print_r($_POST, true));
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                
                $username = $_POST['username'] ?? '';
                $password = $_POST['password'] ?? '';

                if ($username === 'admin' && $password === 'admin') {
                    error_log('successfully logged');
                    $_SESSION['user'] = $username;
                    
                    $this->create_session();

                    header('Location: /app/controllers/ChannelController.php');
                    exit;
                } else {
                    $data = ['error' => 'Not valid credentials!'];
                    echo $this->template->render('login.html', $data);
                }
            }
        }
    }

?>