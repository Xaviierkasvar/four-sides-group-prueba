FROM php:8.2-cli

# Argumentos definidos en docker-compose.yml
ARG user
ARG uid

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    nodejs \
    npm \
    libzip-dev

# Limpiar cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Instalar extensiones PHP
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Obtener Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Crear usuario de sistema para ejecutar comandos
RUN useradd -G www-data,root -u $uid -d /home/$user $user
RUN mkdir -p /home/$user/.composer && \
    chown -R $user:$user /home/$user

# Establecer directorio de trabajo
WORKDIR /var/www

# Copiar archivos de composición
COPY composer.json composer.lock ./

# Instalar dependencias de Composer
RUN composer install --no-scripts --no-autoloader

# Copiar archivos de la aplicación
COPY . /var/www/

# Instalar dependencias y optimizar autoloader
RUN composer dump-autoload --optimize

# Dar permisos a directorios requeridos
RUN chown -R $user:$user /var/www/storage /var/www/bootstrap/cache
RUN chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# Copiar y dar permisos al script de entrada
COPY entrypoint.sh /var/www/entrypoint.sh
RUN chmod +x /var/www/entrypoint.sh

# Cambiar al usuario creado
USER $user

# Exponer puerto para artisan serve
EXPOSE 8000

# Usar el script de entrada en lugar del comando directo
ENTRYPOINT ["/var/www/entrypoint.sh"]