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

namespace App\Dev;

use Psr\Container\ContainerInterface;
use Webify\Base\Infrastructure\Contract\{BootstrapServiceProviderInterface, ServiceProviderInterface};
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;

/**
 * Development service provider that registers the Whoops error handler only in development mode.
 */
final class DevelopmentServiceProvider implements ServiceProviderInterface, BootstrapServiceProviderInterface
{
	/**
	 * {@inheritDoc}
	 */
	public function getDefinitions(): array
	{
		return [];
	}

	/**
	 * {@inheritDoc}
	 */
	public function bootstrap(ContainerInterface $container): void
	{
		new Run()
			->pushHandler(new PrettyPageHandler())
			->register()
		;
	}
}
