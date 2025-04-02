<?php
namespace Core;

class Template 
{
    protected $mustache;

    public function __construct() 
    {
        $mustache_extension = ['extension' => '.html'];
        $templates_path = __DIR__ .'/../views';
        $partials_path = __DIR__ .'/../views/partials';

        $this->mustache = new \Mustache_Engine([
            "loader" => new \Mustache_Loader_FilesystemLoader($templates_path, $mustache_extension),
            "partials_loader" => new \Mustache_Loader_FilesystemLoader($partials_path, $mustache_extension),
        ]);
    }

    public function render($template , $data = []) 
    {
        return $this->mustache->render($template , $data);
    }
}