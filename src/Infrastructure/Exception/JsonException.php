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
use Throwable;
use Webify\Base\Contract\Exception\TranslatableExceptionInterface;
use Webify\Base\Contract\Translation\ExceptionTranslation;

/**
 * Thrown when JSON errors encountered.
 */
final class JsonException extends RuntimeException implements TranslatableExceptionInterface
{
	/**
	 * Private constructor enforces the use of the factory methods to initiate this exception.
	 */
	private function __construct(
		public readonly ExceptionTranslation $translation,
		string $message = '',
		?Throwable $previous = null
	) {
		parent::__construct($message, 0, $previous);
	}

	/**
	 * Factory method to create a new JsonException for a failed encoding.
	 */
	public static function forFailedEncode(string $message, Throwable $throwable): JsonException
	{
		return new self(
			new ExceptionTranslation(
				'app.json_exception',
				'convert_failed',
			),
			$message,
			$throwable
		);
	}
}
