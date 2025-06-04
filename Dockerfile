FROM php:8.1-apache

# Instalar extensiones de PHP
RUN docker-php-ext-install pdo pdo_mysql

# Habilitar módulos de Apache
RUN a2enmod rewrite headers

# Copiar configuración personalizada de Apache
COPY 000-default.conf /etc/apache2/sites-available/000-default.conf

# Copiar archivos del proyecto
COPY . /var/www/html/

# Establecer permisos
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Configurar ServerName
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Script de inicio que maneja puerto dinámico
RUN echo '#!/bin/bash\n\
set -e\n\
PORT=${PORT:-10000}\n\
echo "Configurando Apache en puerto $PORT"\n\
\n\
# Configurar puerto en ports.conf\n\
echo "Listen $PORT" > /etc/apache2/ports.conf\n\
\n\
# Actualizar VirtualHost con el puerto correcto\n\
sed -i "s/<VirtualHost \*:10000>/<VirtualHost *:$PORT>/" /etc/apache2/sites-available/000-default.conf\n\
\n\
# Verificar configuración\n\
apache2ctl configtest\n\
\n\
# Iniciar Apache\n\
exec apache2-foreground' > /usr/local/bin/start-server.sh

RUN chmod +x /usr/local/bin/start-server.sh

EXPOSE 10000

CMD ["/usr/local/bin/start-server.sh"]
