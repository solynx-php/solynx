<?php

namespace app\core;

use app\core\compilers\AuraCompiler;
use app\core\exceptions\NotFoundException;
use app\core\views\AuraView;
use Error;

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
        $path = $this->request->path();
        $method = $this->request->method();
        $callback = $this->routes[$method][$path] ?? false;
        try {
            if ($callback == false) {
                throw new NotFoundException();
            }
            if (is_string($callback)) {

                return $this->renderView($callback);
            }
            if (is_array($callback)) {
                $controller = new $callback[0]();
                $action = $callback[1];
                return call_user_func([$controller, $action], $this->request);
            }
            return call_user_func($callback, $this->request);
        } catch (\Throwable $e) {
            ErrorHandler::handleException($e);
        }
    }

    public function renderView($view, $params = [])
    {
        $layoutContent = $this->layoutContent();
        $viewContent = $this->renderOnlyView($view, $params);
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

    public function renderOnlyView($view, $params)
    {
        foreach ($params as $key => $value) {
            $$key = $value;
        }

        $root = Application::$ROOT_DIR;
        $viewFile = $root . "/views/{$view}.aura.php";
        $cacheDir = $root . "/../storage/cache";

        $compiler = new AuraCompiler($cacheDir, $root);
        $__aura   = new AuraView($compiler, $root);

        if (!is_file($viewFile)) return '';

        ob_start();
        $compiledChild = $compiler->compile($viewFile);
        include $compiledChild;
        $childOut = ob_get_clean();

        if ($__aura->hasLayout()) {
            $layoutFile = $__aura->layoutFile();
            ob_start();
            $compiledLayout = $compiler->compile($layoutFile);
            include $compiledLayout;
            return ob_get_clean();
        }
        return $childOut;
    }
}
