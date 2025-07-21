#!/bin/bash

# Laravel Docker Setup Script
set -e

echo "🚀 Setting up Laravel with Docker..."

# Copy environment file
if [ ! -f .env ]; then
    cp .env.example .env
    echo "✅ Environment file created"
fi

# Build and start containers
echo "🔨 Building Docker containers..."
docker-compose build

echo "🚀 Starting Docker containers..."
docker-compose up -d

# Wait for MySQL to be ready
echo "⏳ Waiting for MySQL to be ready..."
sleep 15

# Install dependencies
echo "📦 Installing Composer dependencies..."
docker-compose exec php composer install

# Generate application key
echo "🔑 Generating application key..."
docker-compose exec php php artisan key:generate

# Run migrations
echo "🗃️ Running database migrations..."
docker-compose exec php php artisan migrate --force

# Seed database if AUTH=internal
if grep -q "AUTH=internal" .env; then
    echo "👤 Seeding database with admin user..."
    docker-compose exec php php artisan db:seed --force
fi

# Set permissions
echo "🔐 Setting permissions..."
docker-compose exec php chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
docker-compose exec php chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

echo "✅ Setup complete!"
echo ""
echo "🌐 Your application is now available at:"
echo "   HTTP: http://localhost"
echo "   SSH (for artisan): ssh root@localhost -p 2222 (password: laravel)"
echo ""
echo "📋 Available authentication modes (set in .env):"
echo "   AUTH=none      - Public access (default)"
echo "   AUTH=basic     - Laravel auth (admin@example.com / password)"
echo "   AUTH=saml      - SAML authentication"
echo "   AUTH=oauth     - OAuth authentication"
echo ""
echo "🔧 To manage containers:"
echo "   docker-compose up -d     # Start services"
echo "   docker-compose down      # Stop services"
echo "   docker-compose logs -f   # View logs"
