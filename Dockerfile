FROM php:8.2-cli

WORKDIR /app

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    && docker-php-ext-install bcmath \
    && rm -rf /var/lib/apt/lists/*

# Install Xdebug
RUN pecl install xdebug && docker-php-ext-enable xdebug

# Configure Xdebug for CLI
COPY docker/xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- \
    --install-dir=/usr/local/bin --filename=composer

# Copy composer files first for better caching
COPY composer.json composer.lock ./

# Install dependencies (ignore platform reqs for better compatibility)
RUN composer install --ignore-platform-reqs --no-scripts

# Copy all other files
COPY . .

# Make the CLI tool executable
RUN chmod +x bin/run-basket

# Run composer scripts
RUN composer dump-autoload --optimize

# Set permissions for CLI tool
RUN chmod +x bin/run-basket

CMD ["tail", "-f", "/dev/null"]  # Keep container running
