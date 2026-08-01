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
?>
<?php $view->renderPartial('header'); ?>

<section class="hero--publication">
	<div class="hero__orb hero__orb--1" aria-hidden="true"></div>
	<div class="hero__orb hero__orb--2" aria-hidden="true"></div>

	<div class="container hero__inner">
		<div class="hero__badge">Extensions</div>
		<h1 class="title">Modular by <em>design</em></h1>
		<p class="hero__subtitle">
			Every bounded context lives in its own Composer package. Install only what your project needs.
		</p>
	</div>
</section>

<section class="section section--raised">
	<div class="container">
		<div class="ext-list">
			<?php foreach ($view->data['extensions'] as $ext) { ?>
				<div class="ext-card">
					<div class="ext-card__head">
						<h2 class="ext-card__name"><?= htmlspecialchars($ext['name'], ENT_QUOTES); ?></h2>
						<span class="ext-card__tag ext-card__tag--<?= strtolower(str_replace(' ', '-', $ext['status'])); ?>">
							<?= htmlspecialchars($ext['status'], ENT_QUOTES); ?>
						</span>
					</div>
					<code class="ext-card__package"><?= htmlspecialchars($ext['package'], ENT_QUOTES); ?></code>
					<p class="ext-card__desc"><?= htmlspecialchars($ext['description'], ENT_QUOTES); ?></p>
					<?php if ('' !== $ext['version'] || '' !== $ext['url']) { ?>
						<div class="ext-card__foot">
							<?php if ('' !== $ext['version']) { ?>
								<span class="ext-card__version"><?= htmlspecialchars($ext['version'], ENT_QUOTES); ?></span>
							<?php } ?>
							<?php if ('' !== $ext['url']) { ?>
								<a href="<?= htmlspecialchars($ext['url'], ENT_QUOTES); ?>" target="_blank" rel="noopener" class="ext-card__link">
									View on GitHub
									<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="14" height="14">
										<path d="M7 17l9.2-9.2M17 17V7H7"/>
									</svg>
								</a>
							<?php } ?>
						</div>
					<?php } ?>
				</div>
			<?php } ?>
		</div>

		<?php if ([] !== $view->data['themes']) { ?>
			<div class="ext-themes">
				<div class="ext-themes__head reveal">
					<p class="label">// Themes</p>
					<h2 class="title" style="margin-top: 14px">The default <em>theme</em></h2>
					<p class="lead" style="margin-top: 18px">
						Ships with WebifyCMS as the default front-end theme.
					</p>
				</div>
				<div class="ext-list">
					<?php foreach ($view->data['themes'] as $theme) { ?>
						<div class="ext-card">
							<div class="ext-card__head">
								<h2 class="ext-card__name"><?= htmlspecialchars($theme['name'], ENT_QUOTES); ?></h2>
								<span class="ext-card__tag ext-card__tag--<?= strtolower(str_replace(' ', '-', $theme['status'])); ?>">
									<?= htmlspecialchars($theme['status'], ENT_QUOTES); ?>
								</span>
							</div>
							<code class="ext-card__package"><?= htmlspecialchars($theme['package'], ENT_QUOTES); ?></code>
							<p class="ext-card__desc"><?= htmlspecialchars($theme['description'], ENT_QUOTES); ?></p>
							<?php if ('' !== $theme['version'] || '' !== $theme['url']) { ?>
								<div class="ext-card__foot">
									<?php if ('' !== $theme['version']) { ?>
										<span class="ext-card__version"><?= htmlspecialchars($theme['version'], ENT_QUOTES); ?></span>
									<?php } ?>
									<?php if ('' !== $theme['url']) { ?>
										<a href="<?= htmlspecialchars($theme['url'], ENT_QUOTES); ?>" target="_blank" rel="noopener" class="ext-card__link">
											View on GitHub
											<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="14" height="14">
												<path d="M7 17l9.2-9.2M17 17V7H7"/>
											</svg>
										</a>
									<?php } ?>
								</div>
							<?php } ?>
						</div>
					<?php } ?>
				</div>
			</div>
		<?php } ?>
	</div>
</section>

<?php $view->renderPartial('footer'); ?>
