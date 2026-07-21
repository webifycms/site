<?php

/**
 * The file is part of the "webifycms/site", WebifyCMS site.
 *
 * @see https://webifycms.com
 *
 * @copyright Copyright (c) 2026 WebifyCMS
 * @license https://webifycms.com/license
 * @author Mohammed Shifreen <mshifreen@gmail.com>
 */
declare(strict_types=1);

use App\Dev\DevelopmentServiceProvider;
use Dotenv\Dotenv;
use Webify\Base\Infrastructure\Container\PhpDiContainerBuilder;
use Webify\Base\Infrastructure\Environment\Environment;
use Webify\Base\Infrastructure\Service\{Application, Config};

// Register Composer autoloader
require __DIR__ . '/../vendor/autoload.php';

// Load environment variables from .env file
Dotenv::createImmutable(__DIR__ . '/..')->safeLoad();

// Load configuration and create application instance
$config      = new Config(require __DIR__ . '/../config/config.php');
$environment = Environment::prepare($config);
$app         = new Application($config, $environment);

// Register development service provider if in development environment
if ($environment->isDevelopment() && class_exists(DevelopmentServiceProvider::class)) {
	$app->registerProvider(new DevelopmentServiceProvider());
}

// Bootstrap application and run it
$app->bootstrap(new PhpDiContainerBuilder());
$app->run();
