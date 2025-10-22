<?php

namespace app\core\compilers\concerns;

trait CompilesStacks
{
    protected function compileStack($e)
    {
        return "<?php \$__aura->startStack{$e}; ?>";
    }
    protected function compileEndstack()
    {
        return "<?php \$__aura->endStack(); ?>";
    }
    protected function compilePrepend($e)
    {
        return "<?php \$__aura->startPrepend{$e}; ?>";
    }
    protected function compileEndprepend()
    {
        return "<?php \$__aura->endPrepend(); ?>";
    }
    protected function compilePull($e)
    {
        return "<?php echo \$__aura->pull{$e}; ?>";
    }
}
