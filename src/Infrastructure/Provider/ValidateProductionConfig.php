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

use App\Infrastructure\Exception\ConfigValidationException;
use Psr\Container\ContainerInterface;
use Webify\Base\Application\Service\ConfigInterface;
use Webify\Base\Infrastructure\Contract\BootstrapServiceProviderInterface;
use Webify\Base\Infrastructure\Environment\Environment;

/**
 * Validates production configuration on application bootstrap.
 */
final readonly class ValidateProductionConfig implements BootstrapServiceProviderInterface
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
		$errors = [];

		/** @var string $appEnv */
		$appEnv = $config->get('app.env', '');

		if ('production' !== $appEnv) {
			$errors[] = 'APP_ENV must be "production"';
		}

		/** @var string $appDebug */
		$appDebug = $config->get('app.debug', 'true');

		if ('false' !== $appDebug) {
			$errors[] = 'APP_DEBUG must be "false"';
		}

		if ([] !== $errors) {
			throw ConfigValidationException::forProductionConfig($errors);
		}
	}
}
