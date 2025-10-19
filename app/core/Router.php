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

class Router
{
    public $request;
    public $response;
    protected $routes = [];

    public function __construct($request, $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    public function get($path, $callback)
    {
        $this->routes['get'][$path] = $callback;
    }

    public function post($path, $callback)
    {
        $this->routes['post'][$path] = $callback;
    }

    public function put($path, $callback)
    {
        $this->routes['put'][$path] = $callback;
    }

    public function delete($path, $callback)
    {
        $this->routes['delete'][$path] = $callback;
    }

    public function patch($path, $callback)
    {
        $this->routes['patch'][$path] = $callback;
    }

    public function options($path, $callback)
    {
        $this->routes['options'][$path] = $callback;
    }

    

    public function dispatch()
    {
        $path = $this->request->getPath();
        $method = $this->request->getMethod();
        $callback = $this->routes[$method][$path] ?? false;
        if ($callback == false) {
            $this->response->setStatusCode(404);
            return "404 Not Found";
        } 
        if (is_string($callback)) {

            return $this->renderView($callback);
        }
        if (is_array($callback)) {
            $controller = new $callback[0]();
            $action = $callback[1];
            return call_user_func([$controller, $action]);
        }
        return call_user_func($callback);
        
    }

    public function renderView($view)
    {
        $layoutContent = $this->layoutContent();
        $viewContent = $this->renderOnlyView($view);
        return str_replace('{{content}}', $viewContent, $layoutContent);
        $viewPath = Application::$ROOT_DIR . "/views/$view.php";
    }

    public function layoutContent()
    {
        $layout = Application::$ROOT_DIR . "/views/layouts/main.aura.php";
        if (file_exists($layout)) {
            ob_start();
            include_once $layout;
            return ob_get_clean();
        }
        return "";
    }

    public function renderOnlyView($view)
    {
        $viewPath = Application::$ROOT_DIR . "/views/$view.aura.php";
        if (file_exists($viewPath)) {
            ob_start();
            include_once $viewPath;
            return ob_get_clean();
        }
        return "";
    }
}
