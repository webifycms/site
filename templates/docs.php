<?php

declare(strict_types=1);

use App\Infrastructure\Service\View;

/**
 * @var View $view
 */
$matches = [];

preg_match_all(
	'/<h2\s+id="([^"]+)"[^>]*>(.*?)<\/h2>/s',
	$view->data['headings'],
	$matches,
	PREG_SET_ORDER
);
?>
<?php $view->renderPartial('header'); ?>

<section class="docs">
	<div class="docs__layout">
		<aside class="docs__sidebar">
			<nav class="docs-sidebar">
				<div class="docs-sidebar__heading">Documentation</div>
				<?php foreach ($view->data['allDocs'] as $doc) { ?>
					<a href="<?= $view->url('/docs/' . $doc['slug']); ?>"
					   class="docs-sidebar__link<?= $doc['slug'] === $view->data['currentSlug'] ? ' docs-sidebar__link--active' : ''; ?>">
						<?= htmlspecialchars($doc['title'], ENT_QUOTES); ?>
					</a>
				<?php } ?>

				<?php if ([] !== $matches) { ?>
					<div class="docs-sidebar__divider"></div>
					<div class="docs-sidebar__heading">On this page</div>
					<?php foreach ($matches as $match) { ?>
						<a href="#<?= htmlspecialchars($match[1], ENT_QUOTES); ?>"
						   class="docs-sidebar__toc-link">
							<?= htmlspecialchars(strip_tags($match[2]), ENT_QUOTES); ?>
						</a>
					<?php } ?>
				<?php } ?>
			</nav>
		</aside>

		<main class="docs__main">
			<article class="docs__content">
				<?= $view->data['content']; ?>
			</article>

			<footer class="docs__footer">
				<p>
					See an error or want to contribute?
					<a href="https://github.com/webifycms/app/tree/main/docs" target="_blank" rel="noopener">
                        Edit on GitHub
                    </a>
				</p>
			</footer>
		</main>
	</div>
</section>

<?php $view->renderPartial('footer'); ?>
