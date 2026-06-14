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

use App\Infrastructure\Presentation\Http\Controller\Home;
use League\Route\Router;

/**
 * Define the routes for the application.
 *
 * @param Router $router the router instance
 */
return static function (Router $router) {
	$router->map('GET', '/', Home::class);
};
