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

use App\Infrastructure\Service\View;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\{ResponseInterface, ServerRequestInterface};

/**
 * Base controller, it's the base controller for all the controllers'.
 */
abstract readonly class Base
{
	/**
	 * The constructor.
	 */
	public function __construct(
		protected View $view,
		protected Psr17Factory $psr17Factory,
	) {}

	/**
	 * Handles the HTTP request/response lifecycle.
	 *
	 * @param ServerRequestInterface $request the HTTP request
	 * @param array<string, mixed>   $args    the route arguments
	 */
	abstract public function __invoke(ServerRequestInterface $request, array $args): ResponseInterface;

	/**
	 * Renders a template and returns a PSR-7 response.
	 *
	 * @param string               $template the template name (without .php)
	 * @param array<string, mixed> $data     template-specific variables
	 * @param int                  $status   the HTTP status code
	 */
	protected function render(string $template, array $data = [], int $status = 200): ResponseInterface
	{
		$contents = $this->view->render($template, $data);

		return $this->psr17Factory
			->createResponse($status)
			->withBody($this->psr17Factory->createStream($contents))
			->withHeader('Content-Type', 'text/html')
		;
	}
}
