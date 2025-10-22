<?php

namespace app\core;

use app\core\compilers\AuraCompiler;
use app\core\exceptions\NotFoundException;
use app\core\views\AuraView;
use Throwable;

/**
 * Class Router
 *
 * @package app\core
 */
class Router
{
    protected $request;
    protected $response;
    protected array $routes = [];

    public function __construct($request, $response)
    {
        $this->request  = $request;
        $this->response = $response;
    }

    /** HTTP verbs */
    public function get($path, $callback)
    {
        $this->routes['get'][$path]     = $callback;
    }
    public function post($path, $callback)
    {
        $this->routes['post'][$path]    = $callback;
    }
    public function put($path, $callback)
    {
        $this->routes['put'][$path]     = $callback;
    }
    public function delete($path, $callback)
    {
        $this->routes['delete'][$path]  = $callback;
    }
    public function patch($path, $callback)
    {
        $this->routes['patch'][$path]   = $callback;
    }
    public function options($path, $callback)
    {
        $this->routes['options'][$path] = $callback;
    }

    /** Main dispatcher */
    public function dispatch()
    {
        $path   = $this->request->path();
        $method = $this->request->method();
        $callback = $this->routes[$method][$path] ?? false;

        try {
            if ($callback === false) throw new NotFoundException();

            if (is_string($callback)) return $this->renderView($callback);
            if (is_array($callback))  return call_user_func([new $callback[0](), $callback[1]], $this->request);
            return call_user_func($callback, $this->request);
        } catch (Throwable $e) {
            ErrorHandler::handleException($e);
        }
    }

    /** Render view with optional layout */
    public function renderView(string $view, array $params = []): string
    {
        $root = Application::$ROOT_DIR;
        $cacheDir = "$root/../storage/cache";

        $compiler = new AuraCompiler($cacheDir, $root);
        $__aura   = new AuraView($compiler, $root);

        $viewFile = "$root/views/{$view}.aura.php";
        if (!is_file($viewFile)) {
            throw new NotFoundException("View [$viewFile] not found");
        }

        // make all $params available (e.g. $user)
        extract($params, EXTR_SKIP);

        // render child view
        ob_start();
        $compiledChild = $compiler->compile($viewFile);
        include $compiledChild;
        $childOut = ob_get_clean();

        // render layout if defined
        if ($__aura->hasLayout()) {
            $layoutFile = $__aura->layoutFile();
            extract($params, EXTR_SKIP);          // ensure same variables in layout
            ob_start();
            $compiledLayout = $compiler->compile($layoutFile);
            include $compiledLayout;
            return ob_get_clean();
        }

        return $childOut;
    }


    /** Optional helper for layout content placeholder */
    public function layoutContent(): string
    {
        $layout = Application::$ROOT_DIR . "/views/layouts/main.aura.php";
        if (file_exists($layout)) {
            ob_start();
            include $layout;
            return ob_get_clean();
        }
        return '';
    }
}
