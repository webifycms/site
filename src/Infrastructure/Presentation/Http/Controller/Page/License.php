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
 * License controller renders the license page.
 */
final readonly class License extends Base
{
	/**
	 * {@inheritdoc}
	 */
	public function __invoke(ServerRequestInterface $request, array $args = []): ResponseInterface
	{
		$this->view->title       = 'MIT License';
		$this->view->description = 'WebifyCMS is open source software released under the MIT License.';
		$this->view->canonical   = $this->view->url('/license');

		return $this->render('license');
	}
}
