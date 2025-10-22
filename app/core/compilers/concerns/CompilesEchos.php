<?php

namespace app\core\compilers\concerns;

trait CompilesEchos
{
    protected function compileEchos(string $c): string
    {
        $c = preg_replace('/\{\{\s*(.+?)\s*\}\}/', '<?= htmlspecialchars($1, ENT_QUOTES, "UTF-8") ?>', $c);
        $c = preg_replace('/\{\!\!\s*(.+?)\s*\!\!\}/', '<?= $1 ?>', $c);
        return $c;
    }

    protected function compilePhp($e)
    {
        return "<?php";
    }
    protected function compileEndphp()
    {
        return '?>';
    }
}
