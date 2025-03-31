<?php
    namespace Core;
    require_once __DIR__ . '/../../vendor/mustache/mustache/src/Mustache/Autoloader.php';
    \Mustache_Autoloader::register();

    class Template {
        protected $mustache;

        public function __construct() {
            $mustache_extension = ['extension' => '.html'];
            $templates_path = __DIR__ .'/../views';

            $this->mustache = new \Mustache_Engine([
                "loader" => new \Mustache_Loader_FilesystemLoader($templates_path, $mustache_extension)
            ]);
        }

        public function render($template , $data = []) {
            return $this->mustache->render($template , $data);
        }
    }
?>