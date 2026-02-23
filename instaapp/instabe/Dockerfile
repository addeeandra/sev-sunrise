FROM ghcr.io/digital-entropy/dokar-php/octane:8.3

ARG PUID=1000
ARG PGID=1000
ARG USER_CONTAINER=dokar
ARG DOCKER_ENV=production
ARG DOCKER_WORKERS=auto

ENV DOCKER_ENV=${DOCKER_ENV}
ENV DOCKER_WORKERS=${DOCKER_WORKERS}
ENV PUID=${PUID}
ENV PGID=${PGID}
ENV USER_CONTAINER=${USER_CONTAINER}

RUN mkdir -p /var/www && chown ${PUID}:${PGID} /var/www

# Copy only production files (exclude frontend source and build configs)
# Use .dockerignore patterns to control what gets copied
COPY --chown=${PUID}:${PGID} . /var/www

# Copy entrypoint.sh into root dir
COPY --chown=${PUID}:${PGID} entrypoint.sh /entrypoint.sh

# Switch to non-root user before creating runtime folders
USER ${USER_CONTAINER}

# Create required directories as the app user, then run composer
RUN mkdir -p bootstrap/cache storage/framework/cache storage/framework/sessions storage/framework/views && \
    composer install --no-dev --no-interaction && \
    php artisan storage:link

EXPOSE 80

CMD ["/entrypoint.sh"]