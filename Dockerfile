# Usar PHP 8.1 con Apache
FROM php:8.1-apache

# Instalar extensiones necesarias
RUN docker-php-ext-install pdo pdo_mysql

# Habilitar mod_rewrite para Apache
RUN a2enmod rewrite

# Configurar el DocumentRoot
ENV APACHE_DOCUMENT_ROOT /var/www/html

# Actualizar la configuración de Apache
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Copiar archivos del proyecto
COPY . /var/www/html/

# Dar permisos correctos
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Crear un script de inicio personalizado
RUN echo '#!/bin/bash\n\
set -e\n\
\n\
# Establecer puerto por defecto si no está definido\n\
if [ -z "$PORT" ]; then\n\
    export PORT=80\n\
fi\n\
\n\
echo "Configurando Apache para el puerto $PORT"\n\
\n\
# Configurar ports.conf\n\
echo "Listen $PORT" > /etc/apache2/ports.conf\n\
\n\
# Configurar el virtual host\n\
sed -i "s/<VirtualHost \*:80>/<VirtualHost *:$PORT>/" /etc/apache2/sites-available/000-default.conf\n\
\n\
# Iniciar Apache\n\
exec apache2-foreground' > /usr/local/bin/start-apache.sh \
    && chmod +x /usr/local/bin/start-apache.sh

# Exponer puerto dinámico
EXPOSE 10000

# Comando de inicio
CMD ["/usr/local/bin/start-apache.sh"]
