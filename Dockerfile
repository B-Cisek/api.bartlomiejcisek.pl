FROM serversideup/php:8.4-fpm-nginx-alpine AS base

FROM base AS development

# Switch to root so we can do root things
USER root

RUN install-php-extensions intl pdo_pgsql redis opcache
# Save the build arguments as a variable
ARG USER_ID
ARG GROUP_ID

# Use the build arguments to change the UID
# and GID of www-data while also changing
# the file permissions for NGINX
RUN docker-php-serversideup-set-id www-data $USER_ID:$GROUP_ID && \
    \
    # Update the file permissions for our NGINX service to match the new UID/GID
    docker-php-serversideup-set-file-permissions --owner $USER_ID:$GROUP_ID --service nginx

# Drop back to our unprivileged user
USER www-data


# Since we're calling "base", production isn't
# calling any of that permission stuff
FROM base AS production

COPY ./.docker/nginx/default.conf /etc/nginx/conf.d/default.conf

# Copy our app files as www-data (33:33)
COPY --chown=www-data:www-data . /var/www/html

RUN composer install --no-interaction --optimize-autoloader --no-dev
