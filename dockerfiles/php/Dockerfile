FROM php:8.3-fpm

# Instalar dependências do sistema e atualizar pacotes
RUN apt-get update \
    && apt-get upgrade -y \
    && apt-get install -y --no-install-recommends \
        bash \
        curl \
        libpng-dev \
        libzip-dev \
        zlib1g-dev \
        libxml2-dev \
        nodejs \
        npm \
        iputils-ping \
        net-tools \
        wget \
        procps \
        vim \
        unzip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Instalar extensões do PHP
RUN docker-php-ext-install gd \
    && docker-php-ext-install zip \
    && docker-php-ext-install dom \
    && docker-php-ext-install pdo_mysql

# Instalar Composer (última versão segura)
RUN curl -fsSL https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN echo "memory_limit = 2048M" > /usr/local/etc/php/conf.d/memory-limit.ini \
    && echo 'max_execution_time = 240' > /usr/local/etc/php/conf.d/docker-php-maxexectime.ini \
    && echo "upload_max_filesize = 10M\npost_max_size = 10M\nmax_execution_time = 300" > /usr/local/etc/php/conf.d/99-custom.ini

# Configurar o diretório de trabalho
WORKDIR /var/www

# Expor a porta 9000
EXPOSE 9000