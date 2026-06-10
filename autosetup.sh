#!/bin/bash

# =======================================================
# Alborada Box — Automated Ubuntu 22.04/24.04 VPS Setup
# =======================================================
# IPTV streaming platform deployment
#
# This script automates:
# - Docker CE + Docker Compose plugin
# - Host-level Nginx (TLS termination + reverse proxy to Docker)
# - Let's Encrypt SSL certificate
# - Laravel app, MySQL, queue worker (all via Docker Compose)
# - XUI One IPTV streaming panel (port 8080)
# - UFW firewall
# - Laravel scheduler cron job
# =======================================================

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
CYAN='\033[0;36m'
MAGENTA='\033[0;35m'
BOLD='\033[1m'
NC='\033[0m'

# Logging
INSTALL_LOG="/var/log/alborada-install.log"
LOG_MAX_SIZE=10485760  # 10MB

# State management for resumable installation
STATE_FILE="/root/.alborada_install_state"
CREDENTIALS_FILE="/root/.alborada_credentials"

declare -A COMPLETED_STEPS

# -------------------------------------------------------
# Application configuration
# -------------------------------------------------------
APP_NAME="AlboradaBox"
APP_DISPLAY_NAME="Alborada Box"
APP_DIR="/var/www/alborada"
DB_HOST="db"           # Docker service name
DB_PORT="3306"
DB_NAME="alborada"
DB_USERNAME="alborada"
IPTV_PANEL_PORT=8080
DOCKER_WEB_PORT=8081   # Internal port; avoids conflict with XUI One on 8080

DOMAIN=""
SITE_URL=""
ADMIN_EMAIL=""
CANONICAL_DOMAIN=""
WWW_DOMAIN=""
DOMAIN_TYPE=""
NEEDS_WWW_REDIRECT=false

# Auto-generated credentials
MYSQL_ROOT_PASSWORD=""
DB_PASSWORD=""
ADMIN_PASSWORD=""

# =======================================================
# Logging
# =======================================================

setup_logging() {
    if [[ -f "$INSTALL_LOG" ]] && [[ $(stat -c%s "$INSTALL_LOG" 2>/dev/null || echo 0) -gt $LOG_MAX_SIZE ]]; then
        mv "$INSTALL_LOG" "${INSTALL_LOG}.old"
    fi
    {
        echo "========================================"
        echo "ALBORADA BOX INSTALLATION LOG"
        echo "Started: $(date)"
        echo "Script: $0"
        echo "========================================"
        echo ""
    } > "$INSTALL_LOG"
    echo -e "${BLUE}[INFO]${NC} Logging to $INSTALL_LOG"
}

log_info()     { echo -e "${BLUE}[INFO]${NC} $1";     echo "[$(date '+%Y-%m-%d %H:%M:%S')] [INFO] $1"     >> "$INSTALL_LOG"; }
log_success()  { echo -e "${GREEN}[SUCCESS]${NC} $1"; echo "[$(date '+%Y-%m-%d %H:%M:%S')] [SUCCESS] $1"  >> "$INSTALL_LOG"; }
log_warning()  { echo -e "${YELLOW}[WARNING]${NC} $1";echo "[$(date '+%Y-%m-%d %H:%M:%S')] [WARNING] $1" >> "$INSTALL_LOG"; }
log_error()    { echo -e "${RED}[ERROR]${NC} $1";     echo "[$(date '+%Y-%m-%d %H:%M:%S')] [ERROR] $1"    >> "$INSTALL_LOG"; }
log_wait()     { echo -e "${YELLOW}[WAIT]${NC} $1 — ${CYAN}please wait...${NC}"; echo "[$(date '+%Y-%m-%d %H:%M:%S')] [WAIT] $1" >> "$INSTALL_LOG"; }
log_progress() { echo -e "${BLUE}[PROGRESS]${NC} $1"; echo "[$(date '+%Y-%m-%d %H:%M:%S')] [PROGRESS] $1" >> "$INSTALL_LOG"; }

log_header() {
    echo -e "\n${CYAN}=================================${NC}"
    echo -e "${CYAN} $1 ${NC}"
    echo -e "${CYAN}=================================${NC}\n"
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] [HEADER] $1" >> "$INSTALL_LOG"
}

log_highlight() {
    echo -e "\n${MAGENTA}══════════════════════════════════════════════════════════${NC}"
    echo -e "${MAGENTA}  $1  ${NC}"
    echo -e "${MAGENTA}══════════════════════════════════════════════════════════${NC}\n"
}

# =======================================================
# Credential Generation
# =======================================================

generate_password() {
    local length=${1:-32}
    openssl rand -base64 48 | tr -dc 'a-zA-Z0-9' | head -c "$length"
}

generate_credentials() {
    log_header "GENERATING SECURE CREDENTIALS"
    MYSQL_ROOT_PASSWORD=$(generate_password 24)
    DB_PASSWORD=$(generate_password 24)
    ADMIN_PASSWORD=$(generate_password 16)
    log_success "All credentials generated"
    save_credentials
}

# =======================================================
# State Management
# =======================================================

save_state() {
    {
        echo "# Alborada Install State — $(date)"
        echo "DOMAIN=$DOMAIN"
        echo "CANONICAL_DOMAIN=$CANONICAL_DOMAIN"
        echo "WWW_DOMAIN=$WWW_DOMAIN"
        echo "DOMAIN_TYPE=$DOMAIN_TYPE"
        echo "NEEDS_WWW_REDIRECT=$NEEDS_WWW_REDIRECT"
        echo "ADMIN_EMAIL=$ADMIN_EMAIL"
        echo "SITE_URL=$SITE_URL"
        echo ""
        echo "# Completed steps"
        for step in "${!COMPLETED_STEPS[@]}"; do
            echo "STEP_${step}=completed"
        done
    } > "$STATE_FILE"
    chmod 600 "$STATE_FILE"
}

