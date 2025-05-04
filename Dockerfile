# Usa una imagen oficial de PHP con Apache
FROM php:8.1-apache

# Copia todos los archivos del proyecto al contenedor
COPY . /var/www/html/

# Instala extensiones necesarias para MySQL
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Establece permisos
RUN chown -R www-data:www-data /var/www/html

# Habilita mod_rewrite (opcional si usas .htaccess)
RUN a2enmod rewrite
