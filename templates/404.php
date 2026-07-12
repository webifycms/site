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

use App\Infrastructure\Service\View;

/**
 * @var View $view
 */
$view->title       = '404 — Page Not Found';
$view->description = "The page you're looking for doesn't exist or has been moved";
?>
<?php $view->renderPartial('header'); ?>

<section class="hero--404">
	<div class="hero__orb hero__orb--1" aria-hidden="true"></div>
	<div class="hero__orb hero__orb--2" aria-hidden="true"></div>

	<div class="container error reveal reveal--vis">
		<div class="error__code">404</div>
		<h1 class="error__heading">Lost in the codebase?</h1>
		<p class="error__text">
			The page you're looking for doesn't exist or has been moved. Let's get you back to familiar territory.
		</p>
		<div class="error__actions">
			<a href="<?= $view->url('/'); ?>" class="btn btn--solid">
				<svg
					width="16"
					height="16"
					viewBox="0 0 24 24"
					fill="none"
					stroke="currentColor"
					stroke-width="2"
					stroke-linecap="round"
					stroke-linejoin="round"
					aria-hidden="true"
				>
					<path d="M19 12H5M12 19l-7-7 7-7" />
				</svg>
				Back Home
			</a>
			<a href="<?= $view->url('/publishing'); ?>" class="btn btn--ghost">Back Publishing</a>
		</div>
	</div>
</section>

<?php $view->renderPartial('footer'); ?>
