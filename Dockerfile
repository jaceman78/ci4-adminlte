# Imagem base do PHP com Apache
FROM php:8.2-apache

# Instala ferramentas básicas de compilação e dependências para extensões PHP
RUN apt-get update && apt-get install -y \
    build-essential \
    libicu-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    pkg-config \
    libonig-dev \
    && docker-php-ext-configure intl \
    && docker-php-ext-install mysqli pdo pdo_mysql intl mbstring zip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Ativa o mod_rewrite para URLs amigáveis
RUN a2enmod rewrite

# Define a pasta de trabalho
WORKDIR /var/www/html

# Copia os arquivos do projeto para dentro do container
COPY . .

# Instala o Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Instala dependências PHP (sem pacotes de desenvolvimento)
RUN composer install --no-dev --optimize-autoloader || true

# Ajusta o Apache para apontar para /public
RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf

# Expõe a porta padrão
EXPOSE 80

# Comando padrão
CMD ["apache2-foreground"]
