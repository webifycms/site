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

use App\Infrastructure\Contract\Service\SubscribeInterface;
use App\Infrastructure\Presentation\Api\Model\Subscribe as SubscribeModel;
use App\Infrastructure\Presentation\Api\{RequestParser, ResponseBuilder};
use Psr\Http\Message\{ResponseInterface, ServerRequestInterface};

/**
 * Subscribe controller handles newsletter signup form submissions.
 */
final readonly class Subscribe
{
	/**
	 * The constructor.
	 */
	public function __construct(
		private SubscribeInterface $service,
		private RequestParser $requestParser,
		private ResponseBuilder $responseBuilder,
	) {}

	/**
	 * Handles the subscription request.
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

		// Parse JSON body
		$data = $this->requestParser->getJsonBody($request);
		// Let DTO handle validation
		$model = SubscribeModel::createFromArray($data, $this->requestParser->getValidator());
		// Use the service
		$result     = $this->service->subscribe($model->toArray());
		$statusCode = $result['statusCode'] ?? 500;

		if (true === $result['success']) {
			return $this->responseBuilder->ok(null, $result['message'] ?? 'Success');
		}

		if (409 === $statusCode) {
			return $this->responseBuilder->ok(null, $result['message'] ?? 'Already subscribed');
		}

		return $this->responseBuilder->error($result['message'] ?? 'Subscription failed', $statusCode);
	}
}
