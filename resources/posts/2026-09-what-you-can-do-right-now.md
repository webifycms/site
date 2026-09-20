---
title: "WebifyCMS Alpha — What You Can Do Right Now"
slug: webifycms-alpha-what-you-can-do-right-now
date: "2026-09-20"
category: "Guide"
---

# WebifyCMS Alpha — What You Can Do Right Now

We have been quiet since the [user extension update](/publishing/webifycms-user-extension-update-domain-complete-infrastructure-next), but we have not been idle. The biggest piece of work since then is the website you are reading right now. **webifycms.com is a real WebifyCMS application, running on the alpha and fully open source** at [github.com/webifycms/site](https://github.com/webifycms/site).

## This Site Is the Proof

WebifyCMS runs on WebifyCMS. The marketing site is not a static page or a theme demo — it is an actual WebifyCMS application built on the `ext-base` foundation and deployed in production. It is our way of eating our own dog food: before we ask anyone else to trust the architecture, we run our own site on it.

Every feature of this site is a small demonstration of what a WebifyCMS application already provides:

- **Publishing** — blog posts as Markdown with YAML front matter, a generated post index, pagination, and categories.
- **Docs** — rendered straight from the repository's Markdown, with a sidebar and an on-page table of contents.
- **Ecosystem pages** — showcases for extensions and themes.
- **Newsletter** — a sign-up flow with server-side validation and rate limiting.
- **SEO** — canonical URLs, meta tags, JSON-LD structured data, and automatic sitemap generation.
- **Discipline** — Clean Architecture with automated boundary tests, PHPStan at level 8, and a production-grade dependency injection container.

And because it is open source, none of this is a black box. The complete source — every template, controller, reader, and test — is public on [GitHub](https://github.com/webifycms/site). If you want to see how a real WebifyCMS application is put together, this site is the example.

## What You Can Do With the Alpha Right Now

Let us be honest: the alpha is a foundation, not a finished CMS. There is no admin panel, no default theme, and no content-management experience yet. What you _can_ do today is genuinely useful:

- **Install it and run it.** The [official installer](/install) gets you a working WebifyCMS application in minutes, or follow the Docker and manual setup in the site repository's [README](https://github.com/webifycms/site).
- **Ship a simple site in minutes by cloning this one.** The marketing site is a complete, working WebifyCMS application. Clone it, swap in your own Markdown posts and content, and you have a clean, content-driven site running locally in minutes — publishing, docs, and SEO already work out of the box, so you skip the setup and go straight to your content.
- **Explore the architecture.** `ext-base` ships a fully wired Clean Architecture skeleton — dependency injection via PHP-DI with a compiled container, routing through League Route, console commands with Symfony Console, logging with Monolog, and architectural tests that enforce the boundaries.
- **Get a head start on the roadmap.** The `ext-user` domain is complete — identity, authentication, and authorization with full unit-test coverage — and its infrastructure is under active development.

## Run It Yourself

The fast path:

```bash
git clone https://github.com/webifycms/site.git
cd site
composer install
npm install
npm run build
```

Serve the `public/` directory with any web server and you have this site running locally — including the publishing pipeline, the docs section, and the newsletter form. From there, making it your own is just editing posts and content — a simple site, in a few minutes. Or start from a blank application with the [installer](https://github.com/webifycms/installer) and build yours on the same foundation.

## What Comes Next

The next milestone is the first beta. The pieces that unlock it are moving forward together: the `ext-user` infrastructure layer, the slot-based admin panel `ext-admin`, and the official default theme `theme-canvas`. When those land, WebifyCMS will stop being just a foundation and start being a CMS you can actually manage content with — followed by content management (`ext-cms`) and the marketplace for themes and extensions (`ext-marketplace`).

## How to Get Involved

WebifyCMS is fully open source under the MIT license, and so is [this website](https://github.com/webifycms/site).

- **WebifyCMS on GitHub:** <https://github.com/webifycms>
- **Application repository:** <https://github.com/webifycms/app>
- **Site repository:** <https://github.com/webifycms/site>
- **Documentation:** <https://webifycms.com/docs>
- **Contributing:** <https://webifycms.com/contributing>

Run it locally, read the source, open issues, and tell us what you build. Thanks for following the WebifyCMS journey.
