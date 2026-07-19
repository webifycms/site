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
APP_BASE_URL=your-domain.com

# Docker ports
NGINX_PORT=80
NGINX_PORT_SSL=443

# Error tracking (Sentry)
ERROR_TRACKING_DSN=https://your-sentry-dsn

# Analytics
ANALYTICS_SCRIPT_URL=https://your-analytics.com/umami.js
ANALYTICS_WEBSITE_ID=your-website-id

# Mail list (Listmonk)
MAIL_LIST_URL=https://your-listmonk-instance.com
MAIL_LIST_USERNAME=your-username
MAIL_LIST_PASSWORD=your-password
```

### 5. Set Up SSL Certificates

See [SSL_SETUP.md](./SSL_SETUP.md) for detailed instructions on generating SSL certificates.

```bash
# Create SSL directory
mkdir -p docker/nginx/ssl

# Generate self-signed certificate (for testing)
openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
  -keyout docker/nginx/ssl/server.key \
  -out docker/nginx/ssl/server.crt

# Set proper permissions
chmod 600 docker/nginx/ssl/server.key
```

### 6. Initial Build and Start

```bash
# Build and start containers
docker compose -f compose.production.yml up -d --build

# Verify containers are running
docker compose -f compose.production.yml ps

# Check logs
docker compose -f compose.production.yml logs -f
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
3. **Deploy job** (only if tests pass): SSHes into the server and runs `scripts/deploy.sh`
4. The deploy script pulls the latest code, rebuilds Docker containers, and restarts them

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
docker compose -f compose.production.yml down
docker compose -f compose.production.yml build --no-cache
docker compose -f compose.production.yml up -d

# Or go back to the previous version
git checkout main~1
```

## Troubleshooting

### Containers won't start

```bash
# Check container logs
docker compose -f compose.production.yml logs app
docker compose -f compose.production.yml logs server

# Check if ports are in use
sudo netstat -tulpn | grep -E ':(80|443)\s'
```

### Permission errors

```bash
# Fix runtime directory permissions
sudo chown -R deploy:docker runtime/
chmod -R 750 runtime/
```

### SSL certificate issues

```bash
# Verify certificate
openssl x509 -in docker/nginx/ssl/server.crt -text -noout

# Check nginx configuration
docker compose -f compose.production.yml exec server nginx -t
```

### Database connection errors

Ensure your `.env` file has the correct database credentials and that the database server is accessible from the Docker network.

## Security Notes

- Never commit `.env` or SSL certificates to version control
- Use strong, unique passwords for all services
- Enable `APP_DEBUG=false` in production
- Keep Docker and system packages updated
- Regularly rotate SSH keys and API tokens
- Monitor Sentry for errors and performance issues
