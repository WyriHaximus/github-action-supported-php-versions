FROM ghcr.io/wyrihaximusnet/php:8.5-nts-alpine-slim-root

COPY supported-versions.json upcoming-releases.json versions.php /app/

ENTRYPOINT ["php", "/app/versions.php"]
