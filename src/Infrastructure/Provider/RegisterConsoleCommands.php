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

use App\Infrastructure\Presentation\Console\Command\Home;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Application as ConsoleApplication;
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

		$consoleApplication->addCommands([
			new Home(),
		]);
	}
}
