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

namespace App\Infrastructure\Provider;

use Monolog\{Level, Logger};
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Sentry\Monolog\BreadcrumbHandler;
use Sentry\SentrySdk;
use Webify\Base\Application\Service\ConfigInterface;
use Webify\Base\Infrastructure\Contract\BootstrapServiceProviderInterface;
use Webify\Base\Infrastructure\Environment\Environment;

use function Sentry\init;

/**
 * Registers the Sentry error tracking SDK in production.
 *
 * In development or when no DSN is configured, Sentry is not initialized.
 */
final readonly class RegisterErrorTracking implements BootstrapServiceProviderInterface
{
	/**
	 * {@inheritDoc}
	 */
	public function bootstrap(ContainerInterface $container): void
	{
		$environment = $container->get(Environment::class);

		if (!$environment->isProduction()) {
			return;
		}

		$config = $container->get(ConfigInterface::class);

		/** @var string $dsn */
		$dsn = $config->get('errorTracking.dsn', '');

		if ('' === $dsn) {
			return;
		}

		/** @var string $release */
		$release = $config->get('version', '0.1.0');

		/** @var string $environmentName */
		$environmentName = $config->get('environment', 'production');

		init([
			'dsn'                 => $dsn,
			'release'             => $release,
			'environment'         => $environmentName,
			'enable_logs'         => true,
			'log_flush_threshold' => 5,
		]);

		/** @var Logger $logger */
		$logger = $container->get(LoggerInterface::class);

		$logger->pushHandler(new BreadcrumbHandler(SentrySdk::getCurrentHub(), Level::Error));
	}
}
