<?php

use Fajar\Bandung\Kernel\HttpKernel;
use Fajar\Bandung\Kernel\Kernel;

require_once __DIR__ . '/../vendor/autoload.php';

/**
 * http flow request and response
 * first we register kernel to binding all depedency
 * then we create http kernel to handle request and response
 */

$container = (new Kernel)->registerProviders();

$app = new HttpKernel($container);
$app->run();

exit;