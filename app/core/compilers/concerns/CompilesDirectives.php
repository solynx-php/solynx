<?php

namespace app\core\compilers\concerns;

trait CompilesDirectives
{
    protected function compileUse($e)
    {
        return "<?php \$__aura->use{$e}; ?>";
    }
    protected function compileUnique($e)
    {
        return "<?php if(\$__aura->unique{$e}): ?>";
    }
    protected function compileEndunique()
    {
        return "<?php \$__aura->endUnique(); endif; ?>";
    }
}
