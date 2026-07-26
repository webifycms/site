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
use App\Infrastructure\Service\{SitemapGenerator, View};
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\{ResponseInterface, ServerRequestInterface};

/**
 * Handles the HTTP request for /sitemap.xml.
 *
 * All XML generation logic lives in SitemapGenerator — this
 * controller only wraps the output in a PSR-7 response.
 */
final readonly class Sitemap extends Base
{
	/**
	 * The constructor.
	 */
	public function __construct(
		View $view,
		Psr17Factory $psr17Factory,
		private SitemapGenerator $sitemap,
	) {
		parent::__construct($view, $psr17Factory);
	}

	/**
	 * {@inheritDoc}
	 */
	public function __invoke(ServerRequestInterface $request, array $args = []): ResponseInterface
	{
		return $this->psr17Factory
			->createResponse(200)
			->withBody($this->psr17Factory->createStream($this->sitemap->generate()))
			->withHeader('Content-Type', 'application/xml')
		;
	}
}
