<?php

namespace app\core\compilers\concerns;

trait CompilesConditionals
{
    protected function compileIf($e)
    {
        return "<?php if{$e}: ?>";
    }
    protected function compileElseif($e)
    {
        return "<?php elseif{$e}: ?>";
    }
    protected function compileElse()
    {
        return "<?php else: ?>";
    }
    protected function compileEndif()
    {
        return "<?php endif; ?>";
    }
}
