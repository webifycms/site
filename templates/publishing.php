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
		<div class="hero__badge">Publishing</div>
		<h1 class="title">Thoughts on building the <em>right way</em></h1>
		<p class="hero__subtitle">
			Clean Architecture, Domain-Driven Design, and the journey of crafting a PHP CMS that respects your business logic.
		</p>
	</div>
</section>

<section class="section section--raised">
	<div class="container">
		<div class="posts" id="postsList">
			<?php if ([] === $view->data['pagination']['items']) { ?>
				<div class="posts__empty reveal reveal--vis">
					<div class="posts__empty-icon">&#9998;</div>
					<h2>No posts yet</h2>
					<p>Check back soon for updates on the journey.</p>
				</div>
			<?php } else { ?>
				<?php foreach ($view->data['pagination']['items'] as $post) { ?>
					<a href="<?= $view->url('/publishing/' . $post['slug']); ?>" class="post-card reveal">
						<div class="post-card__meta">
							<span><?= htmlspecialchars($post['date'], ENT_QUOTES, 'UTF-8'); ?></span>
							<span class="post-card__meta-sep"></span>
							<span>Article</span>
						</div>
						<h2 class="post-card__title"><?= htmlspecialchars($post['title'], ENT_QUOTES); ?></h2>
						<p class="post-card__excerpt"><?= htmlspecialchars($post['excerpt'], ENT_QUOTES); ?></p>
						<span class="post-card__link">
							Read more
							<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
								<path d="M5 12h14M12 5l7 7-7 7"/>
							</svg>
						</span>
					</a>
				<?php } ?>
			<?php } ?>
		</div>

		<?php if (1 < $view->data['pagination']['totalPages']) { ?>
			<nav class="pagination" aria-label="Pagination">
				<?php if (null !== $view->data['pagination']['prev']) { ?>
					<a href="<?= $view->url('/publishing/page/' . $view->data['pagination']['prev']); ?>" class="pagination__link pagination__link--prev">
						<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="20" height="20">
							<path d="M19 12H5M12 19l-7-7 7-7"/>
						</svg>
						Previous
					</a>
				<?php } else { ?>
					<span class="pagination__link pagination__link--disabled pagination__link--prev">
						<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="20" height="20">
							<path d="M19 12H5M12 19l-7-7 7-7"/>
						</svg>
						Previous
					</span>
				<?php } ?>

				<div class="pagination__pages">
					<?php foreach ($view->data['pagination']['pages'] as $p) { ?>
						<?php if (0 === $p) { ?>
							<span class="pagination__gap">&hellip;</span>
						<?php } elseif ($p === $view->data['pagination']['currentPage']) { ?>
							<span class="pagination__link pagination__link--current" aria-current="page"><?= htmlspecialchars((string) $p, ENT_QUOTES, 'UTF-8'); ?></span>
						<?php } else { ?>
							<a href="<?= $view->url('/publishing/page/' . $p); ?>" class="pagination__link"><?= htmlspecialchars((string) $p, ENT_QUOTES, 'UTF-8'); ?></a>
						<?php } ?>
					<?php } ?>
				</div>

				<?php if (null !== $view->data['pagination']['next']) { ?>
					<a href="<?= $view->url('/publishing/page/' . $view->data['pagination']['next']); ?>" class="pagination__link pagination__link--next">
						Next
						<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="20" height="20">
							<path d="M5 12h14M12 5l7 7-7 7"/>
						</svg>
					</a>
				<?php } else { ?>
					<span class="pagination__link pagination__link--disabled pagination__link--next">
						Next
						<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="20" height="20">
							<path d="M5 12h14M12 5l7 7-7 7"/>
						</svg>
					</span>
				<?php } ?>
			</nav>
		<?php } ?>
	</div>
</section>

<?php $view->renderPartial('footer'); ?>
