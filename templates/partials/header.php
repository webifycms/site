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
<!doctype html>
<html lang="en">
	<head>
		<meta charset="UTF-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<?= $view->preloadEntryCss('assets/js/app.js'); ?>
		<style>:root{--paper:#08080c;--paper-raised:#0e0e14;--paper-surface:#12121a;--ink:#e2e2e8;--ink-soft:#9696a8;--ink-muted:#5a5a70;--violet:#a78bfa;--violet-lt:#c4b5fd;--violet-dk:#7c3aed;--violet-glass:rgba(167,139,250,.04);--violet-border:rgba(167,139,250,.12);--rule:rgba(167,139,250,.06);--font-display:"JetBrains Mono","DM Mono","Courier New",monospace;--font-body:"DM Sans",system-ui,sans-serif;--font-mono:"JetBrains Mono","DM Mono","Courier New",monospace}html,body{margin:0;padding:0;background:var(--paper);color:var(--ink);color-scheme:dark;font-family:var(--font-body);-webkit-font-smoothing:antialiased}.announce{position:fixed;top:0;left:0;right:0;z-index:1000;background:var(--violet-dk);color:#fff;font-size:.8125rem;text-align:center;padding:.5rem 1rem}.nav{position:fixed;left:0;right:0;z-index:100;padding:1rem 0;background:rgba(8,8,12,.8);backdrop-filter:blur(1rem);-webkit-backdrop-filter:blur(1rem)}.logo img{display:block}</style>

		<title><?= $view->getTitle(); ?></title>

		<meta name="description" content="<?= htmlspecialchars($view->description, ENT_QUOTES); ?>" />

		<meta property="og:title" content="<?= $view->getTitle(); ?>" />
		<meta property="og:description" content="<?= htmlspecialchars($view->description, ENT_QUOTES); ?>" />
		<meta property="og:url" content="<?= htmlspecialchars('' === $view->canonical ? $view->url() : $view->canonical, ENT_QUOTES); ?>" />
		<meta property="og:type" content="website" />
		<meta property="og:site_name" content="WebifyCMS" />
		<meta property="og:locale" content="en_US" />
		<meta property="og:image" content="<?= $view->assetUrl('img/logo.png'); ?>" />

		<meta name="twitter:card" content="summary_large_image" />

		<link rel="canonical" href="<?= htmlspecialchars('' === $view->canonical ? $view->url() : $view->canonical, ENT_QUOTES); ?>" />
        <link rel="icon" type="image/png" sizes="96x96" href="/favicon-96x96.png">
        <link rel="icon" href="/favicon.svg" type="image/svg+xml" sizes="any">
        <link rel="icon" href="/favicon.ico">
        <link rel="apple-touch-icon" href="/apple-touch-icon.png">
        <link rel="manifest" href="/site.webmanifest">
        <meta name="theme-color" content="#ffffff" media="(prefers-color-scheme: light)">
        <meta name="theme-color" content="#0f172a" media="(prefers-color-scheme: dark)">

		<?= $view->registerMetaTags(); ?>

		<link rel="preconnect" href="https://fonts.googleapis.com" />
		<link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=DM+Mono:wght@400;500&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;1,9..40,300&display=swap" rel="stylesheet" />

		<script type="application/ld+json">
		{
			"@context": "https://schema.org",
			"@type": "WebSite",
			"name": "WebifyCMS",
			"url": "<?= htmlspecialchars($view->url(), ENT_QUOTES); ?>",
			"description": "WebifyCMS is an open-source PHP application framework built on Clean Architecture and Domain-Driven Design."
		}
		</script>

		<?= $view->assetEntryHtml('assets/js/app.js'); ?>
	</head>

	<body>
        <header class="header" id="header">
            <div class="announce" id="announce">
                <p class="announce__text">
                    WebifyCMS <strong>0.1.0-alpha</strong> has been released &mdash;
                    <a href="<?= $view->url('/publishing/webifycms-first-alpha-release-the-bedrock'); ?>">Read the announcement</a>
                </p>
            </div>

            <script>
                (function () {
                    let  a = document.getElementById("announce");

                    if (a) {
                        document.documentElement.style.setProperty("--announce-h", a.offsetHeight + "px");
                    }
                })();
            </script>

            <nav class="nav" id="nav">
                <div class="nav__inner">
                    <a href="<?= $view->url(); ?>" class="logo">
                        <img src="<?= $view->assetUrl('img/logo.png'); ?>" alt="WebifyCMS" width="130" height="49.62" />
                    </a>

                    <ul class="nav__links" id="navLinks">
                        <li><a href="<?= $view->url(); ?>#features">Features</a></li>
                        <li><a href="<?= $view->url(); ?>#roadmap">Roadmap</a></li>
                        <li><a href="<?= $view->url('/install'); ?>"<?= '/install' === $view->currentPath ? ' class="active"' : ''; ?>>Install</a></li>
                        <li><a href="<?= $view->url('/extensions'); ?>"<?= '/extensions' === $view->currentPath ? ' class="active"' : ''; ?>>Extensions</a></li>
                        <li><a href="<?= $view->url('/publishing'); ?>"<?= '/publishing' === $view->currentPath ? ' class="active"' : ''; ?>>Publishing</a></li>
                        <li><a href="<?= $view->url('/docs'); ?>"<?= '/docs' === $view->currentPath ? ' class="active"' : ''; ?>>Docs</a></li>
                    </ul>

                    <div class="nav__actions">
                        <a href="https://github.com/webifycms/app" target="_blank" rel="noopener" class="gh-icon" aria-label="GitHub" title="GitHub">
                            <svg viewBox="0 0 16 16" fill="currentColor" aria-hidden="true" width="18" height="18">
                                <path d="M8 0C3.58 0 0 3.58 0 8c0 3.54 2.29 6.53 5.47 7.59.4.07.55-.17.55-.38 0-.19-.01-.82-.01-1.49-2.01.37-2.53-.49-2.69-.94-.09-.23-.48-.94-.82-1.13-.28-.15-.68-.52-.01-.53.63-.01 1.08.58 1.23.82.72 1.21 1.87.87 2.33.66.07-.52.28-.87.51-1.07-1.78-.2-3.64-.89-3.64-3.95 0-.87.31-1.59.82-2.15-.08-.2-.36-1.02.08-2.12 0 0 .67-.21 2.2.82.64-.18 1.32-.27 2-.27.68 0 1.36.09 2 .27 1.53-1.04 2.2-.82 2.2-.82.44 1.1.16 1.92.08 2.12.51.56.82 1.27.82 2.15 0 3.07-1.87 3.75-3.65 3.95.29.25.54.73.54 1.48 0 1.07-.01 1.93-.01 2.2 0 .21.15.46.55.38A8.013 8.013 0 0016 8c0-4.42-3.58-8-8-8z" />
                            </svg>
                        </a>
                        <button class="nav__toggle" id="navToggle" aria-label="Toggle navigation">
                            <span></span>
                            <span></span>
                            <span></span>
                        </button>
                    </div>
                </div>
            </nav>
        </header>
