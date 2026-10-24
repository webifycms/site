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

namespace App\Infrastructure\Presentation\Api\Controller;

use App\Infrastructure\Contract\Service\UnsubscribeInterface;
use App\Infrastructure\Presentation\Api\Model\Unsubscribe as UnsubscribeModel;
use App\Infrastructure\Presentation\Api\{RequestParser, ResponseBuilder};
use Psr\Http\Message\{ResponseInterface, ServerRequestInterface};

/**
 * Unsubscribe controller handles newsletter deletion requests.
 */
final readonly class Unsubscribe
{
	/**
	 * The constructor.
	 */
	public function __construct(
		private UnsubscribeInterface $service,
		private RequestParser $requestParser,
		private ResponseBuilder $responseBuilder,
	) {}

	/**
	 * Handles the unsubscribe request.
	 *
	 * @param array<string, mixed> $args
	 */
	public function __invoke(ServerRequestInterface $request, array $args = []): ResponseInterface
	{
		// CSRF protection: verify custom header is present
		$xRequestedWith = $request->getHeaderLine('X-Requested-With');

		if ('XMLHttpRequest' !== $xRequestedWith) {
			return $this->responseBuilder->error('Invalid request.', 403);
		}

		// Parse and normalize JSON body
		$data       = $this->requestParser->getJsonBody($request);
		// Let DTO handle basic validations
		$model      = UnsubscribeModel::createFromArray($data, $this->requestParser->getValidator());
		// Use the service
		$result     = $this->service->unsubscribe($model->toArray());
		$statusCode = $result['statusCode'] ?? 500;

		if (true === $result['success']) {
			return $this->responseBuilder->ok(null, $result['message'] ?? 'Success');
		}

		if (400 === $statusCode) {
			return $this->responseBuilder->error(
				'Validation failed',
				400,
				$result['message'] ?? 'Validation failed.',
			);
		}

		return $this->responseBuilder->error(
			'Deletion failed',
			$statusCode,
			$result['message'] ?? 'Deletion failed',
			$result['errors'] ?? []
		);
	}
}