load_state() {
    if [[ -f "$STATE_FILE" ]]; then
        source <(grep -v '^STEP_' "$STATE_FILE" | grep -v '^#')
        while IFS='=' read -r key value; do
            if [[ "$key" == STEP_* ]]; then
                COMPLETED_STEPS["${key#STEP_}"]="completed"
            fi
        done < "$STATE_FILE"
        return 0
    fi
    return 1
}

save_credentials() {
    {
        echo "# Alborada Credentials — $(date)"
        echo "MYSQL_ROOT_PASSWORD=$MYSQL_ROOT_PASSWORD"
        echo "DB_PASSWORD=$DB_PASSWORD"
        echo "ADMIN_PASSWORD=$ADMIN_PASSWORD"
    } > "$CREDENTIALS_FILE"
    chmod 600 "$CREDENTIALS_FILE"
}

load_credentials() {
    if [[ -f "$CREDENTIALS_FILE" ]]; then
        source "$CREDENTIALS_FILE"
        return 0
    fi
    return 1
}

mark_step_complete() {
    COMPLETED_STEPS["$1"]="completed"
    save_state
}

is_step_complete() {
    [[ "${COMPLETED_STEPS[$1]}" == "completed" ]]
}

run_step() {
    local name="$1"
    local func="$2"
    if is_step_complete "$name"; then
        log_info "Skipping completed step: $name"
        return 0
    fi
    log_header "STEP: $name"
    if $func; then
        mark_step_complete "$name"
        log_success "Completed: $name"
        return 0
    else
        log_error "Failed: $name"
        return 1
    fi
}

check_resume() {
    if load_state && load_credentials; then
        echo ""
        echo -e "${MAGENTA}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
        echo -e "${YELLOW}PREVIOUS INSTALLATION DETECTED${NC}"
        echo -e "${MAGENTA}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
        echo ""
        echo -e "  Domain      : ${CYAN}$CANONICAL_DOMAIN${NC}"
        echo -e "  Admin Email : ${CYAN}$ADMIN_EMAIL${NC}"
        echo -e "  Completed steps:"
        for step in "${!COMPLETED_STEPS[@]}"; do
            echo -e "    ${GREEN}✓${NC} $step"
        done
        echo ""
        echo -e "${YELLOW}Options:${NC}"
        echo -e "  1) Resume installation (Recommended)"
        echo -e "  2) Start fresh"
        echo ""
        echo -en "${CYAN}>>> Choose [1]: ${NC}"
        read -r RESUME_OPTION
        if [[ "$RESUME_OPTION" == "2" ]]; then
            rm -f "$STATE_FILE" "$CREDENTIALS_FILE"
            unset COMPLETED_STEPS
            declare -gA COMPLETED_STEPS
            log_info "Starting fresh"
            return 1
        fi
        log_info "Resuming from previous state"
        return 0
    fi
    return 1
}

cleanup_state_files() {
    rm -f "$STATE_FILE" "$CREDENTIALS_FILE"
}

# =======================================================
# Domain Handling
# =======================================================

validate_and_process_domain() {
    local input_domain
    input_domain=$(echo "$1" | sed 's|^https\?://||' | sed 's|/.*||' | tr '[:upper:]' '[:lower:]')

    if [[ -z "$input_domain" ]]; then
        log_error "Domain cannot be empty"; return 1
    fi

    if [[ "$input_domain" =~ ^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$ ]]; then
        log_error "IP addresses are not valid domain names"; return 1
    fi

    if [[ ! "$input_domain" =~ ^[a-z0-9]([a-z0-9-]*[a-z0-9])?(\.[a-z0-9]([a-z0-9-]*[a-z0-9])?)*\.[a-z]{2,}$ ]]; then
        log_error "Invalid domain format: $input_domain"; return 1
    fi

    local dot_count
    dot_count=$(echo "$input_domain" | tr -cd '.' | wc -c)
    local is_ccsld=false
    [[ "$input_domain" =~ \.(com|net|org|co|gov|edu|ac|mil)\.[a-z]{2}$ ]] && is_ccsld=true

    if [[ "$input_domain" =~ ^www\. ]]; then
        DOMAIN_TYPE="www_input"
        CANONICAL_DOMAIN="${input_domain#www.}"
        WWW_DOMAIN="$input_domain"
        NEEDS_WWW_REDIRECT=true
    elif [[ $dot_count -eq 1 ]] || ( [[ $dot_count -eq 2 ]] && [[ "$is_ccsld" == "true" ]] ); then
        DOMAIN_TYPE="root"
        CANONICAL_DOMAIN="$input_domain"
        WWW_DOMAIN="www.$input_domain"
        NEEDS_WWW_REDIRECT=true
    else
        DOMAIN_TYPE="subdomain"
        CANONICAL_DOMAIN="$input_domain"
        WWW_DOMAIN=""
        NEEDS_WWW_REDIRECT=false
    fi

    DOMAIN="$CANONICAL_DOMAIN"
    return 0
}

