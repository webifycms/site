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

namespace App\Infrastructure\Presentation\Http\Controller\Publishing;

use App\Infrastructure\Persistence\Filesystem\{PostPagination, PostReader};
use App\Infrastructure\Presentation\Http\Controller\Base;
use App\Infrastructure\Service\View;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\{ResponseInterface, ServerRequestInterface};

/**
 * The controller renders the single post.
 */
final readonly class Single extends Base
{
	/**
	 * The constructor.
	 */
	public function __construct(
		View $view,
		Psr17Factory $psr17Factory,
		private PostReader $reader,
		private PostPagination $pagination,
	) {
		parent::__construct($view, $psr17Factory);
	}

	/**
	 * {@inheritDoc}
	 */
	public function __invoke(ServerRequestInterface $request, array $args): ResponseInterface
	{
		if (!isset($args['slug']) || !is_string($args['slug'])) {
			return $this->render('404', [], 404);
		}

		$slug = trim($args['slug']);
		$post = $this->reader->findBySlug($slug);

		if (null === $post) {
			return $this->render('404', [], 404);
		}

		$pagination = $this->pagination->forSlug($slug);

		$this->view->title       = $post['title'] . ' — Publishing';
		$this->view->description = $post['excerpt'];
		$this->view->canonical   = $this->view->url('/publishing/' . $post['slug']);
		$this->view->currentPath = '/publishing';

		$this->view->addMeta('article:published_time', $post['date'], 'property');

		return $this->render(
			'post',
			[
				'title'    => $post['title'],
				'slug'     => $post['slug'],
				'date'     => $post['date'],
				'updated'  => $post['updated'],
				'excerpt'  => $post['excerpt'],
				'content'  => $post['html'],
				'prev'     => $pagination['prev'],
				'next'     => $pagination['next'],
			]
		);
	}
}
