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

# Pull latest changes (in case script is run manually)
echo ">>> Pulling latest changes..."
git pull origin main

# Stop existing containers and remove volumes for a clean build
echo ">>> Stopping existing containers..."
docker compose down -v

# Build new image (no cache for clean build)
echo ">>> Building Docker image..."
docker compose build --no-cache

# Start containers
echo ">>> Starting containers..."
docker compose up -d

# Wait for containers to be healthy
echo ">>> Waiting for containers to start..."
sleep 5

# Check if containers are running
if docker compose ps | grep -q "Up"; then
    echo ">>> Installing Composer dependencies inside container..."
    docker compose exec -T app composer install --no-dev --no-autoloader --no-progress --no-interaction
    docker compose exec -T app composer dump-autoload --classmap-authoritative --no-dev

    echo ">>> Clearing page cache..."
    docker compose exec -T app php bin/console pageCache:clear

    echo ">>> Setting directory permissions inside container..."
    docker compose exec -T app mkdir -p runtime/cache/rate-limiter
    docker compose exec -T app chmod -R 0775 runtime
    docker compose exec -T app chmod -R 0775 public/assets 2>/dev/null || true

    echo "=========================================="
    echo "  Deployment Complete!"
    echo "=========================================="
    docker compose ps
else
    echo "ERROR: Containers failed to start!"
    docker compose logs
    exit 1
fi
