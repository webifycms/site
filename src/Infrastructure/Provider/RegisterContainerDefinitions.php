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

use UnexpectedValueException;
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
		$definitions = require __DIR__ . '/../definitions.php';

		if (!is_array($definitions)) {
			throw new UnexpectedValueException('The container definitions file must return an array.');
		}

		$validated = [];

		foreach ($definitions as $id => $definition) {
			if (!is_string($id)) {
				throw new UnexpectedValueException('Container definition identifiers must be strings.');
			}

			$validated[$id] = $definition;
		}

		return $validated;
	}
}
