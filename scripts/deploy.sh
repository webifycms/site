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
COMPOSE_FILE="compose.production.yml"

# Check if .env file exists
if [ ! -f .env ]; then
    echo "ERROR: .env file not found!"
    echo "Please create .env from .env.example and configure it."
    exit 1
fi

# Check if compose.production.yml exists
if [ ! -f "$COMPOSE_FILE" ]; then
    echo "ERROR: $COMPOSE_FILE not found!"
    echo "Please create it from compose.production.yml.example."
    exit 1
fi

# Pull latest changes (in case script is run manually)
echo ">>> Pulling latest changes..."
git pull origin main

# Stop existing containers
echo ">>> Stopping existing containers..."
docker compose -f "$COMPOSE_FILE" down

# Build new image (no cache for clean build)
echo ">>> Building Docker image..."
docker compose -f "$COMPOSE_FILE" build --no-cache

# Start containers
echo ">>> Starting containers..."
docker compose -f "$COMPOSE_FILE" up -d

# Wait for containers to be healthy
echo ">>> Waiting for containers to start..."
sleep 5

# Check if containers are running
if docker compose -f "$COMPOSE_FILE" ps | grep -q "Up"; then
    echo "=========================================="
    echo "  Deployment Complete!"
    echo "=========================================="
    docker compose -f "$COMPOSE_FILE" ps
else
    echo "ERROR: Containers failed to start!"
    docker compose -f "$COMPOSE_FILE" logs
    exit 1
fi
