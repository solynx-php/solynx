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
    public static $ROOT_DIR;
    public $router;
    public $request;
    public $response;
    public static $app;

    public function __construct($ROOT_DIR) {
        self::$app = $this;
        ErrorHandler::register();
        self::$ROOT_DIR = $ROOT_DIR;
        $this->request = new Request();
        $this->response = new Response();
        $this->router = new Router($this->request, $this->response);
    }

    public function run() {
        if ($this->router) {
            echo $this->router->dispatch();
        } else {
            echo "No router defined.";
        }
    }
}