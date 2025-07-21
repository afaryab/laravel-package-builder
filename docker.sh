#!/bin/bash

# Laravel Docker Management Script

case "$1" in
    "start")
        echo "🚀 Starting Laravel Docker containers..."
        docker-compose up -d
        echo "✅ Containers started"
        ;;
    "stop")
        echo "🛑 Stopping Laravel Docker containers..."
        docker-compose down
        echo "✅ Containers stopped"
        ;;
    "restart")
        echo "🔄 Restarting Laravel Docker containers..."
        docker-compose down
        docker-compose up -d
        echo "✅ Containers restarted"
        ;;
    "logs")
        echo "📋 Showing container logs..."
        docker-compose logs -f
        ;;
    "ssh")
        echo "🔐 Connecting to artisan container via SSH..."
        ssh root@localhost -p 2222
        ;;
    "artisan")
        echo "⚡ Running artisan command: ${@:2}"
        docker-compose exec php php artisan "${@:2}"
        ;;
    "composer")
        echo "📦 Running composer command: ${@:2}"
        docker-compose exec php composer "${@:2}"
        ;;
    "reset")
        echo "🔄 Resetting application (clearing caches, running migrations)..."
        docker-compose exec php php artisan cache:clear
        docker-compose exec php php artisan config:clear
        docker-compose exec php php artisan route:clear
        docker-compose exec php php artisan migrate --force
        echo "✅ Application reset complete"
        ;;
    "rebuild")
        echo "🔨 Rebuilding Docker containers..."
        docker-compose down
        docker-compose build --no-cache
        docker-compose up -d
        echo "✅ Containers rebuilt"
        ;;
    "status")
        echo "📊 Container status:"
        docker-compose ps
        ;;
    *)
        echo "Laravel Docker Management Script"
        echo ""
        echo "Usage: $0 {command}"
        echo ""
        echo "Commands:"
        echo "  start     - Start all containers"
        echo "  stop      - Stop all containers"
        echo "  restart   - Restart all containers"
        echo "  logs      - Show container logs"
        echo "  ssh       - SSH into artisan container"
        echo "  artisan   - Run artisan command"
        echo "  composer  - Run composer command"
        echo "  reset     - Clear caches and run migrations"
        echo "  rebuild   - Rebuild containers from scratch"
        echo "  status    - Show container status"
        echo ""
        echo "Examples:"
        echo "  $0 start"
        echo "  $0 artisan migrate"
        echo "  $0 composer install"
        ;;
esac
