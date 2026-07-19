FROM php:8.4-fpm-alpine

# copy Composer binary into the container
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# sets working directory
WORKDIR /var/www/html

# copy the composer.json and composer.lock files
COPY composer.* .

# install dependencies by allowing Docker to use the auth.json file of the host
RUN composer install --no-dev --no-autoloader --no-progress --no-interaction

# copy application files to the working directory and change ownership
COPY . .

# dump the autoloader
RUN composer dump-autoload --classmap-authoritative --no-dev

# create non-root user
RUN addgroup -g 1000 -S app && adduser -u 1000 -S app -G app

# change ownership and permissions
RUN <<EOF
chown -R app:app /var/www/html
chmod -R 0750 runtime
chmod -R 0750 public/assets
chmod -R 0750 bin/console
EOF

# switch to non-root user
USER app

# expose port 9000 and start php-fpm
EXPOSE 9000
CMD ["php-fpm"]