get_domain() {
    echo ""
    echo -e "${MAGENTA}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
    echo -e "${MAGENTA}                     DOMAIN CONFIGURATION${NC}"
    echo -e "${MAGENTA}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
    echo ""
    echo -e "${YELLOW}This domain will serve the Alborada Box web panel (HTTPS).${NC}"
    echo -e "${YELLOW}IPTV customers will connect via: http://stream.YOURDOMAIN.com:${IPTV_PANEL_PORT}${NC}"
    echo -e "${GREEN}Examples: ${CYAN}alboradabox.com${NC} or ${CYAN}app.example.com${NC}"
    echo ""

    while true; do
        echo -en "${CYAN}${BOLD}>>> Enter your domain name: ${NC}"
        read -r input_domain
        if validate_and_process_domain "$input_domain"; then
            break
        fi
        log_warning "Please try again with a valid domain name"
    done

    SITE_URL="https://${CANONICAL_DOMAIN}"
    ADMIN_EMAIL="admin@${CANONICAL_DOMAIN}"

    echo ""
    log_success "Domain configured:"
    log_info "  Web panel    : $SITE_URL"
    log_info "  Admin panel  : $SITE_URL/admin"
    log_info "  Admin email  : $ADMIN_EMAIL"
    log_info "  IPTV streams : http://stream.${CANONICAL_DOMAIN}:${IPTV_PANEL_PORT}"
    [[ "$NEEDS_WWW_REDIRECT" == "true" ]] && \
        log_info "  WWW redirect : https://$WWW_DOMAIN → https://$CANONICAL_DOMAIN"
    echo ""
}

# =======================================================
# Preflight Checks
# =======================================================

check_root() {
    [[ $EUID -ne 0 ]] && { log_error "Must run as root: sudo ./autosetup.sh"; exit 1; }
    log_info "Running as root"
}

check_disk_space() {
    local required_gb=${1:-20}
    local available_gb
    available_gb=$(( $(df / | awk 'NR==2 {print $4}') / 1024 / 1024 ))
    if [[ $available_gb -lt $required_gb ]]; then
        log_error "Insufficient disk: need ${required_gb}GB, have ${available_gb}GB"; exit 1
    fi
    log_success "Disk space OK: ${available_gb}GB available"
}

check_ubuntu_version() {
    local os_id os_version
    os_id=$(lsb_release -si 2>/dev/null || grep '^ID=' /etc/os-release | cut -d= -f2 | tr -d '"')
    os_version=$(lsb_release -sr 2>/dev/null || grep '^VERSION_ID=' /etc/os-release | cut -d= -f2 | tr -d '"')

    if [[ "$os_id" != "Ubuntu" ]]; then
        log_warning "This script is tested on Ubuntu. Running on: $os_id $os_version"
    else
        log_info "OS: $os_id $os_version"
        if [[ "$os_version" != "22.04" && "$os_version" != "24.04" ]]; then
            log_warning "Recommended: Ubuntu 22.04 or 24.04 LTS (detected: $os_version)"
        fi
    fi
}

verify_project_files() {
    log_header "VERIFYING PROJECT FILES"

    if [[ ! -d "$APP_DIR" ]] || [[ ! -f "$APP_DIR/artisan" ]]; then
        log_error "Laravel application not found at $APP_DIR"
        echo ""
        echo -e "${YELLOW}Clone or upload the project first:${NC}"
        echo -e "  ${CYAN}git clone <repo-url> $APP_DIR${NC}"
        echo -e "  Or: ${CYAN}scp -r /local/alborada root@your-server:$APP_DIR${NC}"
        echo ""
        echo -e "${YELLOW}Expected structure:${NC}"
        echo -e "  $APP_DIR/"
        echo -e "    ├── app/"
        echo -e "    ├── docker/"
        echo -e "    ├── docker-compose.yml"
        echo -e "    ├── artisan"
        echo -e "    ├── composer.json"
        echo -e "    └── autosetup.sh"
        exit 1
    fi

    if [[ ! -f "$APP_DIR/docker-compose.yml" ]]; then
        log_error "docker-compose.yml not found at $APP_DIR"; exit 1
    fi

    log_success "Project files verified at $APP_DIR"
}

# =======================================================
# System Packages
# =======================================================

update_system() {
    log_header "UPDATING SYSTEM PACKAGES"
    export DEBIAN_FRONTEND=noninteractive
    log_wait "Updating package repositories"
    apt update -y >/dev/null 2>&1
    log_wait "Upgrading system packages"
    apt upgrade -y >/dev/null 2>&1
    log_wait "Installing essential tools"
    apt install -y curl wget gnupg2 software-properties-common apt-transport-https \
        ca-certificates git openssl unzip zip lsb-release ufw fail2ban >/dev/null 2>&1
    log_success "System packages updated"
}

# =======================================================
# Docker CE + Docker Compose Plugin
# =======================================================

install_docker() {
    log_header "INSTALLING DOCKER CE"

    if command -v docker &>/dev/null; then
        log_info "Docker already installed: $(docker --version)"
        return 0
    fi

    log_wait "Adding Docker GPG key and repository"
    install -m 0755 -d /etc/apt/keyrings
    curl -fsSL https://download.docker.com/linux/ubuntu/gpg | \
        gpg --dearmor -o /etc/apt/keyrings/docker.gpg >/dev/null 2>&1
    chmod a+r /etc/apt/keyrings/docker.gpg

    echo "deb [arch=$(dpkg --print-architecture) signed-by=/etc/apt/keyrings/docker.gpg] \
https://download.docker.com/linux/ubuntu $(lsb_release -cs) stable" | \
        tee /etc/apt/sources.list.d/docker.list >/dev/null

    apt update >/dev/null 2>&1
    log_wait "Installing Docker CE and Docker Compose plugin"
    apt install -y docker-ce docker-ce-cli containerd.io docker-buildx-plugin \
        docker-compose-plugin >/dev/null 2>&1

    systemctl enable docker
    systemctl start docker

    log_success "Docker installed: $(docker --version)"
    log_success "Docker Compose: $(docker compose version)"
}

# =======================================================
# Host-level Nginx (reverse proxy + TLS termination)
# =======================================================

install_host_nginx() {
    log_header "INSTALLING HOST NGINX"

    if dpkg -l nginx &>/dev/null 2>&1; then
        log_info "Nginx already installed"
        return 0
    fi

    apt install -y nginx >/dev/null 2>&1
    systemctl enable nginx
    systemctl start nginx
    log_success "Nginx installed"
}

