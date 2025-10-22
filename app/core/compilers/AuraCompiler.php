<?php

namespace app\core\compilers;

class AuraCompiler
{
    public function __construct(private string $cachePath, private string $rootDir)
    {
        if (!is_dir($cachePath)) mkdir($cachePath, 0777, true);
    }

    public function compile(string $viewFile): string
    {
        $src = file_get_contents($viewFile);
        $php = $this->compileStatements($src, $viewFile);
        $out = $this->cachePath . '/' . md5($viewFile) . '.php';
        file_put_contents($out, $php);
        return $out;
    }

    private function compileStatements(string $c, string $file): string
    {
        $c = preg_replace('/\{\{\s*(.+?)\s*\}\}/', '<?= htmlspecialchars($1, ENT_QUOTES, "UTF-8") ?>', $c);
        $c = preg_replace('/\{\!\!\s*(.+?)\s*\!\!\}/', '<?= $1 ?>', $c);

        $c = preg_replace('/@if\s*\((.+?)\)/', '<?php if ($1): ?>', $c);
        $c = preg_replace('/@elseif\s*\((.+?)\)/', '<?php elseif ($1): ?>', $c);
        $c = str_replace('@else', '<?php else: ?>', $c);
        $c = str_replace('@endif', '<?php endif; ?>', $c);

        $c = preg_replace('/@foreach\s*\((.+?)\)/', '<?php foreach ($1): ?>', $c);
        $c = str_replace('@endforeach', '<?php endforeach; ?>', $c);
        $c = preg_replace('/@for\s*\((.+?)\)/', '<?php for ($1): ?>', $c);
        $c = str_replace('@endfor', '<?php endfor; ?>', $c);
        $c = preg_replace('/@while\s*\((.+?)\)/', '<?php while ($1): ?>', $c);
        $c = str_replace('@endwhile', '<?php endwhile; ?>', $c);

        $c = preg_replace('/@block\s*\(\s*[\'"](.+?)[\'"]\s*\)/', '<?php $__aura->startBlock("$1"); ?>', $c);
        $c = str_replace('@endblock', '<?php $__aura->endBlock(); ?>', $c);
        $c = preg_replace(
            '/@place\s*\(\s*[\'"](.+?)[\'"]\s*(?:,\s*[\'"](.+?)[\'"])?\s*\)/',
            '<?= $__aura->placeBlock("$1", "$2" ?? ""); ?>',
            $c
        );
        $c = preg_replace('/@layout\s*\(\s*[\'"](.+?)[\'"]\s*\)/', '<?php $__aura->extendLayout("$1"); ?>', $c);

        $c = preg_replace(
            '/@use\s*\(\s*[\'"](.+?)[\'"]\s*(?:,\s*(.+?)\s*)?\)/',
            '<?php $__aura->use("$1", (isset($2)?$2:[]), get_defined_vars()); ?>',
            $c
        );

        $c = preg_replace('/@stack\s*\(\s*[\'"](.+?)[\'"]\s*\)/', '<?php $__aura->startStack("$1"); ?>', $c);
        $c = str_replace('@endstack', '<?php $__aura->endStack(); ?>', $c);
        $c = preg_replace('/@prepend\s*\(\s*[\'"](.+?)[\'"]\s*\)/', '<?php $__aura->startPrepend("$1"); ?>', $c);
        $c = str_replace('@endprepend', '<?php $__aura->endPrepend(); ?>', $c);
        $c = preg_replace('/@pull\s*\(\s*[\'"](.+?)[\'"]\s*\)/', '<?= $__aura->pull("$1"); ?>', $c);

        $c = preg_replace('/@unique\s*\(\s*[\'"](.+?)[\'"]\s*\)/', '<?php if($__aura->unique("$1")): ?>', $c);
        $c = str_replace('@endunique', '<?php $__aura->endUnique(); endif; ?>', $c);

        $c = str_replace('@php', '<?php', $c);
        $c = str_replace('@endphp', '?>', $c);

        $banner = "<?php /* compiled: " . addslashes($file) . " */ ?>\n";
        return $banner . $c;
    }
}
