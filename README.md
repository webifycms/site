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

**Local development:**

```bash
# Start the containers (compose.override.yml is applied automatically)
docker compose up -d

# Install PHP dependencies
docker compose exec app composer install

# Install front-end dependencies and build assets
docker compose exec app npm install
docker compose exec app npm run build
```

**Production (server):**

```bash
# Ignore compose.override.yml — only the base compose.yml is used
docker compose -f compose.yml up -d
```

The site will be available at `http://localhost:3000` (configurable via `NGINX_PORT` in `.env`).

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