configure_host_nginx() {
    log_header "CONFIGURING HOST NGINX"

    local NGINX_CONF="/etc/nginx/sites-available/$CANONICAL_DOMAIN"

    if [[ "$NEEDS_WWW_REDIRECT" == "true" ]]; then
        cat > "$NGINX_CONF" <<NGINX_EOF
# www → non-www redirect
server {
    listen 80;
    server_name $WWW_DOMAIN;
    return 301 http://$CANONICAL_DOMAIN\$request_uri;
}

# Alborada Box — main application
server {
    listen 80;
    server_name $CANONICAL_DOMAIN;

    client_max_body_size 100M;

    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header Referrer-Policy "strict-origin-when-cross-origin" always;

    location / {
        proxy_pass         http://127.0.0.1:${DOCKER_WEB_PORT};
        proxy_set_header   Host              \$host;
        proxy_set_header   X-Real-IP         \$remote_addr;
        proxy_set_header   X-Forwarded-For   \$proxy_add_x_forwarded_for;
        proxy_set_header   X-Forwarded-Proto \$scheme;
        proxy_read_timeout 120s;
        proxy_buffering    off;
    }

    location ~ /\.(?!well-known).* { deny all; }
}
NGINX_EOF
    else
        cat > "$NGINX_CONF" <<NGINX_EOF
# Alborada Box — main application
server {
    listen 80;
    server_name $CANONICAL_DOMAIN;

    client_max_body_size 100M;

    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header Referrer-Policy "strict-origin-when-cross-origin" always;

    location / {
        proxy_pass         http://127.0.0.1:${DOCKER_WEB_PORT};
        proxy_set_header   Host              \$host;
        proxy_set_header   X-Real-IP         \$remote_addr;
        proxy_set_header   X-Forwarded-For   \$proxy_add_x_forwarded_for;
        proxy_set_header   X-Forwarded-Proto \$scheme;
        proxy_read_timeout 120s;
        proxy_buffering    off;
    }

    location ~ /\.(?!well-known).* { deny all; }
}
NGINX_EOF
    fi

    ln -sf "$NGINX_CONF" /etc/nginx/sites-enabled/
    rm -f /etc/nginx/sites-enabled/default

    if ! nginx -t >/dev/null 2>&1; then
        log_error "Nginx config test failed"; nginx -t; return 1
    fi
    systemctl reload nginx
    log_success "Nginx configured: $CANONICAL_DOMAIN → 127.0.0.1:${DOCKER_WEB_PORT}"
}

# =======================================================
# SSL Certificate (Let's Encrypt)
# =======================================================

install_ssl() {
    log_header "INSTALLING SSL CERTIFICATE"

    apt install -y certbot python3-certbot-nginx >/dev/null 2>&1

    local SERVER_IP
    SERVER_IP=$(curl -s -4 --max-time 5 ifconfig.me 2>/dev/null \
        || curl -s -4 --max-time 5 icanhazip.com 2>/dev/null \
        || echo "Unable to detect IP")

    echo ""
    echo -e "${MAGENTA}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
    echo -e "${YELLOW}DNS CONFIGURATION REQUIRED${NC}"
    echo -e "${MAGENTA}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
    echo ""
    echo -e "${YELLOW}Add these DNS A records pointing to: ${CYAN}$SERVER_IP${NC}"
    echo ""
    echo -e "  ${CYAN}Type   Host     Value${NC}"
    echo -e "  ──────────────────────────────────────────"
    echo -e "  A      @        $SERVER_IP   (main domain)"
    [[ "$NEEDS_WWW_REDIRECT" == "true" ]] && \
    echo -e "  A      www      $SERVER_IP   (www redirect)"
    echo -e "  A      stream   $SERVER_IP   (IPTV — keep DNS-only/grey-cloud in Cloudflare)"
    echo ""
    echo -e "${YELLOW}TIP: Enable Cloudflare proxy (orange cloud) for @ and www only.${NC}"
    echo -e "${YELLOW}     Keep ${CYAN}stream${YELLOW} as DNS-only (grey cloud) — Cloudflare cannot proxy IPTV video.${NC}"
    echo ""
    echo -e "${YELLOW}Verify propagation: ${CYAN}https://dnschecker.org${NC}"
    echo ""
    echo -e "${MAGENTA}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
    echo -en "${CYAN}${BOLD}>>> Press Enter when DNS is propagated: ${NC}"
    read -r

    if [[ "$NEEDS_WWW_REDIRECT" == "true" ]]; then
        if certbot --nginx -d "$CANONICAL_DOMAIN" -d "$WWW_DOMAIN" \
                --non-interactive --agree-tos --email "$ADMIN_EMAIL" --redirect; then
            log_success "SSL certificate installed for $CANONICAL_DOMAIN + $WWW_DOMAIN"
        else
            log_warning "SSL failed — continuing without HTTPS. Run certbot manually:"
            log_info "  certbot --nginx -d $CANONICAL_DOMAIN -d $WWW_DOMAIN --email $ADMIN_EMAIL"
        fi
    else
        if certbot --nginx -d "$CANONICAL_DOMAIN" \
                --non-interactive --agree-tos --email "$ADMIN_EMAIL" --redirect; then
            log_success "SSL certificate installed for $CANONICAL_DOMAIN"
        else
            log_warning "SSL failed — continuing without HTTPS. Run certbot manually:"
            log_info "  certbot --nginx -d $CANONICAL_DOMAIN --email $ADMIN_EMAIL"
        fi
    fi

    systemctl enable certbot.timer 2>/dev/null
    systemctl start certbot.timer 2>/dev/null || true
    log_success "SSL setup completed (auto-renewal enabled)"
}

# =======================================================
# Docker Compose Override
# Injects generated credentials + re-binds web to port 8081
# (keeps port 8080 free for XUI One IPTV panel)
# =======================================================

