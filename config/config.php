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

use App\Infrastructure\Provider\{
	RegisterConsoleCommands,
	RegisterContainerDefinitions,
	RegisterRoutes,
	RegisterErrorTracking,
	ValidateProductionConfig
};
use Webify\Base\Infrastructure\Provider\BaseServiceProvider;

return [
	'name'             => $_ENV['APP_NAME'] ?? 'WebifyCMS',
	'id'               => $_ENV['APP_ID'] ?? 'webifycms',
	'version'          => $_ENV['APP_VERSION'] ?? '0.1.0',
	'baseUrl'          => $_ENV['APP_BASE_URL'] ?? '',
	'port'             => $_ENV['NGINX_PORT'] ?? 80,
	'portSsl'          => $_ENV['NGINX_PORT_SSL'] ?? 443,
	'basePath'         => dirname(__DIR__),
	'runtimePath'      => dirname(__DIR__) . '/runtime',
	'configPath'       => dirname(__DIR__) . '/config',
	'environment'      => $_ENV['APP_ENV'] ?? 'production',
	'debug'            => filter_var($_ENV['APP_DEBUG'] ?? false, FILTER_VALIDATE_BOOLEAN),
	'providers'        => [
		ValidateProductionConfig::class,
		RegisterErrorTracking::class,
		BaseServiceProvider::class,
		RegisterContainerDefinitions::class,
		RegisterRoutes::class,
		RegisterConsoleCommands::class,
	],
	'cache'            => [
		'lifetime'   => 604800, // 7 days
		'revalidate' => 86400, // 24 hours
	],
	'extensions'       => [],
	'vite'             => [
		'devServerUrl' => $_ENV['VITE_DEV_SERVER_URL'] ?? '',
	],
	'themes'           => [],
	'mailList'         => [
		'url'      => $_ENV['MAIL_LIST_URL'] ?? '',
		'username' => $_ENV['MAIL_LIST_USERNAME'] ?? '',
		'password' => $_ENV['MAIL_LIST_PASSWORD'] ?? '',
	],
	'errorTracking'    => [
		'dsn' => $_ENV['ERROR_TRACKING_DSN'] ?? '',
	],
	'analytics'        => [
		'scriptUrl' => $_ENV['ANALYTICS_SCRIPT_URL'] ?? '',
		'websiteId' => $_ENV['ANALYTICS_WEBSITE_ID'] ?? '',
	],
];
