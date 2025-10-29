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
    protected ?string $currentGroupPrefix = null;

    public function __construct($request, $response)
    {
        $this->request  = $request;
        $this->response = $response;
    }

    public function get($path, $callback)
    {
        $path = $this->applyGroupPrefix($path);
        $this->routes['get'][$path]     = $callback;
    }
    public function post($path, $callback)
    {
        $path = $this->applyGroupPrefix($path);
        $this->routes['post'][$path]    = $callback;
    }
    public function put($path, $callback)
    {
        $path = $this->applyGroupPrefix($path);
        $this->routes['put'][$path]     = $callback;
    }
    public function delete($path, $callback)
    {
        $path = $this->applyGroupPrefix($path);
        $this->routes['delete'][$path]  = $callback;
    }
    public function patch($path, $callback)
    {
        $path = $this->applyGroupPrefix($path);
        $this->routes['patch'][$path]   = $callback;
    }
    public function options($path, $callback)
    {
        $path = $this->applyGroupPrefix($path);
        $this->routes['options'][$path] = $callback;
    }

    public function group(string $prefix, callable $callback): void
    {
        $previous = $this->currentGroupPrefix;
        $this->currentGroupPrefix = rtrim(($previous ?? '') . '/' . ltrim($prefix, '/'), '/');
        $callback($this);
        $this->currentGroupPrefix = $previous;
    }

    protected function applyGroupPrefix(string $path): string
    {
        $prefix = $this->currentGroupPrefix ? '/' . trim($this->currentGroupPrefix, '/') : '';
        return rtrim($prefix . '/' . ltrim($path, '/'), '/') ?: '/';
    }


    public function dispatch()
    {
        $path   = $this->request->path();
        $method = $this->request->method();
        $callback = $this->routes[$method][$path] ?? false;

        try {
            if ($callback === false) throw new NotFoundException();

            $controllerResponse = match (true) {
                is_string($callback) => $this->renderView($callback),
                is_array($callback)  => call_user_func([new $callback[0](), $callback[1]], $this->request),
                default              => call_user_func($callback, $this->request),
            };

            $response = $this->prepareResponse($controllerResponse);
            $response->send();
        } catch (Throwable $e) {
            ErrorHandler::handleException($e);
        }
    }

    protected function prepareResponse($data): Response
    {
        if ($data instanceof Response) {
            return $data;
        }

        if (is_array($data)) {
            $mapped = array_map(function ($item) {
                if (is_object($item) && method_exists($item, 'toArray')) {
                    return $item->toArray();
                }
                return $item;
            }, $data);
            return (new Response())->json($mapped);
        }


        if (is_object($data) && method_exists($data, 'toArray')) {
            return (new Response())->json($data->toArray());
        }

        if (is_string($data)) {
            return new Response($data, 200, ['Content-Type' => 'text/html']);
        }

        return (new Response())->json($data);
    }


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

        extract($params, EXTR_SKIP);

        ob_start();
        $compiledChild = $compiler->compile($viewFile);
        include $compiledChild;
        $childOut = ob_get_clean();

        if ($__aura->hasLayout()) {
            $layoutFile = $__aura->layoutFile();
            extract($params, EXTR_SKIP);
            ob_start();
            $compiledLayout = $compiler->compile($layoutFile);
            include $compiledLayout;
            return ob_get_clean();
        }

        return $childOut;
    }
}
