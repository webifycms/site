<?php

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
		<div class="hero__badge">Getting Started</div>
		<h1 class="title">Install <em>WebifyCMS</em></h1>
		<p class="hero__subtitle">
			Get up and running in minutes with the official installer.
		</p>
	</div>
</section>

<section class="section section--raised">
	<div class="container">
		<article class="docs__content" style="margin: 0 auto;">
			<h1>Install WebifyCMS</h1>

			<p>
				The fastest way to get WebifyCMS running is with the
				<a href="https://github.com/webifycms/installer" target="_blank" rel="noopener">official installer</a>.
				It clones the app and its dependencies, configures your environment, and gets you to a working install in one step.
			</p>

			<h2>Prerequisites</h2>

			<ul>
				<li><strong>Git</strong> &mdash; required for cloning repositories.</li>
				<li><strong>PHP &ge; 8.4</strong> &mdash; required for the application to run (Development mode).</li>
				<li><strong>Composer V2</strong> &mdash; required for installing PHP dependencies (Development mode).</li>
			</ul>

			<h2>Quick Install</h2>

			<p>Create a directory, clone the installer, and run it:</p>

			<pre><code>mkdir ~/webifycms
cd ~/webifycms
git clone https://github.com/webifycms/installer.git
cd installer
chmod +x install.sh
./install.sh</code></pre>

			<p>
				Follow the on-screen prompts to choose between <strong>Development</strong> or <strong>Testing</strong> mode.
				The script will guide you through the rest.
			</p>

			<h2>What the Installer Does</h2>

			<ul>
				<li>Clones <a href="https://github.com/webifycms/app" target="_blank" rel="noopener">webifycms/app</a> and <a href="https://github.com/webifycms/ext-base" target="_blank" rel="noopener">ext-base</a>.</li>
				<li>Sets up your <code>.env</code> with sensible development defaults.</li>
				<li>Creates a local Composer config that symlinks extensions from a shared <code>extensions/</code> directory.</li>
				<li>Installs all PHP dependencies via Composer.</li>
				<li>Optionally starts the PHP built-in dev server on <code>localhost:8000</code>.</li>
			</ul>

			<h2>After Installation</h2>

			<p>Once the installer finishes, start the dev server:</p>

			<pre><code>php -S localhost:8000 -t app/public/</code></pre>

			<p>
				Then visit <a href="http://localhost:8000" target="_blank" rel="noopener">http://localhost:8000</a> in your browser.
			</p>

			<h2>Troubleshooting</h2>

			<ul>
				<li><strong>Permission denied</strong> &mdash; make sure you ran <code>chmod +x install.sh</code>.</li>
				<li><strong>Missing dependencies</strong> &mdash; the script checks for required tools and will prompt you to install anything that is missing.</li>
			</ul>

			<h2>Next Steps</h2>

			<ul>
				<li>Read the <a href="<?= $view->url('/docs'); ?>">documentation</a> to learn about the architecture.</li>
				<li>Browse the <a href="<?= $view->url('/extensions'); ?>">extensions</a> to see what is available.</li>
				<li>Check the <a href="https://github.com/webifycms/app" target="_blank" rel="noopener">source on GitHub</a> to contribute.</li>
			</ul>
		</article>
	</div>
</section>

<?php $view->renderPartial('footer'); ?>
