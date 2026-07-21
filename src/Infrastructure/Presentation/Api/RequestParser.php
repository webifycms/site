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
	 * @return array<string, mixed>
	 *
	 * @throws ValidationException if body is an invalid JSON
	 */
	public function getJsonBody(ServerRequestInterface $request): array
	{
		$body = (string) $request->getBody();

		if ('' === $body) {
			return [];
		}

		/** @var array<string, mixed> $data */
		$data = json_decode($body, true);

		if (json_last_error() !== JSON_ERROR_NONE) {
			$message = sprintf('Invalid JSON body: %s', json_last_error_msg());

			$this->logger->error($message, ['json' => $body]);

			throw ValidationException::forInvalidJson($message);
		}

		return $data;
	}
}
