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

namespace App\Infrastructure\Presentation\Api\Middleware;

use App\Infrastructure\Exception\{JsonException, ValidationException};
use App\Infrastructure\Presentation\Api\ResponseBuilder;
use Psr\Http\Message\{ResponseInterface, ServerRequestInterface};
use Psr\Http\Server\{MiddlewareInterface, RequestHandlerInterface};
use Psr\Log\LoggerInterface;
use RuntimeException;
use Throwable;

use function Sentry\captureException;

/**
 * Catches exceptions thrown by API controllers and converts them into
 * standardised JSON responses via ResponseBuilder.
 *
 * Three tiers are handled:
 *
 *  - ValidationException    → 422 with field-level error details.
 *  - SerializeException     → 500 Internal Server Error (serialization failure).
 *  - RuntimeException       → generic "An error occurred" message; the
 *                             original status code is preserved when it
 *                             falls in the 4xx/5xx range.
 *  - Throwable (fallback)   → 500 Internal Server Error.
 *
 * In all non-validation cases the full exception details are logged
 * server-side but only a safe, generic message is sent to the client
 * to prevent information leakage.
 */
final readonly class ExceptionHandler implements MiddlewareInterface
{
	/**
	 * The constructor.
	 */
	public function __construct(
		private ResponseBuilder $responseBuilder,
		private LoggerInterface $logger,
	) {}

	/**
	 * {@inheritDoc}
	 */
	public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
	{
		try {
			return $handler->handle($request);
		} catch (ValidationException $exception) {
			return $this->responseBuilder->validationError($exception->getError());
		} catch (JsonException $exception) {
			$this->logger->error('API JsonException', ['exception' => $exception]);
			captureException($exception);

			return $this->responseBuilder->serverError();
		} catch (RuntimeException $exception) {
			$this->logger->error('API RuntimeException', ['exception' => $exception]);
			captureException($exception);

			$code = $exception->getCode();

			if (400 <= $code && 600 > $code) {
				return $this->responseBuilder->error('An error occurred. Please try again later.', $code);
			}

			return $this->responseBuilder->error('An error occurred. Please try again later.');
		} catch (Throwable $exception) {
			$this->logger->error('API Error', ['exception' => $exception->getMessage()]);
			captureException($exception);

			return $this->responseBuilder->serverError();
		}
	}
}
