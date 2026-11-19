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

use App\Infrastructure\Exception\ValidationException;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Parses and validates request data for API controllers.
 */
final readonly class RequestParser
{
	/**
	 * The constructor.
	 */
	public function __construct(
		private ValidatorInterface $validator,
		private LoggerInterface $logger,
	) {}

	/**
	 * Get the validator instance.
	 */
	public function getValidator(): ValidatorInterface
	{
		return $this->validator;
	}

	/**
	 * Parse the JSON request body into an array.
	 *
	 * @param bool $normalize Whether to apply common normalizations (e.g., trim string values)
	 *
	 * @return array<string, mixed>
	 *
	 * @throws ValidationException if body is an invalid JSON
	 */
	public function getJsonBody(ServerRequestInterface $request, bool $normalize = true): array
	{
		$body = (string) $request->getBody();

		if ('' === $body) {
			return [];
		}

		$decoded = json_decode($body, true);

		if (json_last_error() !== JSON_ERROR_NONE) {
			$message = sprintf('Invalid JSON body: %s', json_last_error_msg());

			$this->logger->error($message, ['json' => $body]);

			throw ValidationException::forInvalidJson($message);
		}

		if (!is_array($decoded)) {
			$message = 'Invalid JSON body: expected a JSON object.';

			$this->logger->error($message, ['json' => $body]);

			throw ValidationException::forInvalidJson($message);
		}

		$data = [];

		foreach ($decoded as $key => $value) {
			if (!is_string($key)) {
				$message = 'Invalid JSON body: expected an object with string keys.';

				$this->logger->error($message, ['json' => $body]);

				throw ValidationException::forInvalidJson($message);
			}

			$data[$key] = $value;
		}

		return $normalize ? $this->normalize($data) : $data;
	}

	/**
	 * Apply common normalizations to request data.
	 *
	 * @param array<string, mixed> $data
	 *
	 * @return array<string, mixed>
	 */
	private function normalize(array $data): array
	{
		return array_map($this->normalizeValue(...), $data);
	}

	/**
	 * Trims a single value, recursing into nested JSON arrays and objects.
	 */
	private function normalizeValue(mixed $value): mixed
	{
		if (is_string($value)) {
			return mb_trim($value, " \t\n\r\0\x0B\xC2\xA0");
		}

		if (is_array($value)) {
			return array_map($this->normalizeValue(...), $value);
		}

		return $value;
	}
}
