<?php

declare(strict_types=1);

use App\Infrastructure\Service\View;

/**
 * @var View $view
 */
?>
<?php $view->renderPartial('header'); ?>

<section class="hero">
	<div class="hero__orb hero__orb--1" aria-hidden="true"></div>
	<div class="hero__orb hero__orb--2" aria-hidden="true"></div>

	<div class="container hero__inner">
		<div class="hero__badge">
			<span class="hero__badge-dot"></span>
			Early development &mdash; open source
		</div>
		<h1 class="hero__title">
			The PHP CMS built<br />
			<em>the right way</em>
		</h1>
		<p class="hero__subtitle">
			WebifyCMS is an open-source application framework for crafting stunning web applications &mdash; built on Clean
			Architecture and Domain-Driven Design so your business logic stays protected, testable, and adaptable for years.
		</p>
		<div class="hero__actions">
			<a href="https://github.com/webifycms/app" target="_blank" rel="noopener" class="btn btn--solid">
				<svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
					<path
						d="M12 0C5.374 0 0 5.373 0 12c0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23A11.509 11.509 0 0112 5.803c1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576C20.566 21.797 24 17.3 24 12c0-6.627-5.373-12-12-12z"
					/>
				</svg>
				View on GitHub
			</a>
			<a href="#notify" class="btn btn--ghost">Get launch updates</a>
		</div>

		<div class="hero__stats">
			<div class="hero__stat">
				<div class="hero__stat-num">5</div>
				<div class="hero__stat-label">extensions planned</div>
			</div>
			<div class="hero__stat">
				<div class="hero__stat-num">PHP 8.4</div>
				<div class="hero__stat-label">modern foundation</div>
			</div>
			<div class="hero__stat">
				<div class="hero__stat-num">Level 8</div>
				<div class="hero__stat-label">PHPStan strictness</div>
			</div>
		</div>

		<div class="hero__code-wrap reveal">
			<div class="code-card">
				<div class="code-card__bar">
					<span class="code-card__dot code-card__dot--r"></span>
					<span class="code-card__dot code-card__dot--y"></span>
					<span class="code-card__dot code-card__dot--g"></span>
					<span class="code-card__path">src/Domain/User.php</span>
				</div>
				<pre><span class="c-cmt">// Pure domain logic. No framework, no DB, no UI.</span>

<span class="c-kw">final class</span> <span class="c-cls">User</span> <span class="c-kw">extends</span> <span class="c-cls">AggregateRoot</span>
{
    <span class="c-kw">public function</span> <span class="c-fn">activate</span>(): <span class="c-kw">void</span>
    {
        <span class="c-kw">if</span> (<span class="c-var">$this</span><span class="c-op">-&gt;</span>status<span class="c-op">-&gt;</span><span class="c-fn">isActive</span>()) {
            <span class="c-kw">throw</span> <span class="c-cls">UserAlreadyActiveException</span>::<span class="c-fn">create</span>();
        }

        <span class="c-var">$this</span><span class="c-op">-&gt;</span>status  <span class="c-op">=</span> <span class="c-cls">UserStatus</span>::Active;
        <span class="c-var">$this</span><span class="c-op">-&gt;</span><span class="c-fn">recordEvent</span>(<span class="c-kw">new</span> <span class="c-cls">UserWasActivated</span>(...));
    }
}</pre>
			</div>
		</div>
	</div>
</section>

