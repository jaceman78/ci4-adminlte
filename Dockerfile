# Imagem base com PHP e extensões necessárias
FROM php:8.2-apache

# Instala extensões comuns usadas pelo CodeIgniter 4
RUN docker-php-ext-install mysqli pdo pdo_mysql intl mbstring

# Ativa o mod_rewrite do Apache (necessário para CI4)
RUN a2enmod rewrite

# Copia os ficheiros do projeto para o diretório web
COPY . /var/www/html/

# Define o diretório de trabalho
WORKDIR /var/www/html/

# Instala dependências do Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer install --no-dev --optimize-autoloader

# Configura o Apache para apontar para a pasta 'public'
RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf

# Expõe a porta 80
EXPOSE 80

# Comando para iniciar o servidor Apache
CMD ["apache2-foreground"]