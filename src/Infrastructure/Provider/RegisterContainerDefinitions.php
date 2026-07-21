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

use Webify\Base\Infrastructure\Contract\ServiceProviderInterface;

/**
 * Register container definitions service provider.
 */
final class RegisterContainerDefinitions implements ServiceProviderInterface
{
	/**
	 * {@inheritDoc}
	 */
	public function getDefinitions(): array
	{
		return require __DIR__ . '/../definitions.php';
	}
}
