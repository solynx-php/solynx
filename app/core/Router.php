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
    protected array $namedRoutes = [];
    protected ?string $currentGroupNamePrefix = null;
    protected ?string $currentGroupPrefix = null;
    protected array $currentGroupOptions = [];

    public function __construct($request, $response)
    {
        $this->request  = $request;
        $this->response = $response;
    }

    public function get($path, $callback)
    {
        $path = $this->applyGroupPrefix($path);
        $this->routes['get'][$path]     = $callback;
        return $this;
    }
    public function post($path, $callback)
    {
        $path = $this->applyGroupPrefix($path);
        $this->routes['post'][$path]    = $callback;
        return $this;
    }
    public function put($path, $callback)
    {
        $path = $this->applyGroupPrefix($path);
        $this->routes['put'][$path]     = $callback;
        return $this;
    }
    public function delete($path, $callback)
    {
        $path = $this->applyGroupPrefix($path);
        $this->routes['delete'][$path]  = $callback;
        return $this;
    }
    public function patch($path, $callback)
    {
        $path = $this->applyGroupPrefix($path);
        $this->routes['patch'][$path]   = $callback;
        return $this;
    }
    public function options($path, $callback)
    {
        $path = $this->applyGroupPrefix($path);
        $this->routes['options'][$path] = $callback;
        return $this;
    }
    public function name(string $name): self
    {
        $lastMethod = array_key_last($this->routes);
        if (!$lastMethod) return $this;

        $lastPath = array_key_last($this->routes[$lastMethod]);
        if (!$lastPath) return $this;

        $prefix = $this->currentGroupNamePrefix ?? '';
        $fullName = $prefix . $name;

        $this->namedRoutes[$fullName] = [
            'method' => $lastMethod,
            'path' => $lastPath,
            'handler' => $this->routes[$lastMethod][$lastPath]
        ];

        return $this;
    }

    public function group(array|string $attributes, callable $callback)
    {
        if (is_string($attributes)) {
            $attributes = ['prefix' => $attributes];
        }

        $previousPrefix = $this->currentGroupPrefix;
        $previousNamePrefix = $this->currentGroupNamePrefix ?? '';

        $this->currentGroupPrefix = isset($attributes['prefix'])
            ? rtrim(($previousPrefix ? $previousPrefix . '/' : '') . ltrim($attributes['prefix'], '/'), '/')
            : $previousPrefix;

        $this->currentGroupNamePrefix = isset($attributes['as'])
            ? $previousNamePrefix . $attributes['as']
            : $previousNamePrefix;

        $callback($this);

        $this->currentGroupPrefix = $previousPrefix;
        $this->currentGroupNamePrefix = $previousNamePrefix;
    }


    protected function applyGroupPrefix(string $path)
    {
        $prefix = $this->currentGroupPrefix ? '/' . trim($this->currentGroupPrefix, '/') : '';
        return rtrim($prefix . '/' . ltrim($path, '/'), '/') ?: '/';
    }


    public function dispatch()
    {
        $path   = $this->request->path();
        $method = $this->request->method();
        $callback = false;
        $params = [];

        if (isset($this->routes[$method])) {
            foreach ($this->routes[$method] as $route => $handler) {
                $pattern = preg_replace('#\{[^/]+\}#', '([^/]+)', $route);
                if (preg_match("#^{$pattern}$#", $path, $matches)) {
                    array_shift($matches);
                    $callback = $handler;
                    $params = $matches;
                    break;
                }
            }
        }

        try {
            if ($callback === false) {
                throw new NotFoundException();
            }

            $controllerResponse = match (true) {
                is_string($callback) => $this->renderView($callback),

                is_array($callback)  => $this->callController($callback, $params),

                default              => call_user_func_array($callback, $params),
            };



            $response = $this->prepareResponse($controllerResponse);
            $response->send();
        } catch (Throwable $e) {
            ErrorHandler::handleException($e);
        }
    }
    protected function callController(array $callback, array $params)
    {
        [$class, $method] = $callback;
        $controller = new $class();

        $ref = new \ReflectionMethod($controller, $method);
        $expectsRequest = false;
        if ($ref->getNumberOfParameters() > 0) {
            $firstParam = $ref->getParameters()[0];
            $expectsRequest = $firstParam->getName() === 'request';
        }

        $args = $expectsRequest
            ? array_merge([$this->request], $params)
            : $params;

        return call_user_func_array([$controller, $method], $args);
    }

    public function route(string $name, array $params = []): ?string
    {
        if (!isset($this->namedRoutes[$name])) {
            return null;
        }

        $path = $this->namedRoutes[$name]['path'];
        foreach ($params as $key => $value) {
            $path = str_replace("{{$key}}", $value, $path);
        }
        return $path;
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


    public function renderView(string $view, array $params = [])
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
