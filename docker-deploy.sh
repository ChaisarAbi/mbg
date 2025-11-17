#!/bin/bash

# Docker Deployment Script for MBG Laravel Application
# Usage: ./docker-deploy.sh [up|down|build|logs|exec-app|exec-db|backup|restore]

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Function to log messages
log() {
    echo -e "${GREEN}[$(date +'%Y-%m-%d %H:%M:%S')]${NC} $1"
}

warn() {
    echo -e "${YELLOW}[$(date +'%Y-%m-%d %H:%M:%S')]${NC} $1"
}

error() {
    echo -e "${RED}[$(date +'%Y-%m-%d %H:%M:%S')]${NC} $1"
}

info() {
    echo -e "${BLUE}[$(date +'%Y-%m-%d %H:%M:%S')]${NC} $1"
}

# Check if Docker is running
check_docker() {
    if ! docker info > /dev/null 2>&1; then
        error "Docker is not running. Please start Docker and try again."
        exit 1
    fi
}

# Check if docker-compose is available
check_docker_compose() {
    if ! command -v docker-compose &> /dev/null; then
        error "docker-compose is not installed. Please install it first."
        exit 1
    fi
}

# Initialize environment
init_env() {
    if [ ! -f .env ]; then
        warn "No .env file found. Creating from template..."
        cp .env.docker .env
        log "Please edit .env file with your actual configuration before continuing."
        exit 1
    fi
}

# Build and start containers
up() {
    log "Starting MBG application with Docker Compose..."
    docker-compose up -d
    log "Containers started successfully!"
    
    # Wait for services to be ready
    sleep 10
    
    # Run application setup
    setup_application
}

# Build containers without starting
build() {
    log "Building Docker images..."
    docker-compose build
    log "Build completed successfully!"
}

# Stop and remove containers
down() {
    log "Stopping and removing containers..."
    docker-compose down
    log "Containers stopped and removed!"
}

# Show logs
logs() {
    if [ -z "$1" ]; then
        docker-compose logs -f
    else
        docker-compose logs -f "$1"
    fi
}

# Execute command in app container
exec_app() {
    log "Executing command in app container..."
    docker-compose exec app "$@"
}

# Execute command in database container
exec_db() {
    log "Executing command in database container..."
    docker-compose exec mysql "$@"
}

# Setup application (migrations, seeding, etc.)
setup_application() {
    log "Setting up Laravel application..."
    
    # Install dependencies
    docker-compose exec app composer install --no-dev --optimize-autoloader
    
    # Generate application key if not exists
    if ! grep -q "APP_KEY=base64:" .env; then
        log "Generating application key..."
        docker-compose exec app php artisan key:generate
    fi
    
    # Run migrations
    log "Running database migrations..."
    docker-compose exec app php artisan migrate --force
    
    # Seed database if needed
    log "Seeding database..."
    docker-compose exec app php artisan db:seed --force
    
    # Build assets
    log "Building frontend assets..."
    docker-compose exec app npm install
    docker-compose exec app npm run build
    
    # Set permissions
    log "Setting file permissions..."
    docker-compose exec app chown -R www-data:www-data /var/www
    docker-compose exec app chmod -R 775 storage bootstrap/cache
    
    # Clear cache
    log "Clearing application cache..."
    docker-compose exec app php artisan config:cache
    docker-compose exec app php artisan route:cache
    docker-compose exec app php artisan view:cache
    
    log "Application setup completed successfully!"
}

# Backup database
backup() {
    local timestamp=$(date +%Y%m%d_%H%M%S)
    local backup_file="mbg_backup_${timestamp}.sql"
    
    log "Creating database backup..."
    docker-compose exec mysql mysqldump -u root -p${DB_ROOT_PASSWORD} ${DB_DATABASE} > "backups/${backup_file}"
    
    if [ $? -eq 0 ]; then
        log "Backup created successfully: backups/${backup_file}"
    else
        error "Backup failed!"
        exit 1
    fi
}

# Restore database
restore() {
    local backup_file=$1
    
    if [ -z "$backup_file" ]; then
        error "Please specify backup file to restore"
        echo "Usage: $0 restore <backup_file.sql>"
        exit 1
    fi
    
    if [ ! -f "backups/$backup_file" ]; then
        error "Backup file not found: backups/$backup_file"
        exit 1
    fi
    
    warn "This will overwrite the current database. Are you sure? (y/N)"
    read -r response
    if [[ ! "$response" =~ ^[Yy]$ ]]; then
        log "Restore cancelled."
        exit 0
    fi
    
    log "Restoring database from backup: $backup_file"
    docker-compose exec mysql mysql -u root -p${DB_ROOT_PASSWORD} ${DB_DATABASE} < "backups/${backup_file}"
    
    if [ $? -eq 0 ]; then
        log "Database restored successfully!"
    else
        error "Restore failed!"
        exit 1
    fi
}

# Show container status
status() {
    log "Container status:"
    docker-compose ps
    
    echo ""
    log "Application URLs:"
    echo "  - Main Application: http://localhost:8080"
    echo "  - PHPMyAdmin: http://localhost:8081"
    echo "  - MySQL: localhost:3306"
    echo "  - Redis: localhost:6379"
}

# Main script
main() {
    check_docker
    check_docker_compose
    init_env
    
    case "$1" in
        up)
            up
            ;;
        down)
            down
            ;;
        build)
            build
            ;;
        logs)
            logs "$2"
            ;;
        exec-app)
            shift
            exec_app "$@"
            ;;
        exec-db)
            shift
            exec_db "$@"
            ;;
        setup)
            setup_application
            ;;
        backup)
            backup
            ;;
        restore)
            restore "$2"
            ;;
        status)
            status
            ;;
        *)
            echo "Usage: $0 {up|down|build|logs|exec-app|exec-db|setup|backup|restore|status}"
            echo ""
            echo "Commands:"
            echo "  up           - Start all containers"
            echo "  down         - Stop and remove containers"
            echo "  build        - Build Docker images"
            echo "  logs [service] - Show logs (optional service name)"
            echo "  exec-app     - Execute command in app container"
            echo "  exec-db      - Execute command in database container"
            echo "  setup        - Run application setup (migrations, seeding, etc.)"
            echo "  backup       - Create database backup"
            echo "  restore <file> - Restore database from backup"
            echo "  status       - Show container status and URLs"
            exit 1
            ;;
    esac
}

# Create backups directory if it doesn't exist
mkdir -p backups

# Run main function with all arguments
main "$@"
