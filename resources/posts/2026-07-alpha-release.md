---
title: 'WebifyCMS First Alpha Release - The Bedrock'
slug: webifycms-first-alpha-release-the-bedrock
date: '2026-07-10'
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

With the base layer complete, work is now actively underway on `ext-user`, which will provide identity,
authentication, and authorization. Future extensions on the roadmap include content management (`ext-cms`),
an administration panel (`ext-admin`), and a marketplace for themes and extensions (`ext-marketplace`).

## How to Get Involved

WebifyCMS is fully open source under the MIT license. You can explore the codebase, review the architecture,
and follow along as the platform takes shape.

- **GitHub:** https://github.com/webifycms
- **Documentation:** https://webifycms.com/docs

While this alpha is not yet recommended for production environments, we encourage developers, architects,
and open-source enthusiasts to take a look, open issues, and share feedback. The architecture is designed
to be transparent — every layer, every boundary, and every decision is open for discussion.

## What Comes Next

The immediate focus is on completing `ext-user` and preparing for the first beta release.
Alongside that, we will continue expanding documentation, improving test coverage,
and refining the developer experience.

Stay tuned for more updates, and thank you for following the WebifyCMS journey.
