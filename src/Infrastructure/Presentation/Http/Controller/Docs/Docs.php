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

namespace App\Infrastructure\Presentation\Http\Controller\Docs;

use App\Infrastructure\Persistence\GitHub\DocsReader;
use App\Infrastructure\Presentation\Http\Controller\Base;
use App\Infrastructure\Service\View;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\{ResponseInterface, ServerRequestInterface};

/**
 * Renders the documentation page.
 */
final readonly class Docs extends Base
{
	public function __construct(
		View $view,
		Psr17Factory $psr17Factory,
		private DocsReader $reader,
	) {
		parent::__construct($view, $psr17Factory);
	}

	/**
	 * {@inheritDoc}
	 */
	public function __invoke(ServerRequestInterface $request, array $args): ResponseInterface
	{
		$allDocs    = $this->reader->findAll();
		$currentDoc = null;
		$slug       = $args['slug'] ?? null;

		if (is_string($slug)) {
			$currentDoc = $this->reader->findBySlug(trim($slug));

			if (null === $currentDoc) {
				$this->view->title       = '404 — Page not found';
				$this->view->description = 'Page not found';

				return $this->render('404', [], 404);
			}
		}

		if (null === $currentDoc && [] !== $allDocs) {
			$currentDoc = $this->reader->findBySlug($allDocs[0]['slug']);
		}

		if (null === $currentDoc) {
			$this->view->title       = '404 — Page not found';
			$this->view->description = 'Page not found';

			return $this->render('404', [], 404);
		}

		$safeTitle = htmlspecialchars($currentDoc['title'], ENT_QUOTES);

		$this->view->title       = $safeTitle . ' — Documentation';
		$this->view->description = $safeTitle . ' — WebifyCMS documentation';
		$this->view->canonical   = $this->view->url('/docs' . (isset($args['slug']) ? '/' . $args['slug'] : ''));
		$this->view->currentPath = '/docs';

		$this->view
			->addMeta('og:title', $safeTitle, 'property')
			->addMeta('og:description', 'WebifyCMS documentation — ' . $safeTitle, 'property')
		;

		return $this->render(
			'docs',
			[
				'title'       => $safeTitle,
				'content'     => $currentDoc['html'],
				'allDocs'     => $allDocs,
				'currentSlug' => $currentDoc['slug'],
				'headings'    => $currentDoc['html'],
			]
		);
	}
}
