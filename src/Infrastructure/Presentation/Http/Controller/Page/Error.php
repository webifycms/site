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

namespace App\Infrastructure\Presentation\Http\Controller\Page;

use App\Infrastructure\Presentation\Http\Controller\Base;
use Psr\Http\Message\{ResponseInterface, ServerRequestInterface};

/**
 * Error controller, it renders the error page.
 */
final readonly class Error extends Base
{
	/**
	 * {@inheritdoc}
	 */
	public function __invoke(ServerRequestInterface $request, array $args): ResponseInterface
	{
		$statusCode = $args['statusCode'] ?? 500;
		$statusText = $args['statusText'] ?? 'Internal Server Error';

		$subTitle = match (true) {
			404 === $statusCode => "The page you're looking for doesn't exist or has been moved.",
			500 <= $statusCode  => 'Something went wrong on our end. Please try again later.',
			403 === $statusCode => "You don't have permission to access this resource.",
			default             => 'The request could not be completed.',
		};

		$this->view->title       = sprintf('%s — %s', $statusCode, $statusText);
		$this->view->description = $subTitle;

		return $this->render('error', [
			'statusCode' => $statusCode,
			'statusText' => $statusText,
		], (int) $statusCode);
	}
}
