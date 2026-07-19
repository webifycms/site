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

namespace App\Infrastructure\Presentation\Api;

use JsonSerializable;

/**
 * Standardized API response.
 *
 * Used by controllers to ensure a consistent response format across all API endpoints.
 */
final readonly class Response implements JsonSerializable
{
	/**
	 * @param null|array<string, mixed>  $meta
	 * @param null|array<string, string> $errors
	 */
	private function __construct(
		public readonly bool $success,
		public readonly mixed $data,
		public readonly string $message,
		public readonly ?array $meta,
		public readonly ?string $error,
		public readonly ?array $errors,
	) {}

	/**
	 * @return array{
	 *     success: bool,
	 *     data: mixed,
	 *     message: string,
	 *     meta: null|array<string, mixed>,
	 *     error: null|string,
	 *     errors: null|array<string, string>
	 * }
	 */
	public function jsonSerialize(): array
	{
		return [
			'success' => $this->success,
			'data'    => $this->data,
			'message' => $this->message,
			'meta'    => $this->meta,
			'error'   => $this->error,
			'errors'  => $this->errors,
		];
	}

	/**
	 * Create a successful response.
	 *
	 * @param array<string, mixed> $meta
	 */
	public static function success(
		mixed $data = null,
		string $message = '',
		array $meta = [],
	): self {
		return new self(
			success: true,
			data: $data,
			message: $message,
			meta: $meta,
			error: null,
			errors: null,
		);
	}

	/**
	 * Create an error response.
	 *
	 * @param array<string, string> $errors
	 */
	public static function error(
		string $error,
		int $statusCode = 400,
		array $errors = [],
		string $message = '',
	): self {
		return new self(
			success: false,
			data: null,
			message: $message,
			meta: ['statusCode' => $statusCode],
			error: $error,
			errors: [] !== $errors ? $errors : null,
		);
	}

	/**
	 * Create a validation error response.
	 *
	 * @param array<string, string> $errors
	 */
	public static function validationError(
		array $errors,
		string $message = 'Validation failed',
	): self {
		return new self(
			success: false,
			data: null,
			message: $message,
			meta: ['statusCode' => 422],
			error: 'Validation failed',
			errors: $errors,
		);
	}

	/**
	 * Create a not found response.
	 */
	public static function notFound(string $message = 'Resource not found'): self
	{
		return new self(
			success: false,
			data: null,
			message: $message,
			meta: ['statusCode' => 404],
			error: 'Not found',
			errors: null,
		);
	}

	/**
	 * Create an unauthorized response.
	 */
	public static function unauthorized(string $message = 'Unauthorized'): self
	{
		return new self(
			success: false,
			data: null,
			message: $message,
			meta: ['statusCode' => 401],
			error: 'Unauthorized',
			errors: null,
		);
	}

	/**
	 * Create a forbidden response.
	 */
	public static function forbidden(string $message = 'Forbidden'): self
	{
		return new self(
			success: false,
			data: null,
			message: $message,
			meta: ['statusCode' => 403],
			error: 'Forbidden',
			errors: null,
		);
	}

	/**
	 * Create a server error response.
	 */
	public static function serverError(string $message = 'Internal server error'): self
	{
		return new self(
			success: false,
			data: null,
			message: $message,
			meta: ['statusCode' => 500],
			error: 'Internal server error',
			errors: null,
		);
	}
}
