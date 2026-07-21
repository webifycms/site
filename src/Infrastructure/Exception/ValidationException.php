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

use InvalidArgumentException;
use Throwable;
use Webify\Base\Contract\Exception\TranslatableExceptionInterface;
use Webify\Base\Contract\Translation\ExceptionTranslation;

/**
 * Thrown when a validation is failed.
 */
final class ValidationException extends InvalidArgumentException implements TranslatableExceptionInterface
{
	/**
	 * Private constructor enforces the use of the factory methods to initiate this exception.
	 */
	private function __construct(
		public readonly ExceptionTranslation $translation,
		private readonly string $errorMessage,
		private readonly ?string $field = null,
		?Throwable $previous = null,
	) {
		$message = null !== $this->field
			? sprintf('%s: %s', $this->field, $this->errorMessage)
			: $this->errorMessage;

		parent::__construct($message, 400, $previous);
	}

	/**
	 * Factory method to create a new ValidationException for a request validation failure.
	 */
	public static function forRequestValidationFailed(
		string $message,
		?string $field = null,
		?Throwable $throwable = null
	): ValidationException {
		return new self(
			new ExceptionTranslation(
				'app.validation',
				'field_validation_failed',
				[
					'field'   => $field,
					'message' => $message,
				]
			),
			$message,
			$field,
			$throwable,
		);
	}

	/**
	 * Factory method to create a new ValidationException for an invalid JSON.
	 */
	public static function forInvalidJson(
		string $message,
		?Throwable $throwable = null
	): ValidationException {
		return new self(
			new ExceptionTranslation('app.validation', 'invalid_json'),
			$message,
			null,
			$throwable
		);
	}

	/**
	 * Factory method to create a new ValidationException for an invalid JSON.
	 */
	public static function forMissingConfig(string $message): ValidationException
	{
		return new self(
			new ExceptionTranslation('app.validation', 'missing_config'),
			$message
		);
	}

	/**
	 * Returns the error message with the field, useful for the API.
	 *
	 * @return array<string, string>
	 */
	public function getError(): array
	{
		return [
			'field'   => (string) $this->field,
			'message' => $this->message,
		];
	}
}
