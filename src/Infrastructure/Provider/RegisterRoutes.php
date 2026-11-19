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

use App\Infrastructure\Presentation\Http\Middleware\PageCache;
use League\Route\Router;
use Psr\Container\ContainerInterface;
use UnexpectedValueException;
use Webify\Base\Application\Service\ConfigInterface;
use Webify\Base\Infrastructure\Contract\BootstrapServiceProviderInterface;

/**
 * Register routes definitions service provider.
 */
final readonly class RegisterRoutes implements BootstrapServiceProviderInterface
{
	/**
	 * {@inheritDoc}
	 */
	public function bootstrap(ContainerInterface $container): void
	{
		$config = $container->get(ConfigInterface::class);

		if (!$config instanceof ConfigInterface) {
			throw new UnexpectedValueException(sprintf(
				'Container entry "%s" must be an instance of %s.',
				ConfigInterface::class,
				ConfigInterface::class,
			));
		}

		$router = $container->get(Router::class);

		if (!$router instanceof Router) {
			throw new UnexpectedValueException(sprintf(
				'Container entry "%s" must be an instance of %s.',
				Router::class,
				Router::class,
			));
		}

		$pageCache = $container->get(PageCache::class);

		if (!$pageCache instanceof PageCache) {
			throw new UnexpectedValueException(sprintf(
				'Container entry "%s" must be an instance of %s.',
				PageCache::class,
				PageCache::class,
			));
		}

		$routesFile = $config->configPath . '/routes.php';
		$routes     = require $routesFile;

		if (!is_callable($routes)) {
			throw new UnexpectedValueException(sprintf('Routes file "%s" must return a callable.', $routesFile));
		}

		$router->middleware($pageCache);
		$routes($router, $container);
	}
}
