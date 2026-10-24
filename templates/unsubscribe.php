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
		<h1 class="title">Request <em>Data Deletion</em></h1>
		<p class="hero__subtitle">
			Request deletion of your personal data from our newsletter mailing list.
		</p>
	</div>
</section>

<section class="section section--raised">
	<div class="container">
		<article class="docs__content" style="margin: 0 auto; max-width: 640px;">
			<h1>Request Data Deletion</h1>

			<p>You can request deletion of your personal data (name and email address) from our newsletter mailing list at any time. This will permanently remove your information and you will no longer receive updates.</p>

			<div class="signup__card">
				<h3 class="signup__card-title">Delete your data</h3>
				<p class="signup__card-sub">Enter the email address associated with your subscription.</p>

				<form id="unsubscribeForm" novalidate>
					<div class="fg">
						<label for="uEmail">Email address</label>
						<input type="email" id="uEmail" name="email" placeholder="you@example.com" required autocomplete="email" />
						<div class="fg__error" id="emailErr">Please enter a valid email address</div>
					</div>
					<button type="submit" class="btn btn--accent">Delete my data &rarr;</button>
					<p class="signup__privacy">This will permanently remove your data from our mailing list.</p>
				</form>

				<div class="signup__success" id="unsubscribeSuccess" style="display: none;">
					<div class="signup__success-icon">&#10003;</div>
					<h3>Data deleted</h3>
					<p>Your personal data has been removed. You will no longer receive updates from WebifyCMS.</p>
				</div>
			</div>

			<div style="margin-top: 2rem; padding-top: 2rem; border-top: 1px solid var(--rule);">
				<h2>What data is deleted</h2>
				<ul>
					<li>Your name</li>
					<li>Your email address</li>
					<li>Any associated subscription preferences</li>
				</ul>

				<h2>What happens after deletion</h2>
				<ul>
					<li>You will no longer receive any emails from WebifyCMS</li>
					<li>Your data is permanently removed from our mailing list (Keila)</li>
					<li>This action cannot be undone; you would need to subscribe again to rejoin</li>
				</ul>
			</div>
		</article>
	</div>
</section>

<?php $view->renderPartial('footer'); ?>