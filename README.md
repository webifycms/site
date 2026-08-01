# WebifyCMS Site

[![PHP Version](https://img.shields.io/badge/PHP-8.4%2B-blue)](https://www.php.net)
[![License](https://img.shields.io/badge/license-MIT-green)](LICENSE.md)
[![Version](https://img.shields.io/badge/version-0.1.0--alpha-orange)](https://github.com/webifycms/site)

The repository serves for [webifycms.com](https://webifycms.com) site which runs on WebifyCMS alpha.

## Getting Started

### Prerequisites

- PHP >= 8.4
- [Composer](https://getcomposer.org/)
- Node.js >= 20 (for front-end assets)

### Docker Setup (recommended)

**Local development** — `compose.override.yml` is applied automatically by
`docker compose`, giving you live host bind-mounts (code and asset changes are
picked up instantly) and local HTTPS via self-signed certs:

```bash
# Host-side dependencies (target the same files the containers bind-mount)
npm install

# Start the containers
docker compose up -d

# Install PHP dependencies
docker compose exec app composer install

# Build front-end assets (this also copies assets/img/ → public/assets/img/)
docker compose exec app npm run build
```

The site is then available at `http://localhost:${NGINX_PORT}` and
`https://webifycms.com.local:${NGINX_PORT_SSL}` (both set in `.env`). For the
HTTPS hostname, add `127.0.0.1 webifycms.com.local` to `/etc/hosts` and trust
the self-signed cert in `docker/nginx/ssl/` once.

**Production (server)** — only the base `compose.yml` is used, so the
local-only override is never merged:

```bash
# Build and start (compose.override.yml is ignored)
docker compose -f compose.yml up -d --build
```

The two environments differ only in `compose.override.yml`:

| | Local (`docker compose`) | Production (`docker compose -f compose.yml`) |
|---|---|---|
| `app` image | `docker/php/local.Dockerfile` + host bind-mount | `docker/php/Dockerfile` (bundles code + assets + images) |
| nginx config | `docker/nginx/nginx.local.conf` (local TLS) | `docker/nginx/nginx.caddy.conf` (HTTP, Caddy terminates TLS) |
| SSL certs | `docker/nginx/ssl/` (gitignored, self-signed) | none (Caddy-managed) |
| Web root | host `./` bind-mounted | shared `app-data` volume seeded from the image |

The `compose.override.yml` used for local development:

```yaml
services:
  app:
    build:
      context: .
      dockerfile: ./docker/php/local.Dockerfile
    volumes:
      - ./:/var/www/html
  server:
    volumes:
      - ./:/var/www/html
      - ./docker/nginx/nginx.local.conf:/etc/nginx/conf.d/default.conf
      - ./docker/nginx/ssl:/etc/nginx/ssl:ro
    ports:
      - "${NGINX_PORT}:80"
      - "${NGINX_PORT_SSL}:443"
```

### Manual Setup

```bash
# PHP dependencies
composer install

# Front-end dependencies
npm install

# Copy environment file
cp .env.example .env

# Build front-end assets
npm run build
```

Serve the `public/` directory with your web server of choice (Nginx config example in `docker/nginx/nginx.caddy.conf`).

## Front-end Assets (Vite)

CSS and JavaScript source files live in the `assets/` directory and are processed by [Vite](https://vitejs.dev/).

```bash
# Development — starts Vite dev server with HMR on port 5173
npm run dev

# Production build — minifies and hashes files to public/assets/
npm run build
```

- CSS is extracted from inline `<style>` tags into `assets/css/app.css`
- JS is extracted from inline `<script>` tags into `assets/js/app.js`
- In development, `App\Infrastructure\Helper\Vite` serves assets from the Vite dev server
- In production, it reads `public/.vite/manifest.json` for hashed filenames
- `npm run build` also copies `assets/img/` into `public/assets/img/` so images referenced directly from templates (e.g. `logo.png`) are served in every environment — same as the production Dockerfile
- Add the Vite dev server origin (`http://localhost:5173`) to your `.env` if needed

### Asset structure

```
assets/
  css/app.css      ← all styles (design tokens, components, dark mode, responsive)
  js/app.js        ← all scripts (nav scroll, theme toggle, mobile menu, reveal, signup form)
  img/logo.png     ← logo icon mark (standalone)
```

## 🤝 Like to contribute?

Contributions, issues, and feature requests are welcome! Feel free to check the following pages:

- [Wiki Page](https://github.com/webifycms/app/wiki)
- [Project](https://github.com/orgs/webifycms/projects/4)
- [Contribution notes](https://github.com/webifycms/app/blob/main/CONTRIBUTING.md).
- [Issues page](https://github.com/webifycms/app/issues).
- [Code of conduct notes](https://github.com/webifycms/app/blob/main/CODE_OF_CONDUCT.md).

## ⭐️ Show your support

If this project interests you, please consider giving it a ⭐️!

## Author

👤 **Mohammed Shifreen** (Project Lead)

- Website: <https://mshifreen.com/>
- Github: [@Shifrin](https://github.com/Shifrin)
- LinkedIn: [@mshifreen](https://linkedin.com/in/mshifreen/)

## License

WebifyCMS is licensed under MIT license, see the [LICENSE.md](https://github.com/webifycms/app/blob/main/LICENSE.md)
file for details.
