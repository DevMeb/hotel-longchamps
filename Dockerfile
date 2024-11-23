# Étape 1 : Utiliser une image PHP avec les extensions nécessaires
FROM php:8.2-fpm

# Installer les dépendances système nécessaires
RUN apt-get update && apt-get install -y --no-install-recommends \
    libzip-dev \
    unzip \
    git \
    curl \
    vim \
    nano \
    libpng-dev \
    default-mysql-client \
    && docker-php-ext-install pdo_mysql zip gd \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Changer l'utilisateur par défaut en www-data
USER www-data

# Définir le répertoire de travail
WORKDIR /var/www/html

# Copier le projet avec les permissions correctes
COPY --chown=www-data:www-data . .

# Installer les dépendances PHP
RUN composer install --optimize-autoloader

# Exposer le port 9000 pour PHP-FPM
EXPOSE 9000

# Commande par défaut
CMD ["php-fpm"]