configure_docker_compose() {
    log_header "CONFIGURING DOCKER COMPOSE"

    cat > "$APP_DIR/docker-compose.override.yml" <<OVERRIDE_EOF
# Generated by autosetup.sh — $(date)
# Do NOT edit manually.

services:
  db:
    environment:
      MYSQL_ROOT_PASSWORD: "${MYSQL_ROOT_PASSWORD}"
      MYSQL_DATABASE: "${DB_NAME}"
      MYSQL_USER: "${DB_USERNAME}"
      MYSQL_PASSWORD: "${DB_PASSWORD}"
    ports:
      - "127.0.0.1:3306:3306"

  web:
    ports:
      - "127.0.0.1:${DOCKER_WEB_PORT}:80"
OVERRIDE_EOF

    chmod 600 "$APP_DIR/docker-compose.override.yml"
    log_success "docker-compose.override.yml written"
    log_info "  DB password: (generated)"
    log_info "  Web port   : 127.0.0.1:${DOCKER_WEB_PORT} (XUI One keeps port 8080)"
}

# =======================================================
# Laravel .env
# =======================================================

write_env_file() {
    log_header "WRITING LARAVEL .ENV"

    cat > "$APP_DIR/.env" <<ENV_EOF
APP_NAME="${APP_DISPLAY_NAME}"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=${SITE_URL}

APP_LOCALE=en
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=en_US

APP_MAINTENANCE_DRIVER=file

BCRYPT_ROUNDS=12

LOG_CHANNEL=stack
LOG_STACK=single
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=${DB_HOST}
DB_PORT=${DB_PORT}
DB_DATABASE=${DB_NAME}
DB_USERNAME=${DB_USERNAME}
DB_PASSWORD=${DB_PASSWORD}

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local
QUEUE_CONNECTION=database

CACHE_STORE=database

REDIS_CLIENT=phpredis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_SCHEME=tls
MAIL_HOST=
MAIL_PORT=587
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_FROM_ADDRESS=noreply@${CANONICAL_DOMAIN}
MAIL_FROM_NAME="${APP_DISPLAY_NAME}"

# Stripe — fill in after obtaining keys from dashboard.stripe.com
STRIPE_PUBLIC_KEY=
STRIPE_SECRET_KEY=
STRIPE_WEBHOOK_SECRET=

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

VITE_APP_NAME="${APP_DISPLAY_NAME}"
ENV_EOF

    chmod 640 "$APP_DIR/.env"
    log_success ".env written at $APP_DIR/.env"
}

# =======================================================
# Build and Start Docker Containers
# =======================================================

build_and_start_containers() {
    log_header "BUILDING AND STARTING DOCKER CONTAINERS"

    cd "$APP_DIR" || { log_error "Cannot enter $APP_DIR"; return 1; }

    log_wait "Building application Docker image (may take several minutes)"
    if ! docker compose build app >> "$INSTALL_LOG" 2>&1; then
        log_error "Docker image build failed — check $INSTALL_LOG for details"
        return 1
    fi
    log_success "Application image built"

    log_wait "Starting all containers"
    if ! docker compose up -d >> "$INSTALL_LOG" 2>&1; then
        log_error "docker compose up failed — check $INSTALL_LOG for details"
        return 1
    fi

    log_wait "Waiting for MySQL to be ready (up to 90 seconds)"
    local retries=30
    while [[ $retries -gt 0 ]]; do
        if docker compose exec -T db mysqladmin ping -h localhost --silent 2>/dev/null; then
            break
        fi
        sleep 3
        (( retries-- ))
    done
    if [[ $retries -eq 0 ]]; then
        log_error "MySQL did not start in time"
        return 1
    fi

    log_success "All containers started"
    docker compose ps >> "$INSTALL_LOG" 2>&1
}

# =======================================================
# Laravel Application Initialization
# =======================================================

setup_laravel_app() {
    log_header "INITIALIZING LARAVEL APPLICATION"

    cd "$APP_DIR" || { log_error "Cannot enter $APP_DIR"; return 1; }

    log_progress "Generating application key"
    if ! docker compose exec -T app php artisan key:generate --force >> "$INSTALL_LOG" 2>&1; then
        log_error "key:generate failed"; return 1
    fi

    log_progress "Clearing config cache"
    docker compose exec -T app php artisan config:clear >> "$INSTALL_LOG" 2>&1

    log_wait "Running database migrations"
    if ! docker compose exec -T app php artisan migrate --force >> "$INSTALL_LOG" 2>&1; then
        log_error "Migrations failed — check $INSTALL_LOG for details"; return 1
    fi
    log_success "Migrations complete"

    log_wait "Seeding database"
    docker compose exec -T app php artisan db:seed --force >> "$INSTALL_LOG" 2>&1 || \
        log_warning "Seeding skipped — run manually: docker compose exec app php artisan db:seed"

    log_progress "Creating admin user ($ADMIN_EMAIL)"
    docker compose exec -T app php artisan tinker --execute="
        \$user = \App\Models\User::firstOrCreate(
            ['email' => '$ADMIN_EMAIL'],
            [
                'name'              => 'Administrator',
                'password'          => bcrypt('$ADMIN_PASSWORD'),
                'email_verified_at' => now(),
            ]
        );
        echo \$user->wasRecentlyCreated ? 'Admin user created.' : 'Admin user already exists.';
    " >> "$INSTALL_LOG" 2>&1 || \
        log_warning "Auto admin creation failed — register at $SITE_URL/register after setup"

    log_progress "Creating storage symlink"
    docker compose exec -T app php artisan storage:link >> "$INSTALL_LOG" 2>&1 || true

    log_progress "Setting storage permissions"
    chmod -R 775 "$APP_DIR/storage" "$APP_DIR/bootstrap/cache" 2>/dev/null || true

    log_success "Laravel application initialized"
}

