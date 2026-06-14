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

use App\Infrastructure\Provider\{RegisterConsoleCommands, RegisterContainerDefinitions, RegisterRoutes};
use Webify\Base\Infrastructure\Provider\BaseServiceProvider;

return [
	'name'             => $_ENV['APP_NAME'] ?? 'WebifyCMS',
	'id'               => $_ENV['APP_ID'] ?? 'webifycms',
	'version'          => $_ENV['APP_VERSION'] ?? '0.0.1',
	'basePath'         => dirname(__DIR__),
	'runtimePath'      => dirname(__DIR__) . '/runtime',
	'configPath'       => dirname(__DIR__) . '/config',
	'environment'      => $_ENV['APP_ENV'] ?? 'production',
	'debug'            => filter_var($_ENV['APP_DEBUG'] ?? true, FILTER_VALIDATE_BOOLEAN),
	'providers'        => [
		BaseServiceProvider::class,
		RegisterContainerDefinitions::class,
		RegisterRoutes::class,
		RegisterConsoleCommands::class,
	],
	'extensions'       => [],
	'themes'           => [],
];
