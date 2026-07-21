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
 * @phpstan-type Extension array{
 *     name: string,
 *     package: string,
 *     status: string,
 *     version: string,
 *     description: string,
 *     url: string,
 * }
 */
final readonly class Extensions extends Base
{
	/**
	 * {@inheritdoc}
	 */
	public function __invoke(ServerRequestInterface $request, array $args = []): ResponseInterface
	{
		$this->view->title       = 'Extensions';
		$this->view->description = 'WebifyCMS extension ecosystem — every bounded context in its own Composer package.';
		$this->view->canonical   = $this->view->url('/extensions');
		$this->view->currentPath = '/extensions';

		return $this->render('extensions', ['extensions' => self::getExtensions()]);
	}

	/**
	 * @return Extension[]
	 */
	private static function getExtensions(): array
	{
		return [
			[
				'name'        => 'Base',
				'package'     => 'webifycms/ext-base',
				'status'      => 'Completed',
				'version'     => 'v0.1.0-alpha',
				'description' => 'Shared kernel holding abstractions, contracts, and reusable infrastructure components. The foundation every extension builds on.',
				'url'         => 'https://github.com/webifycms/ext-base',
			],
			[
				'name'        => 'Admin',
				'package'     => 'webifycms/ext-admin',
				'status'      => 'In progress',
				'version'     => '',
				'description' => 'Dashboard, settings, UI components, and the slot-based admin panel.',
				'url'         => 'https://github.com/webifycms/ext-admin',
			],
			[
				'name'        => 'User',
				'package'     => 'webifycms/ext-user',
				'status'      => 'In progress',
				'version'     => '',
				'description' => 'Identity, authentication, authorization, registration, profiles, and roles.',
				'url'         => 'https://github.com/webifycms/ext-user',
			],
			[
				'name'        => 'CMS',
				'package'     => 'webifycms/ext-cms',
				'status'      => 'Planned',
				'version'     => '',
				'description' => 'Content types, pages, posts, multi-site support, and blogging.',
				'url'         => '',
			],
			[
				'name'        => 'Marketplace',
				'package'     => 'webifycms/ext-marketplace',
				'status'      => 'Planned',
				'version'     => '',
				'description' => 'Theme and extension distribution hub — discover, install, and manage packages.',
				'url'         => '',
			],
			[
				'name'        => 'AI Assist',
				'package'     => 'webifycms/ext-ai',
				'status'      => 'Planned',
				'version'     => '',
				'description' => 'Content generation, suggestions, and AI-powered editorial assistance.',
				'url'         => '',
			],
		];
	}
}
