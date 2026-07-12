FROM php:8.4-fpm-alpine

# copy PHP extension installer image binary into the container
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

# installing php extensions
RUN chmod +x /usr/local/bin/install-php-extensions && \
    IPE_ICU_EN_ONLY=1 install-php-extensions gd \
    intl \
    bcmath \
    opcache

# sets working directory
WORKDIR /var/www/html

# copy application files to the working directory and change ownership
COPY . .

# change permissions to directories
RUN <<EOF
chmod -R 0755 runtime
chmod -R 0755 public/assets
chmod -R 0755 bin/console
EOF

# expose port 9000 and start php-fpm
EXPOSE 9000
CMD ["php-fpm"]