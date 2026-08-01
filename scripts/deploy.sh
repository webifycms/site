#!/bin/bash
#
# WebifyCMS Deployment Script
#
# This script is called by GitHub Actions via SSH after merging to main.
# It can also be run manually on the server.
#
# Usage:
#   bash scripts/deploy.sh
#
set -euo pipefail

echo "=========================================="
echo "  WebifyCMS Deployment"
echo "=========================================="

# Configuration
APP_NAME="${APP_ID:-webifycms}"

# Check if .env file exists
if [ ! -f .env ]; then
    echo "ERROR: .env file not found!"
    echo "Please create .env from .env.example and configure it."
    exit 1
fi

# Pull latest changes (in case the script is run manually). The machine-specific
# compose.override.yml may differ from git (or not be tracked at all), so move it
# aside first, pull, then restore it — this keeps the pull from ever conflicting
# with or deleting the local override.
echo ">>> Pulling latest changes..."
[ -f compose.override.yml ] && mv compose.override.yml /tmp/compose.override.yml.deploy.bak || true
git pull origin main
[ -f /tmp/compose.override.yml.deploy.bak ] && mv /tmp/compose.override.yml.deploy.bak compose.override.yml || true

# Compose files used on the server. compose.override.yml is gitignored and
# machine-specific: the server keeps its own copy (e.g. to attach the nginx
# container to the shared-proxy network), merged on top of the portable base.
COMPOSE=(docker compose -f compose.yml -f compose.override.yml)

# Stop existing containers and remove volumes for a clean build
echo ">>> Stopping existing containers..."
"${COMPOSE[@]}" down -v

# Build new image (no cache for clean build)
echo ">>> Building Docker image..."
"${COMPOSE[@]}" build --no-cache

# Start containers
echo ">>> Starting containers..."
"${COMPOSE[@]}" up -d

# Wait for containers to be healthy
echo ">>> Waiting for containers to start..."
sleep 5

# Check if containers are running
if "${COMPOSE[@]}" ps | grep -q "Up"; then
    echo ">>> Installing Composer dependencies inside container..."
    "${COMPOSE[@]}" exec -T app composer install --no-dev --no-autoloader --no-progress --no-interaction
    "${COMPOSE[@]}" exec -T app composer dump-autoload --classmap-authoritative --no-dev

    echo ">>> Clearing page cache..."
    "${COMPOSE[@]}" exec -T app php bin/console pageCache:clear

    echo ">>> Setting directory permissions inside container..."
    "${COMPOSE[@]}" exec -T app mkdir -p runtime/cache/rate-limiter
    "${COMPOSE[@]}" exec -T app chmod -R 0775 runtime
    "${COMPOSE[@]}" exec -T app chmod -R 0775 public/assets 2>/dev/null || true

    echo "=========================================="
    echo "  Deployment Complete!"
    echo "=========================================="
    "${COMPOSE[@]}" ps
else
    echo "ERROR: Containers failed to start!"
    "${COMPOSE[@]}" logs
    exit 1
fi
