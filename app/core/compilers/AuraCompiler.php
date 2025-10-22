<?php

namespace app\core\compilers;

use app\core\views\CompilerInterface;
use app\core\compilers\Concerns\{
    CompilesEchos,
    CompilesConditionals,
    CompilesLoops,
    CompilesBlocks,
    CompilesStacks,
    CompilesDirectives
};
use InvalidArgumentException;

class AuraCompiler implements CompilerInterface
{
    use CompilesEchos,
        CompilesConditionals,
        CompilesLoops,
        CompilesBlocks,
        CompilesStacks,
        CompilesDirectives;

    protected string $path = '';
    protected string $cacheDir;
    protected string $rootDir;

    public function __construct(string $cacheDir, string $rootDir)
    {
        $this->cacheDir = rtrim($cacheDir, '/');
        $this->rootDir  = rtrim($rootDir, '/');
    }

    /**
     * Compile a file and return the compiled PHP file path.
     */
    public function compile(?string $path = null): string
    {
        if (empty($path)) {
            throw new InvalidArgumentException("Path cannot be empty");
        }

        $this->path = $path;
        if (!is_dir($this->cacheDir)) {
            mkdir($this->cacheDir, 0777, true);
        }

        $contents = file_get_contents($path);
        $compiled = $this->compileString($contents);

        $compiledPath = $this->cacheDir . '/' . md5($path) . '.php';
        file_put_contents($compiledPath, $compiled);

        return $compiledPath;
    }

    /**
     * Compile raw template string.
     */
    public function compileString(string $value): string
    {
        $result = '';

        foreach (token_get_all($value) as $token) {
            if (is_array($token)) {
                [$id, $content] = $token;

                // Apply echo replacements only to inline HTML parts
                if ($id === T_INLINE_HTML) {
                    $content = $this->compileEchos($content);
                    $content = preg_replace_callback(
                        '/\B@(\w+)([ \t]*)(\( ( (?>[^()]+) | (?3) )* \))?/x',
                        fn($m) => $this->compileDirective($m),
                        $content
                    );
                }

                $result .= $content;
            } else {
                $result .= $token;
            }
        }

        return "<?php /* compiled: {$this->path} */ ?>\n" . $result;
    }


    /**
     * Call directive-specific compiler if available.
     */
    protected function compileDirective(array $match): string
    {
        $name = $match[1];
        $expr = $match[3] ?? '';
        $method = 'compile' . ucfirst($name);

        return method_exists($this, $method)
            ? $this->$method($expr)
            : $match[0];
    }
}
