<?php

namespace app\core\compilers\concerns;

trait CompilesBlocks
{
    protected function compileBlock($e)
    {
        return "<?php \$__aura->startBlock{$e}; ?>";
    }
    protected function compileEndblock()
    {
        return "<?php \$__aura->endBlock(); ?>";
    }
    protected function compileLayout($e)
    {
        return "<?php \$__aura->extendLayout{$e}; ?>";
    }
    protected function compilePlace($e)
    {
        return "<?php echo \$__aura->placeBlock{$e}; ?>";
    }
}
