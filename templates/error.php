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
$statusCode  = $view->data['statusCode'];
$statusText  = $view->data['statusText'];
$subTitle    = $view->description;
?>
<?php $view->renderPartial('header'); ?>

<section class="hero" style="min-height: 60vh">
	<div class="hero__orb hero__orb--1" aria-hidden="true"></div>
	<div class="hero__orb hero__orb--2" aria-hidden="true"></div>

	<div class="container hero__inner" style="text-align: center; padding-top: 80px">
		<div
			style="
				font-size: clamp(5rem, 12vw, 8rem);
				font-weight: 700;
				line-height: 1;
				letter-spacing: -0.04em;
				color: var(--accent);
			"
		>
            <?= htmlspecialchars((string) $statusCode, ENT_QUOTES); ?>
		</div>

		<h1 class="hero__title" style="margin-top: 8px"><?= htmlspecialchars($statusText, ENT_QUOTES); ?></h1>

		<p class="hero__subtitle" style="max-width: 480px; margin: 16px auto 0">
			<?= htmlspecialchars($subTitle, ENT_QUOTES); ?>
		</p>

		<div class="hero__actions" style="justify-content: center; margin-top: 32px">
			<a href="<?= $view->url('/'); ?>" class="btn btn--solid">&larr; Back to Home</a>
		</div>
	</div>
</section>

<?php $view->renderPartial('footer'); ?>
