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
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Container\ContainerInterface;
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
		$config  = $container->get(ConfigInterface::class);
		$router  = $container->get(Router::class);
		$factory = $container->get(Psr17Factory::class);
		$routes  = require $config->configPath . '/routes.php';

		$router->middleware($container->get(PageCache::class));
		$routes($router, $container);
	}
}
