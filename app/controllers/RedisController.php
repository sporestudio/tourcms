<?php
    namespace Controller;

    use Lib\RedisService;

    class RedisController {
        private static $instance = null;
        private $redis;

        private function __construct($host, $port, $passwd) {
            $this->redis = new RedisService($host, $port, $passwd);
        }

        public static function getInstance($host, $port, $passwd) {
            if (self::$instance === null) {
                self::$instance = new self($host, $port, $passwd);
            }
            return self::$instance->redis;
        }
    }
?>