# =======================================================
# XUI One IPTV Streaming Panel
# =======================================================

install_iptv_panel() {
    log_header "INSTALLING XUI ONE IPTV STREAMING PANEL"

    echo ""
    echo -e "${MAGENTA}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
    echo -e "${YELLOW}XUI One IPTV Panel${NC}"
    echo -e "${MAGENTA}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
    echo ""
    echo -e "XUI One will be installed on port ${CYAN}${IPTV_PANEL_PORT}${NC}."
    echo -e "IPTV customers connect to your service via:"
    echo -e "  ${CYAN}http://stream.${CANONICAL_DOMAIN}:${IPTV_PANEL_PORT}${NC}"
    echo ""
    echo -e "${YELLOW}During the XUI One installer, when prompted:${NC}"
    echo -e "  • Panel port     → enter ${CYAN}${IPTV_PANEL_PORT}${NC}"
    echo -e "  • Admin username → choose a username (save it)"
    echo -e "  • Admin password → choose a strong password (save it)"
    echo ""
    echo -e "${RED}${BOLD}IMPORTANT: Save your XUI One admin credentials — you will need them${NC}"
    echo -e "${RED}${BOLD}to connect the web panel to the IPTV server after installation.${NC}"
    echo ""
    echo -en "${CYAN}>>> Press Enter to launch the XUI One installer: ${NC}"
    read -r

    if bash <(curl -s https://raw.githubusercontent.com/AXUIone/XUI-One/master/install.sh); then
        log_success "XUI One installed successfully"
        echo ""
        echo -e "${MAGENTA}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
        echo -e "${YELLOW}XUI One Installation Complete${NC}"
        echo -e "${MAGENTA}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
        echo ""
        echo -e "  Panel URL     : ${CYAN}http://$(curl -s ifconfig.me 2>/dev/null):${IPTV_PANEL_PORT}${NC}"
        echo -e "  Stream URL    : ${CYAN}http://stream.${CANONICAL_DOMAIN}:${IPTV_PANEL_PORT}${NC}"
        echo ""
        echo -e "${YELLOW}After the full setup completes, connect the website to the IPTV panel:${NC}"
        echo -e "  1. Go to ${CYAN}${SITE_URL}/admin${NC} → Settings → IPTV"
        echo -e "  2. Set ${CYAN}xtream_base_url = http://localhost:${IPTV_PANEL_PORT}${NC}"
        echo -e "  3. Enter your XUI One admin username and password"
        echo -e "  4. Enable ${CYAN}iptv_provisioning_enabled = 1${NC}"
        echo -e "${MAGENTA}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
        echo ""
        echo -en "${CYAN}>>> Press Enter to continue with remaining setup steps: ${NC}"
        read -r
    else
        log_warning "XUI One installer returned a non-zero exit code or was skipped."
        log_info "Install manually later with:"
        log_info "  bash <(curl -s https://raw.githubusercontent.com/AXUIone/XUI-One/master/install.sh)"
        log_info "Continuing with remaining setup steps..."
    fi
}

# =======================================================
# Firewall (UFW)
# =======================================================

configure_firewall() {
    log_header "CONFIGURING FIREWALL (UFW)"

    ufw default deny incoming
    ufw default allow outgoing
    ufw allow ssh
    ufw allow 'Nginx Full'
    ufw allow "${IPTV_PANEL_PORT}/tcp" comment 'XUI One IPTV panel'
    ufw --force enable

    log_success "Firewall configured"
    log_info "  Open ports: 22 (SSH)  80 (HTTP)  443 (HTTPS)  ${IPTV_PANEL_PORT} (XUI One)"
}

# =======================================================
# Laravel Scheduler Cron Job
# =======================================================

configure_cron() {
    log_header "CONFIGURING LARAVEL SCHEDULER"

    local CRON_JOB="* * * * * docker compose -f $APP_DIR/docker-compose.yml exec -T app php artisan schedule:run >> /var/log/laravel-scheduler.log 2>&1"

    if crontab -l 2>/dev/null | grep -q "artisan schedule:run"; then
        log_info "Scheduler cron already exists"
    else
        (crontab -l 2>/dev/null || true; echo "$CRON_JOB") | crontab -
        log_success "Scheduler cron added for root"
    fi

    log_info "Scheduled jobs:"
    log_info "  • ProcessSubscriptionRenewalsJob  — daily"
    log_info "  • SendRenewalRemindersJob          — daily at 08:00"
    log_info "  • SendExpiryAlertsJob              — daily at 09:00"
    log_info "  • SyncXtreamStatusJob              — every 4 hours"
}

# =======================================================
# Finalize Laravel Production Cache
# =======================================================

finalize_cache() {
    log_header "FINALIZING PRODUCTION CACHE"

    cd "$APP_DIR" || return 1

    docker compose exec -T app php artisan config:cache >> "$INSTALL_LOG" 2>&1
    docker compose exec -T app php artisan route:cache  >> "$INSTALL_LOG" 2>&1
    docker compose exec -T app php artisan view:cache   >> "$INSTALL_LOG" 2>&1

    log_success "Laravel production cache built"
}

# =======================================================
# Installation Verification
# =======================================================

verify_installation() {
    log_header "VERIFYING INSTALLATION"

    cd "$APP_DIR" || return 0

    systemctl is-active --quiet nginx && \
        log_success "Host Nginx: running" || log_warning "Host Nginx: NOT running"

    docker compose ps --status running 2>/dev/null | grep -q "alborada_app" && \
        log_success "App container: running"    || log_warning "App container: NOT running"
    docker compose ps --status running 2>/dev/null | grep -q "alborada_nginx" && \
        log_success "Web container: running"    || log_warning "Web container: NOT running"
    docker compose ps --status running 2>/dev/null | grep -q "alborada_mysql" && \
        log_success "MySQL container: running"  || log_warning "MySQL container: NOT running"
    docker compose ps --status running 2>/dev/null | grep -q "alborada_worker" && \
        log_success "Worker container: running" || log_warning "Worker container: NOT running"

    [[ -f "/etc/letsencrypt/live/$CANONICAL_DOMAIN/fullchain.pem" ]] && \
        log_success "SSL certificate: installed" || log_warning "SSL certificate: not found"

    local http_code
    http_code=$(curl -s -o /dev/null -w "%{http_code}" --max-time 5 "http://127.0.0.1:${DOCKER_WEB_PORT}" 2>/dev/null)
    if [[ "$http_code" =~ ^(200|301|302)$ ]]; then
        log_success "Web app responding on port ${DOCKER_WEB_PORT} (HTTP $http_code)"
    else
        log_warning "Web app not responding on port ${DOCKER_WEB_PORT}"
    fi

    http_code=$(curl -s -o /dev/null -w "%{http_code}" --max-time 5 "http://localhost:${IPTV_PANEL_PORT}" 2>/dev/null)
    if [[ "$http_code" =~ ^(200|301|302|403)$ ]]; then
        log_success "XUI One panel: responding on port ${IPTV_PANEL_PORT}"
    else
        log_warning "XUI One panel: not responding on port ${IPTV_PANEL_PORT} (may need manual install)"
    fi

    crontab -l 2>/dev/null | grep -q "schedule:run" && \
        log_success "Scheduler cron: active" || log_warning "Scheduler cron: not found"
}

# =======================================================
# Final Summary
# =======================================================

show_final_info() {
    local SERVER_IP
    SERVER_IP=$(curl -s -4 --max-time 5 ifconfig.me 2>/dev/null || echo "YOUR_SERVER_IP")

    log_highlight "$APP_DISPLAY_NAME INSTALLATION COMPLETED!"

    echo -e "${GREEN}Your Alborada Box IPTV platform is live!${NC}"
    echo ""

    echo -e "${YELLOW}WEB PANEL ACCESS:${NC}"
    echo -e "  URL         : ${CYAN}$SITE_URL${NC}"
    echo -e "  Admin panel : ${CYAN}$SITE_URL/admin${NC}"
    echo -e "  Admin email : ${CYAN}$ADMIN_EMAIL${NC}"
    echo -e "  Admin pass  : ${CYAN}$ADMIN_PASSWORD${NC}"
    echo ""

    echo -e "${YELLOW}IPTV STREAMING PANEL (XUI One):${NC}"
    echo -e "  Direct URL  : ${CYAN}http://${SERVER_IP}:${IPTV_PANEL_PORT}${NC}"
    echo -e "  Stream URL  : ${CYAN}http://stream.${CANONICAL_DOMAIN}:${IPTV_PANEL_PORT}${NC}"
    echo ""

    echo -e "${YELLOW}DATABASE CREDENTIALS:${NC}"
    echo -e "  DB Name     : $DB_NAME"
    echo -e "  DB User     : $DB_USERNAME"
    echo -e "  DB Password : $DB_PASSWORD"
    echo -e "  Root PW     : $MYSQL_ROOT_PASSWORD"
    echo ""

    echo -e "${YELLOW}CONNECTING THE WEBSITE TO THE IPTV PANEL:${NC}"
    echo -e "  1. Open  : ${CYAN}$SITE_URL/admin${NC} → Settings → IPTV"
    echo -e "  2. Set   : ${CYAN}xtream_base_url = http://localhost:${IPTV_PANEL_PORT}${NC}"
    echo -e "  3. Enter : your XUI One admin username and password"
    echo -e "  4. Enable: ${CYAN}iptv_provisioning_enabled = 1${NC}"
    echo -e "  5. Save and test: create a test subscription → XUI One line should auto-create"
    echo ""

    echo -e "${YELLOW}NEXT STEPS:${NC}"
    echo -e "  1. Configure SMTP: Admin panel → Settings → SMTP Settings"
    echo -e "     (Recommended: Mailgun free tier — up to 1,000 emails/month)"
    echo -e "  2. Add Stripe keys: edit ${CYAN}$APP_DIR/.env${NC}"
    echo -e "     STRIPE_PUBLIC_KEY, STRIPE_SECRET_KEY, STRIPE_WEBHOOK_SECRET"
    echo -e "     Then: ${CYAN}docker compose -f $APP_DIR/docker-compose.yml exec app php artisan config:cache${NC}"
    echo -e "  3. Register Stripe webhook in Stripe Dashboard → Developers → Webhooks:"
    echo -e "     URL: ${CYAN}$SITE_URL/stripe/webhook${NC}"
    echo -e "     Events: payment_intent.succeeded, payment_intent.payment_failed"
    echo -e "  4. Load IPTV content: XUI One → Bouquets → add an M3U source"
    echo -e "     Test content: ${CYAN}https://iptv-org.github.io/iptv/index.m3u${NC}"
    echo -e "  5. Test end-to-end: Stripe test mode → register customer → pay → check XCIPTV"
    echo ""

    echo -e "${YELLOW}CUSTOMER IPTV APP INSTRUCTIONS:${NC}"
    echo -e "  App    : XCIPTV, IPTV Smarters, or IBO Player"
    echo -e "  Method : Xtream Codes API login"
    echo -e "  Server : ${CYAN}http://stream.${CANONICAL_DOMAIN}:${IPTV_PANEL_PORT}${NC}"
    echo -e "  Login  : username + password from welcome email (auto-sent on payment)"
    echo ""

    echo -e "${YELLOW}DOCKER MANAGEMENT:${NC}"
    echo -e "  Status      : ${CYAN}cd $APP_DIR && docker compose ps${NC}"
    echo -e "  App logs    : ${CYAN}docker compose logs -f app${NC}"
    echo -e "  Worker logs : ${CYAN}docker compose logs -f worker${NC}"
    echo -e "  Restart all : ${CYAN}docker compose restart app web worker${NC}"
    echo -e "  Rebuild     : ${CYAN}docker compose build app && docker compose up -d${NC}"
    echo ""

    echo -e "${YELLOW}FILES:${NC}"
    echo -e "  Application : ${CYAN}$APP_DIR${NC}"
    echo -e "  Install log : ${CYAN}$INSTALL_LOG${NC}"
    echo -e "  Scheduler   : ${CYAN}/var/log/laravel-scheduler.log${NC}"
    echo ""

    echo -e "${RED}${BOLD}SAVE THESE CREDENTIALS NOW — this is the only time they are shown!${NC}"
}

# =======================================================
# Help
# =======================================================

show_help() {
    echo ""
    echo -e "${CYAN}$APP_DISPLAY_NAME — Automated VPS Deployment${NC}"
    echo ""
    echo -e "${YELLOW}Usage:${NC}"
    echo "  sudo ./autosetup.sh [--domain yourdomain.com]"
    echo ""
    echo -e "${YELLOW}Prerequisites:${NC}"
    echo "  • Ubuntu 22.04 or 24.04 LTS VPS (min 4 vCPU / 8 GB RAM / 100 GB SSD)"
    echo "  • Project files in $APP_DIR (git clone or scp)"
    echo "  • Domain name with DNS control"
    echo ""
    echo -e "${YELLOW}What this script installs:${NC}"
    echo "  • Docker CE + Docker Compose plugin"
    echo "  • Host-level Nginx (TLS termination + reverse proxy)"
    echo "  • Let's Encrypt SSL certificate (auto-renewing)"
    echo "  • Laravel app + MySQL + queue worker (via Docker Compose)"
    echo "  • XUI One IPTV streaming panel (port ${IPTV_PANEL_PORT})"
    echo "  • UFW firewall"
    echo "  • Laravel scheduler cron job"
    echo ""
    echo -e "${YELLOW}Ports:${NC}"
    echo "  22    SSH"
    echo "  80    HTTP → HTTPS redirect"
    echo "  443   HTTPS (Laravel web app)"
    echo "  ${IPTV_PANEL_PORT}   XUI One IPTV panel + Xtream Codes API + stream delivery"
    echo ""
}

# =======================================================
# Argument Parsing
# =======================================================

parse_arguments() {
    while [[ $# -gt 0 ]]; do
        case "$1" in
            --domain)
                if validate_and_process_domain "$2"; then
                    SITE_URL="https://${CANONICAL_DOMAIN}"
                    ADMIN_EMAIL="admin@${CANONICAL_DOMAIN}"
                    shift 2
                else
                    log_error "Invalid domain: $2"; exit 1
                fi ;;
            --help|-h)
                show_help; exit 0 ;;
            *)
                log_error "Unknown option: $1"; show_help; exit 1 ;;
        esac
    done
}

