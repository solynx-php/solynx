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

    public function dispatch()
    {
        $path = $this->request->getPath();
        $method = $this->request->getMethod();
        $callback = $this->routes[$method][$path] ?? false;
        if ($callback == false) {
            $this->response->setStatusCode(404);
            return "404 Not Found";
        } elseif (is_string($callback)) {

            return $this->renderView($callback);
        } else {
            return call_user_func($this->routes[$method][$path]);
        }
    }

    protected function renderView($view)
    {
        $layoutContent = $this->layoutContent();
        $viewContent = $this->renderOnlyView($view);
        return str_replace('{{content}}', $viewContent, $layoutContent);
        $viewPath = Application::$ROOT_DIR . "/views/$view.php";
    }

    protected function layoutContent()
    {
        $layout = Application::$ROOT_DIR . "/views/layouts/main.aura.php";
        if (file_exists($layout)) {
            ob_start();
            include_once $layout;
            return ob_get_clean();
        }
        return "";
    }

    protected function renderOnlyView($view)
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
