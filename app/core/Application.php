<?php
namespace app\core;
/**
 * Class Application
 *
 * @author Solynx
 * @version 1.0
 * @package app\core
 * 
 * */

class Application {
    public $router;
    public $request;

    public function __construct() {
        $this->request = new Request();
        $this->router = new Router($this->request);
    }

    public function run() {
        if ($this->router) {
            $this->router->dispatch();
        } else {
            echo "No router defined.";
        }
    }
}