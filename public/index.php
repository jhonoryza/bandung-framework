<?php

use Fajar\Bandung\Kernel\HttpKernel;
use Fajar\Bandung\Kernel\Kernel;

require_once __DIR__ . '/../vendor/autoload.php';

/**
 * http flow request and response
 * first we load environment variable
 * then we register kernel to binding all http required dependency from app directory
 * finally we create http kernel to handle request and response
 */

$appDir = __DIR__ . '/../app';
$container = (new Kernel)
    ->loadEnv(__DIR__ . '/../')
    ->registerHttpDependency($appDir);

$app = new HttpKernel($container);
$app->run();

exit;