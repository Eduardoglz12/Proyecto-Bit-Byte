# Usar una imagen oficial de PHP 8.2 con Servidor Apache
FROM php:8.2-apache

# Instalar las extensiones de PHP que necesitas (mysqli)
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Habilitar mod_rewrite (para URLs amigables, si las usas)
RUN a2enmod rewrite

# Copiar todo tu proyecto al directorio web del servidor
COPY . /var/www/html/

# (Opcional) Ajustar permisos
RUN chown -R www-data:www-data /var/www/html