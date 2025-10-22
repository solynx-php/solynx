<?php

namespace app\core\log;

use Monolog\Logger as MonoLogger;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;

class Logger {
    private static $logger;

    public static function getLogger(): MonoLogger {
        if (!self::$logger) {
            $logDir = dirname(__DIR__) . '/../../storage/logs';
            if (!is_dir($logDir)) {
                mkdir($logDir, 0777, true);
            }

            $file = $logDir . '/app.log';
            $handler = new StreamHandler($file, MonoLogger::DEBUG);
            $formatter = new LineFormatter("[%datetime%] %level_name%: %message% %context%\n", "Y-m-d H:i:s");
            $handler->setFormatter($formatter);

            self::$logger = new MonoLogger('app');
            self::$logger->pushHandler($handler);
        }

        return self::$logger;
    }

    public static function info(string $message, array $context = []): void {
        self::getLogger()->info($message, $context);
    }

    public static function error(string $message, array $context = []): void {
        self::getLogger()->error($message, $context);
    }

    public static function debug(string $message, array $context = []): void {
        self::getLogger()->debug($message, $context);
    }
}
