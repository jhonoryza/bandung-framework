<?php

use Fajar\Bandung\Kernel\CommandKernel;
use Fajar\Bandung\Kernel\Kernel;

require_once __DIR__ . '/vendor/autoload.php';

/**
 * console command flow
 * first we register kernel to binding all dependency and command from app folder
 * then we create command kernel to handle request and response
 */

$appDir = __DIR__ . '/app';
$container = (new Kernel())->registerCommandDependency($appDir);

$app = new CommandKernel($container);
$app->run();

exit;