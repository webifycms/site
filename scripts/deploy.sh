#!/bin/bash
#
# WebifyCMS Deployment Script
#
# This script is called by GitHub Actions via SSH after merging to main.
# It can also be run manually on the server.
#
# Usage:
#   DEPLOY_ENV=production bash scripts/deploy.sh
#   bash scripts/deploy.sh --force  # Skip interactive confirmation (use with caution)
#
set -euo pipefail

echo "=========================================="
echo "  WebifyCMS Deployment"
echo "=========================================="

# Safety guardrails
FORCE=false
for arg in "$@"; do
    case "$arg" in
        --force|-f)
            FORCE=true
            ;;
        *)
            echo "Unknown argument: $arg"
            echo "Usage: DEPLOY_ENV=production bash scripts/deploy.sh [--force]"
            exit 1
            ;;
    esac
done

# Prevent accidental execution outside of production/server context
if [ "${DEPLOY_ENV:-}" != "production" ] && [ "$FORCE" = false ]; then
    if [ -t 0 ]; then
        echo "WARNING: This script performs destructive operations (docker compose down -v)."
        echo "It is intended to run only on the production server or in CI."
        echo ""
        read -r -p "Type 'deploy' to confirm you want to proceed on this machine: " confirm
        if [ "$confirm" != "deploy" ]; then
            echo "Aborted."
            exit 1
        fi
    else
        echo "ERROR: Refusing to run non-interactively without DEPLOY_ENV=production or --force."
        echo "Set DEPLOY_ENV=production in the environment or pass --force if you know what you're doing."
        exit 1
    fi
fi

if [ "${DEPLOY_ENV:-}" = "production" ]; then
    echo ">>> Running in PRODUCTION mode (DEPLOY_ENV=production)"
elif [ "$FORCE" = true ]; then
    echo ">>> Running with --force (bypassing production environment check)"
fi
echo ""

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
