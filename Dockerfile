
FROM php:latest

WORKDIR /usr/local/src

RUN apt-get update && apt-get install -y \
    libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql

COPY . .

CMD ["sh", "entry.sh"]