# =======================================================
# Main Entry Point
# =======================================================

main() {
    setup_logging "$@"

    log_highlight "$APP_DISPLAY_NAME — AUTOMATED VPS SETUP"

    parse_arguments "$@"
    check_root
    check_ubuntu_version
    check_disk_space 20

    if check_resume; then
        log_info "Resuming from previous state"
        verify_project_files
    else
        verify_project_files
        generate_credentials
        [[ -z "$DOMAIN" ]] && get_domain
    fi

    echo ""
    echo -e "${MAGENTA}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
    echo -e "${MAGENTA}                    READY TO BEGIN INSTALLATION${NC}"
    echo -e "${MAGENTA}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
    echo ""
    echo -e "  Domain       : ${CYAN}$CANONICAL_DOMAIN${NC}"
    echo -e "  Web panel    : ${CYAN}$SITE_URL${NC}"
    echo -e "  Admin email  : ${CYAN}$ADMIN_EMAIL${NC}"
    echo -e "  IPTV panel   : ${CYAN}http://stream.${CANONICAL_DOMAIN}:${IPTV_PANEL_PORT}${NC}"
    echo ""
    echo -e "  Will install : Docker · Nginx · SSL · Laravel (Docker) · XUI One · UFW"
    echo ""
    echo -e "${MAGENTA}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
    echo -en "${CYAN}${BOLD}>>> Continue? (y/N): ${NC}"
    read -n 1 -r REPLY
    echo ""
    if [[ ! $REPLY =~ ^[Yy]$ ]]; then
        log_warning "Setup cancelled"; exit 0
    fi

    echo ""
    log_success "Starting automated installation..."
    echo ""

    run_step "system_update"          update_system            || exit 1
    run_step "docker_install"         install_docker           || exit 1
    run_step "nginx_install"          install_host_nginx       || exit 1
    run_step "nginx_configure"        configure_host_nginx     || exit 1
    run_step "ssl_install"            install_ssl              || exit 1
    run_step "docker_compose_config"  configure_docker_compose || exit 1
    run_step "env_write"              write_env_file           || exit 1
    run_step "containers_start"       build_and_start_containers || exit 1
    run_step "laravel_setup"          setup_laravel_app        || exit 1
    run_step "iptv_panel_install"     install_iptv_panel       || exit 1
    run_step "firewall_configure"     configure_firewall       || exit 1
    run_step "cron_configure"         configure_cron           || exit 1
    run_step "finalize_cache"         finalize_cache           || exit 1
    run_step "verify_install"         verify_installation      || exit 1

    cleanup_state_files
    show_final_info

    echo "[$(date '+%Y-%m-%d %H:%M:%S')] [SUCCESS] Installation completed" >> "$INSTALL_LOG"
    exit 0
}

main "$@"
