<?php

namespace app\core;

use Throwable;

class ErrorHandler
{
    public static function register()
    {
        set_exception_handler([self::class, 'handleException']);
        set_error_handler([self::class, 'handleError']);
        register_shutdown_function([self::class, 'handleShutdown']);
    }

    public static function handleException(Throwable $e)
    {
        $status = (int) ($e->getCode() ?: 500);
        http_response_code($status);

        $exceptionClass = get_class($e);
        $message = htmlspecialchars($e->getMessage());
        $file = htmlspecialchars($e->getFile());
        $line = $e->getLine();
        $trace = htmlspecialchars($e->getTraceAsString());
        $timestamp = date('Y-m-d H:i:s');

        if (self::isDebugMode()) {
            self::renderErrorPage($exceptionClass, $message, $file, $line, $trace, $timestamp);
        } else {
            self::renderSimpleError();
        }
    }

    public static function handleError($errno, $errstr, $errfile, $errline)
    {
        throw new \ErrorException($errstr, 0, $errno, $errfile, $errline);
    }

    public static function handleShutdown()
    {
        $error = error_get_last();
        if ($error && in_array($error['type'], [E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_PARSE])) {
            $exception = new \ErrorException(
                $error['message'],
                0,
                $error['type'],
                $error['file'],
                $error['line']
            );
            self::handleException($exception);
        }
    }


    private static function isDebugMode()
    {
        if (function_exists('env')) {
            return env('APP_DEBUG', true);
        }
        return true;
    }

    private static function renderErrorPage($exceptionClass, $message, $file, $line, $trace, $timestamp)
    {
        $templatePath = __DIR__ . '/views/error.php';

        if (file_exists($templatePath)) {
            require $templatePath;
        } else {
            echo "<h1>Error</h1>";
            echo "<p>Exception: {$exceptionClass}</p>";
            echo "<p>Message: {$message}</p>";
            echo "<p>File: {$file} : {$line}</p>";
        }
    }

    private static function renderSimpleError()
    {
        $templatePath = __DIR__ . '/views/error-simple.php';

        if (file_exists($templatePath)) {
            require $templatePath;
        } else {
            echo "<h1>500 - Server Error</h1>";
            echo "<p>Something went wrong. Please try again later.</p>";
        }
    }
}
