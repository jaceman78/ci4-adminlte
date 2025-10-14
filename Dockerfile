# Imagem base com PHP e Apache
FROM php:8.2-apache

# Instala extensões necessárias ao CodeIgniter
RUN docker-php-ext-install mysqli pdo pdo_mysql intl mbstring

# Ativa o mod_rewrite para URLs amigáveis
RUN a2enmod rewrite

# Copia os arquivos do projeto
COPY . /var/www/html/

# Define o diretório de trabalho
WORKDIR /var/www/html/

# Instala o Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Instala dependências PHP
RUN composer install --no-dev --optimize-autoloader

# Configura o Apache para servir a pasta public/
RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf

# Expõe a porta 80
EXPOSE 80

# Inicia o Apache
CMD ["apache2-foreground"]
