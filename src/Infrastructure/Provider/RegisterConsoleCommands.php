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

use App\Infrastructure\Persistence\Filesystem\PostIndexer;
use App\Infrastructure\Presentation\Console\Command\{ClearPageCache, GeneratePostsIndex};
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Application as ConsoleApplication;
use UnexpectedValueException;
use Webify\Base\Application\Service\ConfigInterface;
use Webify\Base\Infrastructure\Contract\BootstrapServiceProviderInterface;

/**
 * Register console commands.
 */
final class RegisterConsoleCommands implements BootstrapServiceProviderInterface
{
	/**
	 * {@inheritDoc}
	 */
	public function bootstrap(ContainerInterface $container): void
	{
		$consoleApplication = $container->get(ConsoleApplication::class);

		if (!$consoleApplication instanceof ConsoleApplication) {
			throw new UnexpectedValueException(sprintf(
				'Container entry "%s" must be an instance of %s.',
				ConsoleApplication::class,
				ConsoleApplication::class,
			));
		}

		$config = $container->get(ConfigInterface::class);

		if (!$config instanceof ConfigInterface) {
			throw new UnexpectedValueException(sprintf(
				'Container entry "%s" must be an instance of %s.',
				ConfigInterface::class,
				ConfigInterface::class,
			));
		}

		$indexer = $container->get(PostIndexer::class);

		if (!$indexer instanceof PostIndexer) {
			throw new UnexpectedValueException(sprintf(
				'Container entry "%s" must be an instance of %s.',
				PostIndexer::class,
				PostIndexer::class,
			));
		}

		$consoleApplication->addCommands([
			new ClearPageCache($config),
			new GeneratePostsIndex($indexer),
		]);
	}
}
