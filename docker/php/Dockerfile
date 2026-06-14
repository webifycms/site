FROM php:8.4-fpm-alpine

# copy PHP extension installer image binary into the container
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

# installing php extensions
RUN chmod +x /usr/local/bin/install-php-extensions && \
    IPE_ICU_EN_ONLY=1 install-php-extensions gd \
    intl \
    bcmath \
    opcache

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

# change permissions to directories
RUN <<EOF
chmod -R 0777 runtime
chmod -R 0777 public/assets
chmod -R 0755 bin/console
EOF

# expose port 9000 and start php-fpm
EXPOSE 9000
CMD ["php-fpm"]