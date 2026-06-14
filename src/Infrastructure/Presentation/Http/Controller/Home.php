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

namespace App\Infrastructure\Presentation\Http\Controller;

use Psr\Http\Message\{ResponseFactoryInterface, ResponseInterface, StreamFactoryInterface};
use Webify\Base\Application\Service\ConfigInterface;

/**
 * Home controller, just an example controller. It renders the home page.
 */
final readonly class Home
{
	/**
	 * The constructor.
	 */
	public function __construct(
		private ResponseFactoryInterface $responseFactory,
		private StreamFactoryInterface $streamFactory,
		private ConfigInterface $config
	) {}

	/**
	 * Handles the HTTP request/response lifecycle of the home page.
	 */
	public function __invoke(): ResponseInterface
	{
		$response = $this->responseFactory->createResponse(200);
		$contents = file_get_contents($this->config->basePath . '/templates/home.html');
		$body     = $this->streamFactory->createStream((string) $contents);

		return $response
			->withBody($body)
			->withHeader('Content-Type', 'text/html')
		;
	}
}
