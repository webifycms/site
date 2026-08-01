# Deployment Guide

This document covers deploying WebifyCMS to a self-hosted server using Docker and GitHub Actions.

## Prerequisites

- A Linux server (Ubuntu 22.04+ recommended) with Docker and Docker Compose installed
- A domain name pointed to your server's IP address
- SSH access to the server
- A GitHub repository with the following secrets configured

## Server Setup

### 1. Install Docker

```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Install Docker
curl -fsSL https://get.docker.com -o get-docker.sh
sudo sh get-docker.sh

# Add your user to the docker group
sudo usermod -aG docker $USER

# Install Docker Compose
sudo apt install docker-compose-plugin -y

# Log out and back in for group changes to take effect
```

### 2. Create Deploy User

```bash
# Create a dedicated deploy user
sudo adduser deploy
sudo usermod -aG docker deploy

# Switch to deploy user
su - deploy
```

### 3. Clone the Repository

```bash
cd /var/www
git clone https://github.com/webifycms/site.git webifycms.com
cd webifycms.com
```

### 4. Configure Environment

```bash
# Copy the example env file
cp .env.example .env

# Edit with your values
nano .env
```

Required environment variables:

```env
APP_NAME=WebifyCMS
APP_ID=webifycms
APP_VERSION=0.1.0
APP_ENV=production
APP_DEBUG=false
APP_BASE_URL=https://your-domain.com

# Docker port (mapped to host, the reverse proxy proxies to this)
NGINX_PORT=3000

# Error tracking (Sentry)
ERROR_TRACKING_DSN=https://your-sentry-dsn

# Analytics
ANALYTICS_SCRIPT_URL=https://your-analytics.com/umami.js
ANALYTICS_WEBSITE_ID=your-website-id

# Mail list (Keila)
MAIL_LIST_URL=https://your-keila-instance.com
MAIL_LIST_API_KEY=your-api-key
```

### 5. Reverse Proxy Setup

TLS and the public entry point are handled outside this project. The server's
gitignored `compose.override.yml` (created once on the server) adds whatever
networking the proxy needs — the details are environment-specific and kept out
of this repo.

### 6. Initial Build and Start

> **Important:** production uses the portable base `compose.yml` merged with the
> server's own, gitignored `compose.override.yml`, which holds environment-specific
> networking for the reverse proxy. It must NOT contain the local-dev overrides
> (which would switch to the dev image, host bind-mounts, and local TLS config).

```bash
# Build and start containers
docker compose -f compose.yml -f compose.override.yml up -d --build

# Verify containers are running
docker compose -f compose.yml -f compose.override.yml ps

# Check logs
docker compose -f compose.yml -f compose.override.yml logs -f
```

## GitHub Actions Setup

### 1. Configure Repository Secrets

Go to your GitHub repository → Settings → Secrets and variables → Actions, and add:

| Secret | Description | Example |
|--------|-------------|---------|
| `DEPLOY_SSH_HOST` | Server IP or hostname | `203.0.113.50` |
| `DEPLOY_SSH_USER` | SSH username | `deploy` |
| `DEPLOY_SSH_KEY` | Private SSH key (full PEM) | `-----BEGIN OPENSSH PRIVATE KEY-----...` |
| `DEPLOY_PATH` | Path to project on server | `/var/www/webifycms.com` |

### 2. Generate SSH Key Pair

On your local machine:

```bash
# Generate a new key pair
ssh-keygen -t ed25519 -C "github-actions" -f deploy_key

# Copy the public key to the server
ssh-copy-id -i deploy_key.pub deploy@your-server-ip

# Add the private key content to GitHub Secrets as DEPLOY_SSH_KEY
cat deploy_key
```

### 3. How Deployment Works

1. When you push to `main` or merge a PR, the workflow triggers
2. **Test job**: Runs static analysis, code style checks, and tests
3. **Deploy job** (only if tests pass):
   - Builds frontend assets locally (`npm run build`) — this also copies `assets/img/` into `public/assets/img/`
   - Uploads built assets (`public/assets/`, `public/.vite/`) to the server via SCP
   - SSHes into the server and runs `scripts/deploy.sh`
4. The deploy script pulls the latest code, rebuilds Docker containers (`docker compose -f compose.yml -f compose.override.yml`), and restarts them
5. The server image is built by `docker/php/Dockerfile`, which bundles the app code, the built assets, and the `assets/img/` images into the `app-data` volume — this is what nginx actually serves

### Environment separation

The project ships one portable `compose.yml` plus a machine-specific,
gitignored `compose.override.yml` merged on top in every environment:

| | Production (server) | Local development |
|---|---|---|
| Compose files | `docker compose -f compose.yml -f compose.override.yml` | `docker compose` (override auto-merged) |
| `app` image | `docker/php/Dockerfile` (code + assets + images bundled) | `docker/php/local.Dockerfile` (live host bind-mount) |
| nginx config | `docker/nginx/nginx.caddy.conf` (HTTP only, reverse proxy terminates TLS) | `docker/nginx/nginx.local.conf` (local TLS, self-signed certs) |
| SSL certs | Proxy-managed, none required | `docker/nginx/ssl/` (gitignored, local only) |
| Web root | shared `app-data` volume seeded from the image | host `./` bind-mounted into both containers |
| networks | `webifycms-site` + environment-specific networking (override) | `webifycms-site` |

`compose.override.yml` is gitignored so every environment keeps its own copy.
The committed one in the repo is the local-dev setup; the server keeps a minimal
production override with the environment-specific networking needed by the
reverse proxy. `scripts/deploy.sh` merges it explicitly via
`-f compose.yml -f compose.override.yml`.

## Manual Deployment

If you need to deploy manually:

```bash
# SSH into the server
ssh deploy@your-server-ip

# Navigate to the project
cd /var/www/webifycms.com

# Run the deploy script
bash scripts/deploy.sh
```

## Rollback

If something goes wrong, you can rollback to a previous version:

```bash
# SSH into the server
ssh deploy@your-server-ip

cd /var/www/webifycms.com

# Check out a specific commit or tag
git log --oneline -10  # Find the commit you want
git checkout <commit-hash>

# Rebuild and restart
docker compose -f compose.yml -f compose.override.yml down
docker compose -f compose.yml -f compose.override.yml build --no-cache
docker compose -f compose.yml -f compose.override.yml up -d

# Or go back to the previous version
git checkout main~1
```

## Troubleshooting

### Containers won't start

```bash
# Check container logs
docker compose -f compose.yml -f compose.override.yml logs app
docker compose -f compose.yml -f compose.override.yml logs server

# Check if the port configured in NGINX_PORT is in use
sudo netstat -tulpn | grep ":${NGINX_PORT} "
```

### Permission errors

```bash
# Fix runtime directory permissions
sudo chown -R deploy:docker runtime/
chmod -R 750 runtime/
```

### SSL certificate issues

TLS and certificates are handled by the reverse proxy. Verify the proxy is
running and the certificate for your domain is active and trusted.

## Security Notes

- Never commit `.env` or SSL certificates to version control
- Use strong, unique passwords for all services
- Enable `APP_DEBUG=false` in production
- Keep Docker and system packages updated
- Regularly rotate SSH keys and API tokens
- Monitor Sentry for errors and performance issues
