---
title: 'WebifyCMS First Alpha Release - The Bedrock'
slug: webifycms-first-alpha-release-the-bedrock
date: '2026-07-10'
updated: '2026-07-31'
---

# WebifyCMS First Alpha Release

We are excited to announce the very first alpha release of WebifyCMS — version `0.1.0-alpha "Bedrock"` is now available.
This marks a major milestone for the project, bringing together months of architectural work, domain modeling,
and infrastructure wiring into a cohesive foundation.

## What is Included

This initial alpha focuses on the core foundation (`ext-base`), which provides:

- A fully wired Clean Architecture skeleton with Domain, Application, and Infrastructure layers
- Dependency injection via PHP-DI with production-grade compiled container
- HTTP routing through League Route
- Console commands powered by Symfony Console
- Logging with Monolog
- Automatic architecture boundary enforcement through architectural tests

The entire foundation has been validated against a comprehensive suite of unit tests with strict coverage
metadata enforcement, ensuring the core is solid from day one.

## Current Development Status

With the base layer complete, work is now actively underway on the three pieces that unlock the first
beta: `ext-user` (identity, authentication, and authorization), `ext-admin` (the slot-based admin panel),
and the official default theme `webifycms/theme-canvas`, which powers the front-end. Future extensions on
the roadmap include content management (`ext-cms`) and a marketplace for themes and extensions
(`ext-marketplace`).

## How to Get Involved

WebifyCMS is fully open source under the MIT license. You can explore the codebase, review the architecture,
and follow along as the platform takes shape.

- **GitHub:** https://github.com/webifycms
- **Documentation:** https://webifycms.com/docs

While this alpha is not yet recommended for production environments, we encourage developers, architects,
and open-source enthusiasts to take a look, open issues, and share feedback. The architecture is designed
to be transparent — every layer, every boundary, and every decision is open for discussion.

## What Comes Next

The immediate focus is on completing `ext-user`. Because the user experience spans both the admin panel and
the front-end, that work goes hand in hand with `ext-admin` and the default `theme-canvas` theme — all three
are in progress together. Once they land, we will prepare the first beta release while continuing to expand
documentation, improve test coverage, and refine the developer experience.

Stay tuned for more updates, and thank you for following the WebifyCMS journey.