<section class="section section--light" id="features">
	<div class="container features__inner">
		<div class="features__sticky reveal">
			<p class="label">Why WebifyCMS</p>
			<h2 class="title" style="margin-top: 16px">Architecture you'll still <em>appreciate</em> in five years</h2>
			<p class="lead" style="margin-top: 18px">
				Most CMS platforms couple business rules to the framework. WebifyCMS inverts that &mdash; your domain is pure
				PHP that happens to be served by the web.
			</p>
		</div>
		<div class="features__list">
			<div class="f-card reveal">
				<div class="f-card__icon">&loz;</div>
				<div>
					<div class="f-card__title">Clean Architecture</div>
					<p class="f-card__text">
						Domain layer is completely isolated from any framework, database, or UI. Swap out infrastructure without
						touching a single line of business logic.
					</p>
				</div>
			</div>
			<div class="f-card reveal">
				<div class="f-card__icon">&#9632;</div>
				<div>
					<div class="f-card__title">Domain-Driven Design</div>
					<p class="f-card__text">
						Rich entities, value objects, aggregate roots, and domain events &mdash; built properly. No anemic models,
						no leaking infrastructure into your domain.
					</p>
				</div>
			</div>
			<div class="f-card reveal">
				<div class="f-card__icon">&#9641;</div>
				<div>
					<div class="f-card__title">Extension Ecosystem</div>
					<p class="f-card__text">
						Every bounded context lives in its own Composer package: <code>ext-cms</code>, <code>ext-user</code>,
						<code>ext-admin</code>. Add only what you need.
					</p>
					<a href="<?= $view->url('/extensions'); ?>" class="f-card__cta">
						View all extensions
						<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="14" height="14">
							<path d="M5 12h14M12 5l7 7-7 7"/>
						</svg>
					</a>
				</div>
			</div>
			<div class="f-card reveal">
				<div class="f-card__icon">&#9678;</div>
				<div>
					<div class="f-card__title">PSR Standards</div>
					<p class="f-card__text">
						Built on PSR-7, -11, -14, -15, -17. Interoperable with the wider PHP ecosystem &mdash; swap any component as
						needed.
					</p>
				</div>
			</div>
			<div class="f-card reveal">
				<div class="f-card__icon">&#9635;</div>
				<div>
					<div class="f-card__title">PHPStan Level 8</div>
					<p class="f-card__text">
						Every class, method, and edge case analysed at the strictest level &mdash; PHPStan level 8 enforced in CI.
						Refactor with confidence.
					</p>
				</div>
			</div>
			<div class="f-card reveal">
				<div class="f-card__icon">&#9650;</div>
				<div>
					<div class="f-card__title">Themeable Admin</div>
					<p class="f-card__text">
						The admin panel is fully skinnable. Extend it via a slot system without coupling extensions to each other.
					</p>
				</div>
			</div>
			<div class="f-card reveal">
				<div class="f-card__icon">&#8646;</div>
				<div>
					<div class="f-card__title">Swappable Infrastructure</div>
					<p class="f-card__text">
						Every infrastructure component &mdash; UID generators, slugifiers, event publishers, markdown readers
						&mdash; is behind a domain contract. Replace Symfony's ULID generator with your own, or swap League
						CommonMark for a different parser. Your domain never knows the difference.
					</p>
				</div>
			</div>
		</div>
	</div>
</section>

