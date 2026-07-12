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

use App\Infrastructure\Presentation\Http\Controller\Api\Subscribe;
use App\Infrastructure\Presentation\Http\Controller\Docs\Docs as DocsController;
use App\Infrastructure\Presentation\Http\Controller\Page\{Extensions, Home, License, NotFound, Privacy, Sitemap};
use App\Infrastructure\Presentation\Http\Controller\Publishing\{Posts, Single};
use League\Route\{RouteGroup, Router};

/**
 * Define the routes for the application.
 *
 * @param Router $router the router instance
 */
return static function (Router $router) {
	// Sitemap
	$router->map('GET', '/sitemap.xml', Sitemap::class);
	// API routes
	$router->map('POST', '/api/subscribe', Subscribe::class);
	// Home page
	$router->map('GET', '/', Home::class);
	// License page
	$router->map('GET', '/license', License::class);
	// Privacy policy page
	$router->map('GET', '/privacy', Privacy::class);
	// Extensions page
	$router->map('GET', '/extensions', Extensions::class);
	// 404 page
	$router->map('GET', '/404', NotFound::class);
	// Publishing group
	$router->group('/publishing', static function (RouteGroup $route) {
		$route->map('GET', '/', Posts::class);
		$route->map('GET', '/page/{page:number}', Posts::class);
		$route->map('GET', '/{slug:slug}', Single::class);
	});
	// Docs group
	$router->group('/docs', static function (RouteGroup $route) {
		$route->map('GET', '/', DocsController::class);
		$route->map('GET', '/{slug:slug}', DocsController::class);
	});
};
