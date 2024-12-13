# Étape 1 : Utiliser une image PHP avec les extensions nécessaires
FROM php:8.2-fpm

# Argument pour l'utilisateur
ARG UID=1000
ARG GID=1000

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

# Créer un utilisateur non-root correspondant à l'hôte
RUN groupadd -g $GID sail && \
    useradd -u $UID -g sail -m sail && \
    chown -R sail:sail /home/sail

# Définir le répertoire de travail
WORKDIR /var/www/html

# Copier le projet avec les permissions correctes
COPY . .

# Configurer les permissions nécessaires
RUN chmod -R 775 /var/www/html/storage 
RUN chown -R sail:sail /var/www/html/storage
RUN chown -R sail:sail /var/www/html/bootstrap/cache

# Installer les dépendances PHP
RUN composer install --optimize-autoloader

# Exposer le port 9000 pour PHP-FPM
EXPOSE 9000

# Changer l'utilisateur par défaut
USER sail

# Commande par défaut
CMD ["php-fpm"]
