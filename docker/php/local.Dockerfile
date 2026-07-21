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

# copy entrypoint script
COPY docker/php/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# expose port 9000 and start php-fpm
EXPOSE 9000
ENTRYPOINT ["entrypoint.sh"]