<section class="section" id="roadmap">
	<div class="container">
		<div class="reveal" style="text-align: center; margin-bottom: 56px">
			<p class="label label--center">Progress</p>
			<h2 class="title" style="margin-top: 14px">What's built, what's next</h2>
			<p class="lead" style="margin: 14px auto 0">Building in public. Here's exactly where each piece stands.</p>
		</div>

		<div class="timeline">
			<div class="timeline__group reveal">
				<div class="timeline__group-header timeline__group-header--done">
					Completed
					<span class="timeline__group-badge timeline__group-badge--done">5</span>
				</div>
				<div class="timeline__item timeline__item--done">
					<div class="timeline__item-title">Base extension &mdash; shared-kernel with reusable components</div>
					<span class="timeline__item-tag tl-done">Done</span>
				</div>
				<div class="timeline__item timeline__item--done">
					<div class="timeline__item-title">Application skeleton with default configuration</div>
					<span class="timeline__item-tag tl-done">Done</span>
				</div>
				<div class="timeline__item timeline__item--done">
					<div class="timeline__item-title">Dev-tools &mdash; coding standards, static analysis, Rector</div>
					<span class="timeline__item-tag tl-done">Done</span>
				</div>
				<div class="timeline__item timeline__item--done">
					<div class="timeline__item-title">Domain layer &mdash; Identity, Authentication &amp; Authorization contracts</div>
					<span class="timeline__item-tag tl-done">Done</span>
				</div>
			</div>

			<div class="timeline__group reveal">
				<div class="timeline__group-header timeline__group-header--active">
					In progress
					<span class="timeline__group-badge timeline__group-badge--active">4</span>
				</div>
				<div class="timeline__item timeline__item--active">
					<div class="timeline__item-title">Admin extension &mdash; dashboard, settings, UI components</div>
					<span class="timeline__item-tag tl-active">In progress</span>
				</div>
				<div class="timeline__item timeline__item--active">
					<div class="timeline__item-title">User extension &mdash; registration, profiles, roles</div>
					<span class="timeline__item-tag tl-active">In progress</span>
				</div>
				<div class="timeline__item timeline__item--active">
					<div class="timeline__item-title">Default theme &mdash; admin user-interface, responsive, accessible front-end</div>
					<span class="timeline__item-tag tl-active">In progress</span>
				</div>
			</div>

			<div class="timeline__group reveal">
				<div class="timeline__group-header timeline__group-header--pending">
					Planned
					<span class="timeline__group-badge timeline__group-badge--pending">3</span>
				</div>
				<div class="timeline__item timeline__item--pending">
					<div class="timeline__item-title">Site &amp; blog extensions &mdash; content types, pages, posts</div>
					<span class="timeline__item-tag tl-pending">Planned</span>
				</div>
				<div class="timeline__item timeline__item--pending">
					<div class="timeline__item-title">Marketplace &mdash; theme &amp; extension distribution</div>
					<span class="timeline__item-tag tl-pending">Planned</span>
				</div>
				<div class="timeline__item timeline__item--pending">
					<div class="timeline__item-title">AI assist extension &mdash; content generation &amp; suggestions</div>
					<span class="timeline__item-tag tl-pending">Planned</span>
				</div>
			</div>
		</div>
	</div>
</section>

<section class="section section--light" id="showcase">
	<div class="container">
		<div class="reveal" style="text-align: center; margin-bottom: 48px">
			<p class="label label--center">Showcase</p>
			<h2 class="title" style="margin-top: 14px">Built with <em>WebifyCMS</em></h2>
			<p class="lead" style="margin: 14px auto 0">The website you're browsing right now is built with WebifyCMS &mdash; every page, post, and component.</p>
		</div>

		<div class="showcase__card reveal">
			<div class="showcase__browser-bar">
				<span class="code-card__dot code-card__dot--r"></span>
				<span class="code-card__dot code-card__dot--y"></span>
				<span class="code-card__dot code-card__dot--g"></span>
				<span class="showcase__url">webifycms.com</span>
				<span class="showcase__lock">
					<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" width="12" height="12">
						<rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
						<path d="M7 11V7a5 5 0 0 1 10 0v4"/>
					</svg>
				</span>
			</div>
			<div class="showcase__body">
				<img src="<?= $view->assetUrl('img/showcase_webifycms.jpg'); ?>" alt="WebifyCMS.com" width="100%" height="100%" />
			</div>
		</div>

		<p class="showcase__footnote reveal">
			Dogfooding in production &mdash;
			<a href="https://github.com/webifycms/site" target="_blank" rel="noopener">browse the source code on GitHub</a>
		</p>
	</div>
</section>

<section class="section" id="sponsors">
	<div class="container">
		<div class="reveal" style="text-align: center; margin-bottom: 48px">
			<p class="label label--center">Sponsors</p>
			<h2 class="title" style="margin-top: 14px">Supporting <em>WebifyCMS</em></h2>
			<p class="lead" style="margin: 14px auto 0">The people and organizations who make this project possible.</p>
		</div>

		<div class="sponsors__grid">
			<a href="https://daisycon.com" target="_blank" rel="noopener" class="sponsor__card reveal">
				<img src="<?= $view->assetUrl('img/daisycon.svg'); ?>" alt="Daisycon" width="300" height="85">
			</a>
		</div>

		<div class="sponsors__cta reveal">
			<p>Want to sponsor? <a href="mailto:mshifreen@gmail.com">Get in touch</a> &mdash; sponsor logos link back to your site.</p>
		</div>
	</div>
</section>

<?php $view->renderPartial('footer'); ?>
