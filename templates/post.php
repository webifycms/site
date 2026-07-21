<?php

declare(strict_types=1);

use App\Infrastructure\Service\View;

/**
 * @var View $view
 */
?>
<?php $view->renderPartial('header'); ?>

<article>
	<script type="application/ld+json">
	{
		"@context": "https://schema.org",
		"@type": "Article",
		"headline": "<?= json_encode($view->data['title'], JSON_UNESCAPED_UNICODE); ?>",
		"description": "<?= json_encode($view->data['excerpt'], JSON_UNESCAPED_UNICODE); ?>",
		"datePublished": "<?= json_encode($view->data['date'], JSON_UNESCAPED_UNICODE); ?>",
		"author": {
			"@type": "Person",
			"name": "Mohammed Shifreen"
		},
		"publisher": {
			"@type": "Organization",
			"name": "WebifyCMS"
		}
	}
	</script>

	<header class="post-header">
		<div class="hero__orb hero__orb--1" aria-hidden="true"></div>
		<div class="hero__orb hero__orb--2" aria-hidden="true"></div>

		<div class="post-header__inner">
			<a href="<?= $view->url('/publishing'); ?>" class="post-header__back">
				<svg
					viewBox="0 0 24 24"
					fill="none"
					stroke="currentColor"
					stroke-width="2"
					stroke-linecap="round"
					stroke-linejoin="round"
				>
					<path d="M19 12H5M12 19l-7-7 7-7" />
				</svg>
				Back to Publishing
			</a>
			<div class="post-header__meta">
				<span class="post-header__date"><?= htmlspecialchars($view->data['date'], ENT_QUOTES); ?></span>
				<span class="post-header__meta-sep"></span>
				<span>Article</span>
			</div>
			<h1 class="post-header__title"><?= htmlspecialchars($view->data['title'], ENT_QUOTES); ?></h1>
			<p class="post-header__excerpt"><?= htmlspecialchars($view->data['excerpt'], ENT_QUOTES); ?></p>
		</div>
	</header>

	<div class="share">
		<span class="share__label">Share</span>
		<div class="share__actions">
			<a class="share__btn" href="https://twitter.com/intent/tweet?url=<?= urlencode($view->url('/publishing/' . $view->data['slug'])); ?>&text=<?= urlencode($view->data['title']); ?>" target="_blank" rel="noopener" aria-label="Share on X">
				<svg viewBox="0 0 24 24" fill="currentColor" width="18" height="18"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
			</a>
			<a class="share__btn" href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode($view->url('/publishing/' . $view->data['slug'])); ?>" target="_blank" rel="noopener" aria-label="Share on Facebook">
				<svg viewBox="0 0 24 24" fill="currentColor" width="18" height="18"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
			</a>
			<a class="share__btn" href="https://www.linkedin.com/shareArticle?mini=true&url=<?= urlencode($view->url('/publishing/' . $view->data['slug'])); ?>&title=<?= urlencode($view->data['title']); ?>" target="_blank" rel="noopener" aria-label="Share on LinkedIn">
				<svg viewBox="0 0 24 24" fill="currentColor" width="18" height="18"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
			</a>
			<button class="share__btn share__btn--copy" type="button" data-copy-url="<?= htmlspecialchars($view->url('/publishing/' . $view->data['slug']), ENT_QUOTES); ?>" aria-label="Copy link">
				<svg class="share__icon-link" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="18" height="18"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
				<svg class="share__icon-check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="18" height="18"><polyline points="20 6 9 17 4 12"/></svg>
			</button>
		</div>
	</div>

	<div class="post-body">
		<div class="post-body__inner reveal reveal--vis">
			<div class="post-body__content"><?= $view->data['content']; ?></div>
		</div>

		<nav class="post-nav">
			<?php if (null !== $view->data['prev']) { ?>
				<a href="<?= $view->url('/publishing/' . $view->data['prev']['slug']); ?>" class="post-nav__link">
					<span class="post-nav__label">Previous</span>
					<span class="post-nav__title"><?= htmlspecialchars($view->data['prev']['title'], ENT_QUOTES); ?></span>
				</a>
			<?php } ?>
			<?php if (null !== $view->data['next']) { ?>
				<a href="<?= $view->url('/publishing/' . $view->data['next']['slug']); ?>" class="post-nav__link post-nav__link--next">
					<span class="post-nav__label">Next</span>
					<span class="post-nav__title"><?= htmlspecialchars($view->data['next']['title'], ENT_QUOTES); ?></span>
				</a>
			<?php } ?>
		</nav>
	</div>
</article>

<?php $view->renderPartial('footer'); ?>
