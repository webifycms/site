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
 * Privacy controller renders the privacy policy page.
 */
final readonly class Privacy extends Base
{
	/**
	 * {@inheritdoc}
	 */
	public function __invoke(ServerRequestInterface $request, array $args = []): ResponseInterface
	{
		$this->view->title       = 'Privacy Policy';
		$this->view->description = 'How WebifyCMS collects, uses, and protects your data. Learn about our use of Plausible Analytics and GlitchTip.';
		$this->view->canonical   = $this->view->url('/privacy');

		return $this->render('privacy');
	}
}
