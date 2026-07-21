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

use App\Infrastructure\Exception\JsonException as AppJsonException;
use JsonException;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\ResponseInterface;

/**
 * Builds standardized API responses using the Response class.
 */
final readonly class ResponseBuilder
{
	/**
	 * @param Psr17Factory $psr17Factory For creating PSR-7 responses
	 */
	public function __construct(
		private Psr17Factory $psr17Factory,
	) {}

	/**
	 * Return a successful response (200 OK).
	 *
	 * @param array<string, mixed> $meta
	 */
	public function ok(
		mixed $data = null,
		string $message = 'Success',
		array $meta = [],
	): ResponseInterface {
		return $this->jsonResponse(
			Response::success($data, $message, $meta),
			200
		);
	}

	/**
	 * Return a created response (201 Created).
	 *
	 * @param array<string, mixed> $meta
	 */
	public function created(
		mixed $data = null,
		string $message = 'Resource created',
		array $meta = [],
	): ResponseInterface {
		return $this->jsonResponse(
			Response::success($data, $message, $meta),
			201
		);
	}

	/**
	 * Return a no-content response (204 No Content).
	 */
	public function noContent(): ResponseInterface
	{
		return $this->psr17Factory->createResponse(204);
	}

	/**
	 * Return an error response with a custom status code.
	 *
	 * @param array<string, string> $errors
	 */
	public function error(
		string $error,
		int $statusCode = 400,
		string $message = '',
		array $errors = [],
	): ResponseInterface {
		return $this->jsonResponse(
			Response::error($error, $statusCode, $errors, $message),
			$statusCode
		);
	}

	/**
	 * Return a validation error response (422 Unprocessable Entity).
	 *
	 * @param array<string, string> $errors
	 */
	public function validationError(
		array $errors,
		string $message = 'Validation failed',
	): ResponseInterface {
		return $this->jsonResponse(
			Response::validationError($errors, $message),
			422
		);
	}

	/**
	 * Return a not found response (404).
	 */
	public function notFound(string $message = 'Resource not found'): ResponseInterface
	{
		return $this->jsonResponse(Response::notFound($message), 404);
	}

	/**
	 * Return an unauthorized response (401).
	 */
	public function unauthorized(string $message = 'Unauthorized'): ResponseInterface
	{
		return $this->jsonResponse(Response::unauthorized($message), 401);
	}

	/**
	 * Return a forbidden response (403).
	 */
	public function forbidden(string $message = 'Forbidden'): ResponseInterface
	{
		return $this->jsonResponse(Response::forbidden($message), 403);
	}

	/**
	 * Return a server error response (500).
	 */
	public function serverError(string $message = 'Internal server error'): ResponseInterface
	{
		return $this->jsonResponse(Response::serverError($message), 500);
	}

	/**
	 * Return a raw JSON response with full control.
	 *
	 * @param array<string, string> $headers
	 */
	public function jsonResponse(
		mixed $data,
		int $status = 200,
		array $headers = [],
	): ResponseInterface {
		if (!isset($headers['Content-Type'])) {
			$headers['Content-Type'] = 'application/json';
		}

		try {
			$json = json_encode($data, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
		} catch (JsonException $exception) {
			throw AppJsonException::forFailedEncode('Failed to encode.', $exception);
		}

		$response = $this->psr17Factory
			->createResponse($status)
			->withBody($this->psr17Factory->createStream($json))
			->withHeader('Content-Type', 'application/json')
		;

		foreach ($headers as $key => $value) {
			$response = $response->withHeader($key, $value);
		}

		return $response;
	}
}
