<?php

namespace app\core\views;

use app\core\compilers\AuraCompiler;

class AuraView
{
    private array $blocks = [];
    private array $blockStack = [];
    private ?string $layout = null;

    private array $stacks = [];        
    private array $stackBuffer = [];   
    private array $prepends = [];      
    private array $prependBuffer = [];

    private array $onceKeys = [];
    private array $onceStack = [];

    public function __construct(private AuraCompiler $compiler, private string $rootDir) {}

    public function extendLayout(string $name)
    {
        $this->layout = $name;
    }
    public function hasLayout()
    {
        return $this->layout !== null;
    }
    public function layoutFile()
    {
        return $this->rootDir . "/views/layouts/{$this->layout}.aura.php";
    }

    public function startBlock(string $name)
    {
        $this->blockStack[] = $name;
        ob_start();
    }
    public function endBlock()
    {
        $name = array_pop($this->blockStack);
        $this->blocks[$name] = ob_get_clean();
    }
    public function placeBlock(string $name, string $default = '')
    {
        if (array_key_exists($name, $this->blocks)) {
            return $this->blocks[$name];
        }

        if (isset($GLOBALS[$name])) {
            return $GLOBALS[$name];
        }

        return $default;
    }

    public function use(string $view, array $with = [], array $scope = [])
    {
        extract($scope, EXTR_SKIP);
        foreach ($with as $k => $v) {
            $$k = $v;
        }
        $__aura = $this; 
        $file = $this->rootDir . "/views/{$view}.aura.php";
        if (!is_file($file)) return;
        $compiled = $this->compiler->compile($file);
        include $compiled;
    }

    public function startStack(string $name)
    {
        $this->stackBuffer[] = $name;
        ob_start();
    }
    public function endStack()
    {
        $name = array_pop($this->stackBuffer);
        $this->stacks[$name][] = ob_get_clean();
    }
    public function startPrepend(string $name)
    {
        $this->prependBuffer[] = $name;
        ob_start();
    }
    public function endPrepend()
    {
        $name = array_pop($this->prependBuffer);
        array_unshift($this->stacks[$name], ob_get_clean());
    }
    public function pull(string $name)
    {
        $items = $this->stacks[$name] ?? [];
        return implode("", $items);
    }

    public function unique(string $key)
    {
        if (!empty($this->onceKeys[$key])) return false;
        $this->onceKeys[$key] = true;
        $this->onceStack[] = $key;
        return true;
    }
    public function endUnique()
    {
        array_pop($this->onceStack);
    }
}
