#!/bin/sh
set -e

# ensure writable directories exist
mkdir -p runtime/cache/rate-limiter

# change permissions to directories
chmod -R 0777 runtime
chmod -R 0777 public/assets
[ -f bin/console ] && chmod 0777 bin/console

exec php-fpm
