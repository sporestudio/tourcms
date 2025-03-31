<?php
    namespace Controller;
    use Lib\RedisService;
    use Controller\BaseController;

    class LoginController extends BaseController {
        
        public function __construct() {
            global $REDIS_HOST, $REDIS_PORT, $REDIS_PASSWORD;
            parent::__construct($REDIS_HOST, $REDIS_PORT, $REDIS_PASSWORD);
        }

        public function index() {
            echo $this->template->render('login.html');
        }

        public function check_login() {
            global $MARKETPLACE_ID, $API_KEY;

            $login = [
                'mk_id' => $MARKETPLACE_ID,
                'api_id' => $API_KEY,
                'ttl' => time() + 600
            ];
            
            
            $this->redis->storeItemInRedis('LOGIN', json_encode($login), RedisService::REDIS_TYPE_STRING);

            $login_str = $this->redis->getItemFromRedis('LOGIN', RedisService::REDIS_TYPE_STRING);
            $login = json_decode($login_str, true);
        }


        public function process() {
            error_log(print_r($_POST, true));
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                
                $username = $_POST['username'] ?? '';
                $password = $_POST['password'] ?? '';

                if ($username === 'admin' && $password === 'admin') {
                    error_log('successfully logged');
                    $_SESSION['user'] = $username;

                    // Update or create keys redis session
                    $this->check_login();

                    header('Location: /app/controllers/ChannelController.php');
                    exit;
                } else {
                    $data = ['error' => 'Not valid credentials!'];
                    echo $this->template->render('login.html', $data);
                }
            }
        }
    }


    // Process action depends on http method
    $controller = new LoginController();

    error_log(print_r($_SERVER, true));
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $controller->process();
    } else {
        $controller->index();
    }
?>