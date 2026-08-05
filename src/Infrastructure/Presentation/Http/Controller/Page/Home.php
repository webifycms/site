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

use App\Infrastructure\Persistence\Filesystem\PostReader;
use App\Infrastructure\Presentation\Http\Controller\Base;
use App\Infrastructure\Service\View;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\{ResponseInterface, ServerRequestInterface};

/**
 * Home controller renders the home page.
 */
final readonly class Home extends Base
{
	/**
	 * The number of latest posts shown on the home page.
	 */
	private const int LATEST_POSTS_COUNT = 2;

	/**
	 * The constructor.
	 */
	public function __construct(
		View $view,
		Psr17Factory $psr17Factory,
		private PostReader $posts,
	) {
		parent::__construct($view, $psr17Factory);
	}

	/**
	 * {@inheritdoc}
	 */
	public function __invoke(ServerRequestInterface $request, array $args = []): ResponseInterface
	{
		$this->view->title       = 'The PHP CMS Built the Right Way';
		$this->view->description = 'WebifyCMS is an open-source PHP application framework built on Clean Architecture and Domain-Driven Design. Your business logic stays pure, testable, and framework-agnostic.';
		$this->view->canonical   = $this->view->url('/');

		return $this->render('home', [
			'latestPosts' => array_slice($this->posts->findAll(), 0, self::LATEST_POSTS_COUNT),
		]);
	}
}
