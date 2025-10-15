# Usa a imagem oficial do PHP com Apache
FROM php:8.2-apache

# Atualiza o sistema e instala pacotes necessários antes de instalar extensões PHP
RUN apt-get update && apt-get install -y \
    libicu-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    && docker-php-ext-configure intl \
    && docker-php-ext-install mysqli pdo pdo_mysql intl mbstring zip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Ativa o mod_rewrite do Apache (necessário para CodeIgniter)
RUN a2enmod rewrite

# Define o diretório de trabalho
WORKDIR /var/www/html

# Copia os arquivos do projeto para dentro do container
COPY . .

# Instala o Composer (para dependências do CI4)
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Instala dependências PHP (sem pacotes de desenvolvimento)
RUN composer install --no-dev --optimize-autoloader

# Define a pasta /public como raiz do Apache
RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf

# Expõe a porta padrão do servidor web
EXPOSE 80

# Comando padrão para iniciar o Apache
CMD ["apache2-foreground"]
