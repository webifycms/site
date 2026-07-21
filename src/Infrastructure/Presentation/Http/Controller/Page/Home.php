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
 * Home controller renders the home page.
 */
final readonly class Home extends Base
{
	/**
	 * {@inheritdoc}
	 */
	public function __invoke(ServerRequestInterface $request, array $args = []): ResponseInterface
	{
		$this->view->title       = 'PHP CMS Built the Right Way';
		$this->view->description = 'WebifyCMS is an open-source application framework for building web applications, with built-in content management features.';
		$this->view->canonical   = $this->view->url('/');

		return $this->render('home');
	}
}
