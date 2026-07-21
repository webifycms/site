FROM php:8.4-fpm-alpine

# copy Composer binary into the container
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# sets working directory
WORKDIR /var/www/html

# copy the composer.json and composer.lock files
COPY composer.* .

# install dependencies by allowing Docker to use the auth.json file of the host
RUN composer install --no-dev --no-autoloader --no-progress --no-interaction

# dump the autoloader
RUN composer dump-autoload --classmap-authoritative --no-dev

# copy application source
COPY --chown=app:app . .

# create non-root user
RUN addgroup -g 1000 -S app && adduser -u 1000 -S app -G app

# change ownership
RUN chown -R app:app /var/www/html

# switch to non-root user
USER app

# expose port 9000 and start php-fpm
EXPOSE 9000
CMD ["php-fpm"]