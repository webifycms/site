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

namespace App\Infrastructure\Service;

use App\Infrastructure\Presentation\Http\Controller\Page\Error;
use League\Route\Http\Exception\{HttpExceptionInterface, NotFoundException};
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\{ResponseInterface, ServerRequestInterface};
use Throwable;
use Webify\Base\Infrastructure\Contract\ErrorHandlerInterface;

use function Sentry\captureException;

/**
 * Catches all uncaught exceptions within the middleware pipeline.
 *
 * In debug mode it re-throws so the registered development error handler
 * (Whoops) can produce a rich diagnostic page. In production, it logs the
 * exception and emits an appropriate HTTP error response.
 */
final readonly class ErrorHandler implements ErrorHandlerInterface
{
	public function __construct(
		private Psr17Factory $factory,
		private View $view,
	) {}

	/**
	 * {@inheritDoc}
	 */
	public function handle(ServerRequestInterface $request, Throwable $throwable): ResponseInterface
	{
		captureException($throwable);

		$statusCode = 500;

		if ($throwable instanceof NotFoundException) {
			$statusCode = 404;
		}

		if ($throwable instanceof HttpExceptionInterface) {
			$statusCode = $throwable->getStatusCode();
		}

		$statusText = match ($statusCode) {
			403     => 'Forbidden',
			404     => 'Page Not Found',
			405     => 'Method Not Allowed',
			500     => 'Internal Server Error',
			default => 'Error',
		};

		return new Error($this->view, $this->factory)($request, [
			'statusCode' => $statusCode,
			'statusText' => $statusText,
		]);
	}
}
