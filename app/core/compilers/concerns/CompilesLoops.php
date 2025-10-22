<?php
namespace app\core\compilers\concerns;

trait CompilesLoops
{
    protected function compileForeach($expression)
    {
        return "<?php foreach{$expression}: ?>";
    }

    protected function compileEndforeach()
    {
        return '<?php endforeach; ?>';
    }

    protected function compileFor($expression)
    {
        return "<?php for{$expression}: ?>";
    }

    protected function compileEndfor()
    {
        return '<?php endfor; ?>';
    }

    protected function compileWhile($expression)
    {
        return "<?php while{$expression}: ?>";
    }

    protected function compileEndwhile()
    {
        return '<?php endwhile; ?>';
    }

    protected function compileBreak($expression)
    {
        if ($expression) {
            preg_match('/\(\s*(-?\d+)\s*\)$/', $expression, $m);
            return $m ? '<?php break '.max(1,$m[1]).'; ?>' : "<?php if{$expression} break; ?>";
        }
        return '<?php break; ?>';
    }

    protected function compileContinue($expression)
    {
        if ($expression) {
            preg_match('/\(\s*(-?\d+)\s*\)$/', $expression, $m);
            return $m ? '<?php continue '.max(1,$m[1]).'; ?>' : "<?php if{$expression} continue; ?>";
        }
        return '<?php continue; ?>';
    }
}
