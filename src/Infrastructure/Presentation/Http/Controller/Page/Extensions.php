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

use App\Infrastructure\Persistence\Filesystem\ExtensionsReader;
use App\Infrastructure\Presentation\Http\Controller\Base;
use App\Infrastructure\Service\View;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\{ResponseInterface, ServerRequestInterface};

/**
 * Renders the /extensions page listing the WebifyCMS extension ecosystem.
 */
final readonly class Extensions extends Base
{
	/**
	 * The constructor.
	 */
	public function __construct(
		View $view,
		Psr17Factory $psr17Factory,
		private ExtensionsReader $extensions
	) {
		parent::__construct($view, $psr17Factory);
	}

	/**
	 * {@inheritdoc}
	 */
	public function __invoke(ServerRequestInterface $request, array $args = []): ResponseInterface
	{
		$this->view->title       = 'Extensions';
		$this->view->description = 'WebifyCMS extension ecosystem — every bounded context in its own Composer package.';
		$this->view->canonical   = $this->view->url('/extensions');
		$this->view->currentPath = '/extensions';

		return $this->render('extensions', ['extensions' => $this->extensions->findAll()]);
	}
}
