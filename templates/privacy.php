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
		<div class="hero__badge">Legal</div>
		<h1 class="title">Privacy <em>Policy</em></h1>
		<p class="hero__subtitle">
			How we collect, use, and protect your data.
		</p>
		<p class="hero__updated">Last updated: July 11, 2026</p>
	</div>
</section>

<section class="section section--light">
	<div class="container">
		<article class="docs__content" style="margin: 0 auto;">
			<h1>Privacy Policy</h1>

			<p>WebifyCMS ("we", "us", "our") operates the website at <a href="https://webifycms.com" target="_blank" rel="noopener">webifycms.com</a>. This Privacy Policy explains what data we collect, how we use it, and your rights.</p>

			<h2>Data We Collect</h2>

			<h3>Newsletter Signup</h3>
			<p>When you subscribe to launch updates via our website form, we collect:</p>
			<ul>
				<li>Your name</li>
				<li>Your email address</li>
			</ul>
			<p>This data is stored until you unsubscribe. You can request deletion at any time by contacting <a href="mailto:mshifreen@gmail.com">mshifreen@gmail.com</a>.</p>

			<h3>Analytics (Plausible Analytics)</h3>
			<p>We use <a href="https://plausible.io" target="_blank" rel="noopener">Plausible Analytics</a>, a privacy-focused analytics tool. Plausible:</p>
			<ul>
				<li>Does <strong>not</strong> use cookies</li>
				<li>Does <strong>not</strong> collect personal data</li>
				<li>Tracks only anonymous usage statistics: pages visited, referrer, browser type, country</li>
				<li>All data is processed in the EU and is fully GDPR, CCPA, and PECR compliant</li>
			</ul>

			<h3>Error Monitoring (GlitchTip)</h3>
			<p>We use <a href="https://glitchtip.com" target="_blank" rel="noopener">GlitchTip</a> to monitor application errors. When an error occurs, GlitchTip may collect:</p>
			<ul>
				<li>IP address (anonymized after 30 days)</li>
				<li>Browser type and version</li>
				<li>Operating system</li>
				<li>Error stack traces and technical details</li>
			</ul>
			<p>This data is used solely to identify and fix bugs. It is not used for tracking or advertising.</p>

			<h2>How We Use Your Data</h2>
			<p>We use the collected data to:</p>
			<ul>
				<li>Understand how visitors use our website</li>
				<li>Identify and fix application errors</li>
				<li>Improve our website and services</li>
			</ul>

			<h2>Data Retention</h2>
			<ul>
				<li><strong>Analytics data:</strong> Retained indefinitely (anonymous, no personal data)</li>
				<li><strong>Error data:</strong> Automatically purged after 90 days</li>
			</ul>

			<h2>Your Rights</h2>
			<p>Depending on your jurisdiction, you may have the right to:</p>
			<ul>
				<li>Access what data we hold about you</li>
				<li>Request deletion of your data</li>
				<li>Opt out of data collection</li>
			</ul>
			<p>To exercise these rights, contact us at <a href="mailto:mshifreen@gmail.com">mshifreen@gmail.com</a>.</p>

			<h2>Third-Party Services</h2>
			<ul>
				<li><a href="https://plausible.io" target="_blank" rel="noopener">Plausible Analytics</a> — analytics (no cookies, EU-hosted)</li>
				<li><a href="https://glitchtip.com" target="_blank" rel="noopener">GlitchTip</a> — error monitoring</li>
				<li><a href="https://listmonk.app" target="_blank" rel="noopener">Listmonk</a> — newsletter/mailing list (self-hosted, no third-party data sharing)</li>
				<li><a href="https://github.com" target="_blank" rel="noopener">GitHub</a> — code hosting and issue tracking</li>
			</ul>

			<h2>Changes to This Policy</h2>
			<p>We may update this Privacy Policy from time to time. Changes will be posted on this page with an updated revision date.</p>

			<h2>Contact</h2>
			<p>For questions about this Privacy Policy, contact us at <a href="mailto:mshifreen@gmail.com">mshifreen@gmail.com</a>.</p>
		</article>
	</div>
</section>

<?php $view->renderPartial('footer'); ?>
