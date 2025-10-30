<?php

use app\core\Application;
use app\core\Env;
use app\core\Config;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../app/core/helpers.php';

$ROOT_DIR = dirname(__DIR__) . '/app';

Env::load(__DIR__ . '/../.env');

Config::instance();

date_default_timezone_set(config('timezone', 'UTC'));
ini_set('display_errors', config('debug', false) ? '1' : '0');

$app = new Application($ROOT_DIR);
return $app;
