<?php
namespace app\core\views;

interface CompilerInterface
{
    /**
     * Compile a file and return the compiled PHP path.
     */
    public function compile(?string $path = null): string;

    /**
     * Compile a raw template string.
     */
    public function compileString(string $value): string;
}
