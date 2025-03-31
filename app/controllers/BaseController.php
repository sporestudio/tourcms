<?php
    namespace Controller;
    use Core\Template;
    use Lib\RedisService;

    class BaseController {
        protected $template;
        protected $redis;

        public function __construct($REDIS_HOST, $REDIS_PORT, $REDIS_PASSWORD) {
            $this->template = new Template();
            $this->redis = new RedisService($REDIS_HOST, $REDIS_PORT, $REDIS_PASSWORD);
        }

        // Aux method to redirect
        public function redirect($url) {
            header("Location: $url");
            exit;
        }
    }
?>