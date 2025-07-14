FROM php

ENV NODE_ENV=development

RUN groupadd -r developer && useradd -r -g developer necrokochou

WORKDIR /var/www/html

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    autoconf \
    make \
    g++ \
    libicu-dev \
    libzip-dev \
    zlib1g-dev \
    libpq-dev \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install \
    pgsql \
    pdo_pgsql \
    intl \
    zip

RUN pecl install mongodb

RUN docker-php-ext-enable mongodb

COPY --from=composer /usr/bin/composer /usr/local/bin/composer

COPY . /var/www/html/

USER necrokochou

EXPOSE 8000

RUN composer install

CMD ["php", "-S", "0.0.0.0:8000", "router.php"]