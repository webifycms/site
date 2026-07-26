# ── Stage 1: Build frontend assets ──────────────────────────────
FROM node:24-alpine AS frontend

WORKDIR /build

# install dependencies first (layer cache)
COPY package.json package-lock.json ./
RUN npm ci --no-audit --no-fund

# copy source files needed by Vite
COPY assets/ ./assets/
COPY vite.config.js ./

# build production assets
RUN npm run build

# ── Stage 2: PHP-FPM runtime ───────────────────────────────────
FROM php:8.4-fpm-alpine

# copy Composer binary into the container
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# sets working directory
WORKDIR /var/www/html

# copy the composer.json and composer.lock files
COPY composer.* .

# install dependencies by allowing Docker to use the auth.json file of the host
RUN composer install --no-dev --no-autoloader --no-progress --no-interaction

# copy application source
COPY --chown=app:app . .

# copy built frontend assets from stage 1
COPY --from=frontend --chown=app:app /build/public/assets/ ./public/assets/
COPY --from=frontend --chown=app:app /build/public/.vite/ ./public/.vite/

# copy images from source into public (not handled by Vite build)
COPY --chown=app:app assets/img/ ./public/assets/img/

# dump the autoloader (after source copy so classmap includes app classes)
RUN composer dump-autoload --classmap-authoritative --no-dev

# create non-root user
RUN addgroup -g 1000 -S app && adduser -u 1000 -S app -G app

# change ownership
RUN chown -R app:app /var/www/html

# create runtime directories
RUN mkdir -p runtime/cache/rate-limiter && chown -R app:app runtime

# switch to non-root user
USER app

# expose port 9000 and start php-fpm
EXPOSE 9000
CMD ["php-fpm"]
