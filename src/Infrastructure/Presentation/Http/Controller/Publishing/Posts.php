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

use App\Infrastructure\Persistence\Filesystem\{PostIndexer, PostPagination, PostReader};
use App\Infrastructure\Presentation\Http\Controller\Base;
use App\Infrastructure\Service\View;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\{ResponseInterface, ServerRequestInterface};

/**
 * The controller renders the posts listing with page-based pagination
 * and optional category filtering.
 */
final readonly class Posts extends Base
{
	/**
	 * The constructor.
	 */
	public function __construct(
		View $view,
		Psr17Factory $psr17Factory,
		private PostPagination $pagination,
		private PostReader $reader,
	) {
		parent::__construct($view, $psr17Factory);
	}

	/**
	 * {@inheritdoc}
	 */
	public function __invoke(ServerRequestInterface $request, array $args): ResponseInterface
	{
		$page     = isset($args['page']) ? (int) $args['page'] : 1;
		$category = $this->resolveCategory($request);

		$pagination = $this->pagination->forList($page, 10, $category);

		$this->view->title       = null === $category ? 'Publishing' : $category . ' — Publishing';
		$this->view->description = 'Thoughts on Clean Architecture, DDD, and building the PHP CMS built the right way.';
		$this->view->canonical   = $this->view->url('/publishing');
		$this->view->currentPath = '/publishing';

		if (1 < $page || null !== $category) {
			$this->view->addMeta('robots', 'noindex, follow');
		}

		return $this->render('publishing', [
			'pagination'     => $pagination,
			'activeCategory' => $category,
			'categories'     => $this->categoryCounts(),
		]);
	}

	/**
	 * Reads the category query parameter and validates it against the
	 * allowed list. Invalid values are ignored.
	 */
	private function resolveCategory(ServerRequestInterface $request): ?string
	{
		$category = trim((string) ($request->getQueryParams()['category'] ?? ''));

		return in_array($category, PostIndexer::CATEGORIES, true) ? $category : null;
	}

	/**
	 * Returns the number of published posts per category.
	 *
	 * @return array<string, int>
	 */
	private function categoryCounts(): array
	{
		$counts = array_fill_keys(PostIndexer::CATEGORIES, 0);

		foreach ($this->reader->findAll() as $post) {
			$category = $post['category'] ?? 'Update';

			if (array_key_exists($category, $counts)) {
				++$counts[$category];
			}
		}

		return $counts;
	}
}
