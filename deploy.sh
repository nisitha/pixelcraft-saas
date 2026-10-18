#!/usr/bin/env bash
# ==============================================================================
# Vertex Books SaaS & Solar ERP - Production Deployment Script
# Target Domains: vertexhomeslk.com / vertexinnovationlk.com
# ==============================================================================

set -eo pipefail

GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

APP_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "$APP_DIR"

echo -e "${BLUE}======================================================${NC}"
echo -e "${BLUE}   Vertex Books Production Deployment Automation       ${NC}"
echo -e "${BLUE}======================================================${NC}"
echo -e "Timestamp : $(date)"
echo -e "Directory : $APP_DIR"

# 1. Check for .env file
if [ ! -f "$APP_DIR/.env" ]; then
    echo -e "${YELLOW}[!] Warning: .env file not found.${NC}"
    if [ -f "$APP_DIR/.env.production.example" ]; then
        echo -e "${YELLOW}[+] Copying .env.production.example to .env...${NC}"
        cp "$APP_DIR/.env.production.example" "$APP_DIR/.env"
        echo -e "${RED}[!] ATTENTION: Please update .env with your real database credentials and run 'php artisan key:generate'.${NC}"
    else
        echo -e "${RED}[✗] Error: Neither .env nor .env.production.example exists. Aborting.${NC}"
        exit 1
    fi
fi

# 2. Put application into maintenance mode
echo -e "\n${YELLOW}[1/7] Activating maintenance mode...${NC}"
php artisan down --render="errors::503" --secret="vertex-deploy-bypass" 2>/dev/null || true

# 3. Pull latest Git commits if inside git repository
if [ -d "$APP_DIR/.git" ] && [ "$1" != "--skip-pull" ]; then
    echo -e "\n${YELLOW}[2/7] Pulling latest changes from git...${NC}"
    git fetch origin main
    git reset --hard origin/main
else
    echo -e "\n${YELLOW}[2/7] Skipping git pull (not a git repo or --skip-pull specified)...${NC}"
fi

# 4. Install PHP dependencies
echo -e "\n${YELLOW}[3/7] Installing production composer dependencies...${NC}"
if command -v composer &> /dev/null; then
    composer install --no-dev --prefer-dist --optimize-autoloader --no-interaction
elif [ -f "$HOME/bin/composer" ]; then
    php "$HOME/bin/composer" install --no-dev --prefer-dist --optimize-autoloader --no-interaction
else
    echo -e "${YELLOW}[!] Composer binary not found in PATH. Skipping composer install.${NC}"
fi

# 5. Run Database Migrations
echo -e "\n${YELLOW}[4/7] Running database migrations safely...${NC}"
php artisan migrate --force

# 6. Ensure Storage Symlink Exists
echo -e "\n${YELLOW}[5/7] Verifying storage symlink...${NC}"
php artisan storage:link 2>/dev/null || true

# 7. Clear & Optimize Application Caches
echo -e "\n${YELLOW}[6/7] Optimizing caches for production performance...${NC}"
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# Optional Filament optimizations
php artisan icons:cache 2>/dev/null || true
php artisan filament:cache-components 2>/dev/null || true

# 8. Bring Application Back Online
echo -e "\n${YELLOW}[7/7] Deactivating maintenance mode...${NC}"
php artisan up

echo -e "\n${GREEN}======================================================${NC}"
echo -e "${GREEN}   Deployment Completed Successfully!                 ${NC}"
echo -e "${GREEN}   Application is LIVE at https://vertexhomeslk.com   ${NC}"
echo -e "${GREEN}======================================================${NC}"
