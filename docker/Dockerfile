FROM php:8.3-fpm-alpine

# Install system dependencies
RUN apk add --no-cache \
    git \
    curl \
    libpng-dev \
    libxml2-dev \
    zip \
    unzip \
    oniguruma-dev \
    icu-dev \
    freetype-dev \
    libjpeg-turbo-dev \
    libzip-dev \
    supervisor \
    openssh-server \
    nginx \
    apache2-utils \
    autoconf \
    gcc \
    g++ \
    make \
    dcron

# Clear cache
RUN rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd intl zip

# Install Redis extension using PECL
RUN pecl install redis && docker-php-ext-enable redis

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configure SSH
RUN mkdir /var/run/sshd
RUN echo 'root:root' | chpasswd
RUN sed -i 's/#PermitRootLogin prohibit-password/PermitRootLogin yes/' /etc/ssh/sshd_config
RUN ssh-keygen -A

# Configure PHP-FPM
RUN sed -i 's/listen = 127.0.0.1:9000/listen = 9000/' /usr/local/etc/php-fpm.d/www.conf
RUN sed -i 's/;listen.owner = www-data/listen.owner = www-data/' /usr/local/etc/php-fpm.d/www.conf
RUN sed -i 's/;listen.group = www-data/listen.group = www-data/' /usr/local/etc/php-fpm.d/www.conf
RUN sed -i 's/;listen.mode = 0660/listen.mode = 0660/' /usr/local/etc/php-fpm.d/www.conf

# Set working directory
WORKDIR /var/www/html

# Configure nginx user and directories
RUN adduser -D -s /bin/sh nginx nginx || true
RUN mkdir -p /var/log/nginx /var/lib/nginx/tmp /var/cache/nginx
RUN chown -R nginx:nginx /var/log/nginx /var/lib/nginx /var/cache/nginx
RUN chmod -R 755 /var/log/nginx /var/lib/nginx /var/cache/nginx

# Copy configurations
COPY docker/nginx.conf /etc/nginx/nginx.conf
COPY docker/default.conf /etc/nginx/conf.d/default.conf
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/start.sh /start.sh

# Create .htpasswd file
RUN touch /etc/nginx/.htpasswd

# Make start script executable
RUN chmod +x /start.sh

# Create log directories
RUN mkdir -p /var/log/supervisor /var/log/nginx

# Expose ports
EXPOSE 80 443 22 9000

# Start supervisor which will manage all services
CMD ["/start.sh"]
