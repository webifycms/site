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

use App\Infrastructure\Persistence\Filesystem\PostPagination;
use App\Infrastructure\Presentation\Http\Controller\Base;
use App\Infrastructure\Service\View;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\{ResponseInterface, ServerRequestInterface};

/**
 * The controller renders the posts listing with page-based pagination.
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
	) {
		parent::__construct($view, $psr17Factory);
	}

	/**
	 * {@inheritdoc}
	 */
	public function __invoke(ServerRequestInterface $request, array $args): ResponseInterface
	{
		$page       = isset($args['page']) ? (int) $args['page'] : 1;
		$pagination = $this->pagination->forList($page);

		$this->view->title       = 'Publishing';
		$this->view->description = 'Thoughts on Clean Architecture, DDD, and building the PHP CMS built the right way.';
		$this->view->canonical   = $this->view->url('/publishing');
		$this->view->currentPath = '/publishing';

		if (1 < $page) {
			$this->view->addMeta('robots', 'noindex, follow');
		}

		return $this->render('publishing', ['pagination' => $pagination]);
	}
}
