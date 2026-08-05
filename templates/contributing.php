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
		<div class="hero__badge">Community</div>
		<h1 class="title">Contribute to <em>WebifyCMS</em></h1>
		<p class="hero__subtitle">
			WebifyCMS is fully open source under the MIT license. Help us build the future of PHP web applications.
		</p>
	</div>
</section>

<section class="section section--raised">
	<div class="container">
		<article class="docs__content" style="margin: 0 auto;">
			<h1>Contributing</h1>

			<p>Thank you for being here and considering a contribution to WebifyCMS. Together we can make great software.
			Contributions, issues, and feature requests are all welcome.</p>

			<h2>Developer Certificate of Origin (DCO)</h2>

			<p>To keep WebifyCMS open, secure, and legally clean, we use a <strong>Developer Certificate of Origin (DCO)</strong>
			instead of a complex contributor agreement. By contributing, you certify that you have the right to submit the
			code under the project's open-source license.</p>

			<p>All you need to do is sign off on your git commits with the <code>-s</code> flag:</p>

			<pre><code>git commit -s -m "Your meaningful commit message"</code></pre>

			<p>This appends a <code>Signed-off-by</code> line to your commit message. Pull requests with unsigned commits
			cannot be merged.</p>

			<h2>How to Get Involved</h2>

			<ul>
				<li><strong>Explore the codebase</strong> &mdash; start with the <a href="https://github.com/webifycms/app" target="_blank" rel="noopener">main application</a> and the <a href="https://github.com/webifycms" target="_blank" rel="noopener">extension repositories</a>.</li>
				<li><strong>Report bugs and open issues</strong> on the <a href="https://github.com/webifycms/app/issues" target="_blank" rel="noopener">issues page</a>.</li>
				<li><strong>Follow the roadmap</strong> on the <a href="https://github.com/orgs/webifycms/projects/4" target="_blank" rel="noopener">project board</a>.</li>
				<li><strong>Read the wiki</strong> for architecture notes and discussions.</li>
				<li><strong>Respect the code of conduct</strong> &mdash; see the <a href="https://github.com/webifycms/app/blob/main/.github/CODE_OF_CONDUCT.md" target="_blank" rel="noopener">Code of Conduct</a>.</li>
			</ul>

			<h2>Setting Up for Development</h2>

			<p>Prerequisites: an environment capable of running Docker or Docker Desktop.</p>

			<ol>
				<li>Clone the main repository and use the <a href="https://github.com/webifycms/installer" target="_blank" rel="noopener">WebifyCMS Installer</a> to initialize the local stack.</li>
				<li>Copy the sample environment file: <code>cp .env.sample .env</code>, then adjust the values for your local setup.</li>
				<li>Install dependencies with <code>composer install</code>.</li>
				<li>Run the application: <code>php -S localhost:8000 -t public/</code>.</li>
			</ol>

			<h2>Full Contribution Notes</h2>

			<p>For the complete guide, including the DCO details and local stack layout, read the
			<a href="https://github.com/webifycms/app/blob/main/.github/CONTRIBUTING.md" target="_blank" rel="noopener">CONTRIBUTING.md</a>
			in the main repository.</p>
		</article>
	</div>
</section>

<?php $view->renderPartial('footer'); ?>
