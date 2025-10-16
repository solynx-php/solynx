<?php
namespace app\core;
/**
 * Class Router
 *
 * @author Solynx
 * @version 1.0
 * @package app\core
 * 
 * */

class Router {
    public $request;
    protected $routes = [];

    public function __construct($request) {
        $this->request = $request;
    }

    public function get($path, $callback) {
        $this->routes['get'][$path] = $callback;
    }

    public function post($path, $callback) {
        $this->routes['post'][$path] = $callback;
    }

    public function dispatch() {
        $path = $this->request->getPath();
        $method = $this->request->getMethod();
        $callback = $this->routes[$method][$path] ?? false;
        if ($callback == false) {
            http_response_code(404);
            echo "404 Not Found";
        } else {
            call_user_func($this->routes[$method][$path]);
        }
    }
}