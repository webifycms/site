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

namespace App\Infrastructure\Exception;

use RuntimeException;
use Webify\Base\Contract\Exception\TranslatableExceptionInterface;
use Webify\Base\Contract\Translation\ExceptionTranslation;

/**
 * Thrown when application configuration validation fails during bootstrap.
 */
final class ConfigValidationException extends RuntimeException implements TranslatableExceptionInterface
{
	/**
	 * Private constructor enforces the use of the factory methods to initiate this exception.
	 */
	private function __construct(
		public readonly ExceptionTranslation $translation,
		string $message = ''
	) {
		parent::__construct($message);
	}

	/**
	 * Factory method to create a new ConfigValidationException for production config failures.
	 *
	 * @param list<string> $errors Human-readable list of configuration errors
	 */
	public static function forProductionConfig(array $errors): ConfigValidationException
	{
		return new self(
			new ExceptionTranslation(
				'app.config',
				'config_validation_failed',
			),
			'Production configuration validation failed: ' . implode(', ', $errors)
		);
	}
}
