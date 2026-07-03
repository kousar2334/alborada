#!/bin/bash
# ==========================================================================
# Moissanite Radiance — WEB SERVER Setup
# Laravel 12 IPTV Platform web panel + phpMyAdmin + Postfix mail server
# ==========================================================================
# Compatible : Ubuntu 22.04 LTS / 24.04 LTS (fresh install)
# Run as     : sudo ./websetup.sh [--domain yourdomain.com]
#
# This script sets up the WEB server only. The IPTV streaming server
# (XUI.ONE) is installed on a SEPARATE VPS with streamsetup.sh.
#
# Port layout after installation:
#   :443  → Host Nginx (HTTPS/TLS) → 127.0.0.1:8081 (Docker web container)
#   :80   → HTTP → HTTPS redirect
#   :8081 → Docker web container    (127.0.0.1 only, proxied by Nginx)
#   :8082 → phpMyAdmin container    (127.0.0.1 only, proxied at /phpmyadmin)
#   :25   → Postfix mail server     (localhost + Docker network only)
#   :3306 → MySQL                   (Docker internal only, never public)
#
# Docker → services on the host:
#   The app container reaches host Postfix via http://host.docker.internal
#   (extra_hosts is set in docker-compose.yml).
# ==========================================================================

# ── Colours ────────────────────────────────────────────────────────────────
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
CYAN='\033[0;36m'
MAGENTA='\033[0;35m'
BOLD='\033[1m'
NC='\033[0m'

# ── Constants ──────────────────────────────────────────────────────────────
INSTALL_LOG="/var/log/alborada-web-install.log"
LOG_MAX_BYTES=10485760          # 10 MB — rotate when exceeded
STATE_FILE="/root/.alborada_web_install_state"
CREDENTIALS_FILE="/root/.alborada_credentials"
DNS_RECORDS_FILE="/root/.alborada_dns_records"

APP_DISPLAY_NAME="Moissanite Radiance"
APP_DIR="/var/www/alborada"

DB_HOST="db"                    # Docker Compose service name
DB_PORT="3306"
DB_NAME="alborada"
DB_USERNAME="alborada"

DOCKER_WEB_PORT=8081            # Docker web container (proxied by host Nginx)
PHPMYADMIN_PORT=8082            # phpMyAdmin container (proxied at /phpmyadmin)
NODE_VERSION=20                 # Node.js LTS major version
DKIM_SELECTOR="mail"            # DKIM selector for the Postfix mail server

# ── Runtime state ──────────────────────────────────────────────────────────
DOMAIN=""
CANONICAL_DOMAIN=""
WWW_DOMAIN=""
DOMAIN_TYPE=""
NEEDS_WWW_REDIRECT=false
SITE_URL=""
ADMIN_EMAIL=""

MYSQL_ROOT_PASSWORD=""
DB_PASSWORD=""
ADMIN_PASSWORD=""

declare -A COMPLETED_STEPS

# ==========================================================================
# Logging
# ==========================================================================

setup_logging() {
    if [[ -f "$INSTALL_LOG" ]] && \
       [[ "$(stat -c%s "$INSTALL_LOG" 2>/dev/null || echo 0)" -gt "$LOG_MAX_BYTES" ]]; then
        mv "$INSTALL_LOG" "${INSTALL_LOG}.old"
    fi
    {
        echo "========================================================"
        echo "  ALBORADA BOX — WEB SERVER INSTALLATION LOG"
        echo "  Started : $(date)"
        echo "  Script  : $0"
        echo "========================================================"
        echo ""
    } > "$INSTALL_LOG"
    echo -e "${BLUE}[INFO]${NC} Log: $INSTALL_LOG"
}

_ts()          { date '+%Y-%m-%d %H:%M:%S'; }
log_info()     { echo -e "${BLUE}[INFO]${NC}  $1";      echo "[$(_ts)] [INFO]     $1" >> "$INSTALL_LOG"; }
log_success()  { echo -e "${GREEN}[OK]${NC}    $1";     echo "[$(_ts)] [OK]       $1" >> "$INSTALL_LOG"; }
log_warning()  { echo -e "${YELLOW}[WARN]${NC}  $1";    echo "[$(_ts)] [WARN]     $1" >> "$INSTALL_LOG"; }
log_error()    { echo -e "${RED}[ERR]${NC}   $1";       echo "[$(_ts)] [ERROR]    $1" >> "$INSTALL_LOG"; }
log_wait()     { echo -e "${YELLOW}[WAIT]${NC}  $1…";   echo "[$(_ts)] [WAIT]     $1" >> "$INSTALL_LOG"; }
log_progress() { echo -e "${BLUE}[....]${NC}  $1";      echo "[$(_ts)] [STEP]     $1" >> "$INSTALL_LOG"; }

log_header() {
    echo -e "\n${CYAN}══════════════════════════════════════════════════${NC}"
    echo -e "${CYAN}  $1${NC}"
    echo -e "${CYAN}══════════════════════════════════════════════════${NC}\n"
    echo "[$(_ts)] ── $1" >> "$INSTALL_LOG"
}

log_highlight() {
    echo -e "\n${MAGENTA}╔════════════════════════════════════════════════════╗${NC}"
    echo -e "${MAGENTA}  $1${NC}"
    echo -e "${MAGENTA}╚════════════════════════════════════════════════════╝${NC}\n"
}

# ==========================================================================
# Credential Generation
# ==========================================================================

generate_password() {
    openssl rand -base64 48 | tr -dc 'a-zA-Z0-9' | head -c "${1:-32}"
}

generate_credentials() {
    log_header "GENERATING SECURE CREDENTIALS"
    MYSQL_ROOT_PASSWORD=$(generate_password 24)
    DB_PASSWORD=$(generate_password 24)
    ADMIN_PASSWORD=$(generate_password 16)
    save_credentials
    log_success "Credentials written to $CREDENTIALS_FILE (chmod 600)"
}

save_credentials() {
    printf '# Moissanite Radiance Credentials — %s\nMYSQL_ROOT_PASSWORD=%s\nDB_PASSWORD=%s\nADMIN_PASSWORD=%s\n' \
        "$(date)" "$MYSQL_ROOT_PASSWORD" "$DB_PASSWORD" "$ADMIN_PASSWORD" \
        > "$CREDENTIALS_FILE"
    chmod 600 "$CREDENTIALS_FILE"
}

load_credentials() {
    [[ -f "$CREDENTIALS_FILE" ]] || return 1
    # shellcheck source=/dev/null
    source "$CREDENTIALS_FILE"
}

# ==========================================================================
# State Management — resumable installation
# ==========================================================================

save_state() {
    {
        echo "# Moissanite Radiance Web Install State — $(date)"
        echo "DOMAIN=$DOMAIN"
        echo "CANONICAL_DOMAIN=$CANONICAL_DOMAIN"
        echo "WWW_DOMAIN=$WWW_DOMAIN"
        echo "DOMAIN_TYPE=$DOMAIN_TYPE"
        echo "NEEDS_WWW_REDIRECT=$NEEDS_WWW_REDIRECT"
        echo "ADMIN_EMAIL=$ADMIN_EMAIL"
        echo "SITE_URL=$SITE_URL"
        for step in "${!COMPLETED_STEPS[@]}"; do
            echo "STEP_${step}=done"
        done
    } > "$STATE_FILE"
    chmod 600 "$STATE_FILE"
}

load_state() {
    [[ -f "$STATE_FILE" ]] || return 1
    # shellcheck source=/dev/null
    source <(grep -v '^STEP_' "$STATE_FILE" | grep -v '^#')
    while IFS='=' read -r k _v; do
        [[ "$k" == STEP_* ]] && COMPLETED_STEPS["${k#STEP_}"]="done"
    done < "$STATE_FILE"
    return 0
}

mark_done() { COMPLETED_STEPS["$1"]="done"; save_state; }
is_done()   { [[ "${COMPLETED_STEPS[$1]:-}" == "done" ]]; }

run_step() {
    local name="$1" func="$2"
    if is_done "$name"; then
        log_info "Skipping already completed step: $name"
        return 0
    fi
    log_header "STEP: $name"
    if $func; then
        mark_done "$name"
        log_success "Completed: $name"
        return 0
    else
        log_error "Step FAILED: $name"
        echo ""
        echo -e "${RED}Fix the error above, then re-run this script — it will resume from here.${NC}"
        exit 1
    fi
}

check_resume() {
    if load_state && load_credentials; then
        echo ""
        echo -e "${MAGENTA}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
        echo -e "${YELLOW}  PREVIOUS INSTALLATION DETECTED${NC}"
        echo -e "${MAGENTA}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
        echo ""
        echo -e "  Domain : ${CYAN}$CANONICAL_DOMAIN${NC}"
        echo -e "  Email  : ${CYAN}$ADMIN_EMAIL${NC}"
        echo ""
        echo -e "  Completed steps:"
        for s in "${!COMPLETED_STEPS[@]}"; do echo -e "    ${GREEN}✓${NC} $s"; done
        echo ""
        echo -e "  [1] Resume from where it stopped  (recommended)"
        echo -e "  [2] Start completely fresh"
        echo ""
        echo -en "${CYAN}>>> Choice [1]: ${NC}"
        read -r _choice
        if [[ "$_choice" == "2" ]]; then
            rm -f "$STATE_FILE" "$CREDENTIALS_FILE"
            unset COMPLETED_STEPS; declare -gA COMPLETED_STEPS
            log_info "Starting fresh"
            return 1
        fi
        log_info "Resuming"
        return 0
    fi
    return 1
}

# ==========================================================================
# Domain Validation
# ==========================================================================

validate_domain() {
    local raw d
    raw="$1"
    d=$(echo "$raw" | sed 's|^https\?://||;s|/.*||' | tr '[:upper:]' '[:lower:]')

    [[ -z "$d" ]] && { log_error "Domain cannot be empty"; return 1; }
    [[ "$d" =~ ^[0-9]{1,3}(\.[0-9]{1,3}){3}$ ]] && { log_error "IP addresses are not valid domains"; return 1; }
    [[ ! "$d" =~ ^[a-z0-9]([a-z0-9-]*[a-z0-9])?(\.[a-z0-9]([a-z0-9-]*[a-z0-9])?)*\.[a-z]{2,}$ ]] && \
        { log_error "Invalid domain format: $d"; return 1; }

    local dots is_ccsld=false
    dots=$(echo "$d" | tr -cd '.' | wc -c)
    [[ "$d" =~ \.(com|net|org|co|gov|edu|ac|mil)\.[a-z]{2}$ ]] && is_ccsld=true

    if [[ "$d" =~ ^www\. ]]; then
        DOMAIN_TYPE="www_input"; CANONICAL_DOMAIN="${d#www.}"; WWW_DOMAIN="$d"; NEEDS_WWW_REDIRECT=true
    elif [[ $dots -eq 1 ]] || ( [[ $dots -eq 2 ]] && [[ "$is_ccsld" == true ]] ); then
        DOMAIN_TYPE="root";      CANONICAL_DOMAIN="$d";         WWW_DOMAIN="www.$d"; NEEDS_WWW_REDIRECT=true
    else
        DOMAIN_TYPE="subdomain"; CANONICAL_DOMAIN="$d";         WWW_DOMAIN="";       NEEDS_WWW_REDIRECT=false
    fi
    DOMAIN="$CANONICAL_DOMAIN"
    return 0
}

get_domain() {
    echo ""
    echo -e "${MAGENTA}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
    echo -e "${MAGENTA}  DOMAIN CONFIGURATION${NC}"
    echo -e "${MAGENTA}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
    echo ""
    echo -e "${YELLOW}  This domain will serve the web panel over HTTPS.${NC}"
    echo -e "${YELLOW}  The IPTV streaming server is a SEPARATE VPS (streamsetup.sh).${NC}"
    echo -e "${GREEN}  Examples:${NC} ${CYAN}alboradabox.com${NC}  or  ${CYAN}app.example.com${NC}"
    echo ""
    while true; do
        echo -en "${CYAN}${BOLD}>>> Domain name: ${NC}"
        read -r _input
        validate_domain "$_input" && break
        log_warning "Please try again with a valid domain name"
    done
    SITE_URL="https://${CANONICAL_DOMAIN}"
    ADMIN_EMAIL="admin@${CANONICAL_DOMAIN}"
    echo ""
    log_success "Domain set: $CANONICAL_DOMAIN"
    log_info    "  Web panel    : $SITE_URL"
    log_info    "  Admin panel  : $SITE_URL/admin"
    log_info    "  phpMyAdmin   : $SITE_URL/phpmyadmin"
    [[ "$NEEDS_WWW_REDIRECT" == true ]] && \
        log_info "  WWW redirect : https://$WWW_DOMAIN → $SITE_URL"
    echo ""
}

# ==========================================================================
# Preflight Checks
# ==========================================================================

check_root() {
    [[ $EUID -eq 0 ]] || { log_error "Must run as root.  Try: sudo ./websetup.sh"; exit 1; }
    log_info "Running as root: OK"
}

check_ubuntu() {
    local os ver
    os=$(lsb_release -si 2>/dev/null || grep '^ID=' /etc/os-release | cut -d= -f2 | tr -d '"')
    ver=$(lsb_release -sr 2>/dev/null || grep '^VERSION_ID=' /etc/os-release | cut -d= -f2 | tr -d '"')
    log_info "OS: $os $ver"
    [[ "$os" == "Ubuntu" ]] || log_warning "Tested on Ubuntu only (running: $os)"
    if [[ "$ver" == "22.04" || "$ver" == "24.04" ]]; then
        log_success "Ubuntu $ver LTS — supported"
    else
        log_warning "Recommended: Ubuntu 22.04/24.04 LTS (detected: $ver) — proceeding but untested"
    fi
}

check_disk() {
    local need="${1:-25}" avail
    avail=$(( $(df / --output=avail 2>/dev/null | tail -1) / 1024 / 1024 ))
    [[ $avail -ge $need ]] || { log_error "Disk: need ${need} GB free, only ${avail} GB available"; exit 1; }
    log_success "Disk: ${avail} GB available"
}

verify_project_files() {
    log_header "VERIFYING PROJECT FILES AT $APP_DIR"
    if [[ ! -f "$APP_DIR/artisan" ]]; then
        log_error "Laravel project not found at $APP_DIR"
        echo ""
        echo -e "${YELLOW}Upload the project first, then re-run this script:${NC}"
        echo -e "  ${CYAN}git clone <repo-url> $APP_DIR${NC}"
        echo -e "  or:  ${CYAN}scp -r /local/alborada root@YOUR_VPS:$APP_DIR${NC}"
        echo ""
        echo -e "${YELLOW}Expected directory structure:${NC}"
        echo -e "  $APP_DIR/"
        echo -e "    ├── artisan"
        echo -e "    ├── composer.json"
        echo -e "    ├── package.json"
        echo -e "    ├── docker-compose.yml"
        echo -e "    ├── websetup.sh"
        echo -e "    └── docker/php/Dockerfile"
        exit 1
    fi
    for f in composer.json package.json docker-compose.yml docker/php/Dockerfile; do
        [[ -f "$APP_DIR/$f" ]] || { log_error "Missing required file: $APP_DIR/$f"; exit 1; }
    done
    log_success "All required project files present"
}

# ==========================================================================
# STEP 01 — System Packages + Node.js 20 LTS
# ==========================================================================
# Node.js is required on the VPS host to run:
#   npm ci        — install devDependencies (vite, tailwind, etc.)
#   npm run build — compile CSS/JS to public/build/
# Both public/build/ and node_modules/ are gitignored, so they must
# be produced here before containers serve any pages.
# ==========================================================================

install_system_packages() {
    log_header "INSTALLING SYSTEM PACKAGES"
    export DEBIAN_FRONTEND=noninteractive

    log_wait "Updating package lists"
    apt-get update -y >> "$INSTALL_LOG" 2>&1

    log_wait "Upgrading installed packages"
    apt-get upgrade -y >> "$INSTALL_LOG" 2>&1

    log_wait "Installing essential tools"
    apt-get install -y \
        curl wget gnupg2 software-properties-common apt-transport-https \
        ca-certificates git openssl unzip zip lsb-release \
        ufw fail2ban >> "$INSTALL_LOG" 2>&1
    log_success "Essential system packages installed"

    # ── Node.js 20 LTS ─────────────────────────────────────────
    local installed_major=0
    command -v node &>/dev/null && \
        installed_major=$(node --version 2>/dev/null | cut -d. -f1 | tr -d 'v' || echo 0)

    if [[ $installed_major -ge $NODE_VERSION ]]; then
        log_info "Node.js already installed: $(node --version)  npm: $(npm --version)"
    else
        log_wait "Installing Node.js ${NODE_VERSION} LTS via NodeSource"
        curl -fsSL "https://deb.nodesource.com/setup_${NODE_VERSION}.x" | bash - >> "$INSTALL_LOG" 2>&1
        apt-get install -y nodejs >> "$INSTALL_LOG" 2>&1
        log_success "Node.js $(node --version) installed, npm $(npm --version)"
    fi
}

# ==========================================================================
# STEP 02 — Docker CE + Docker Compose Plugin
# ==========================================================================

install_docker() {
    log_header "INSTALLING DOCKER CE"

    if ! command -v docker &>/dev/null; then
        log_wait "Adding Docker GPG key and apt repository"
        install -m 0755 -d /etc/apt/keyrings
        curl -fsSL https://download.docker.com/linux/ubuntu/gpg | \
            gpg --dearmor -o /etc/apt/keyrings/docker.gpg 2>/dev/null
        chmod a+r /etc/apt/keyrings/docker.gpg

        echo "deb [arch=$(dpkg --print-architecture) signed-by=/etc/apt/keyrings/docker.gpg] \
https://download.docker.com/linux/ubuntu $(lsb_release -cs) stable" \
            | tee /etc/apt/sources.list.d/docker.list > /dev/null

        apt-get update >> "$INSTALL_LOG" 2>&1
        log_wait "Installing Docker CE packages"
        apt-get install -y \
            docker-ce docker-ce-cli containerd.io \
            docker-buildx-plugin docker-compose-plugin >> "$INSTALL_LOG" 2>&1
        log_success "Docker  : $(docker --version)"
        log_success "Compose : $(docker compose version)"
    else
        log_info "Docker already present: $(docker --version)"
    fi

    # ── iptables-legacy (Ubuntu 22.04 / 24.04 compatibility) ─────────────────
    # Ubuntu 22.04+ defaults to iptables-nft (nftables backend).
    # Docker requires iptables-legacy for its bridge networking driver.
    # Without this, docker compose up fails with "driver failed to set up
    # container networking".
    log_wait "Configuring iptables-legacy for Docker bridge networking"
    apt-get install -y iptables >> "$INSTALL_LOG" 2>&1
    update-alternatives --set iptables  /usr/sbin/iptables-legacy  >> "$INSTALL_LOG" 2>&1 || true
    update-alternatives --set ip6tables /usr/sbin/ip6tables-legacy >> "$INSTALL_LOG" 2>&1 || true
    log_success "iptables-legacy configured"

    # ── Docker daemon configuration ───────────────────────────────────────────
    # Enable iptables explicitly and add log rotation.
    mkdir -p /etc/docker
    cat > /etc/docker/daemon.json <<'DAEMON'
{
    "iptables": true,
    "log-driver": "json-file",
    "log-opts": {
        "max-size": "10m",
        "max-file": "3"
    }
}
DAEMON
    log_success "Docker daemon.json written"

    systemctl enable docker >> "$INSTALL_LOG" 2>&1
    systemctl restart docker
    log_success "Docker daemon restarted with iptables-legacy backend"
}

# ==========================================================================
# STEP 03 — Host-level Nginx
# Terminates TLS on port 443 and reverse-proxies to the Docker web
# container on 127.0.0.1:8081 and phpMyAdmin on 127.0.0.1:8082.
# ==========================================================================

install_nginx() {
    log_header "INSTALLING HOST NGINX"
    if dpkg -s nginx 2>/dev/null | grep -q '^Status: install ok installed'; then
        log_info "Nginx already installed"
        return 0
    fi
    apt-get install -y nginx >> "$INSTALL_LOG" 2>&1
    systemctl enable nginx
    systemctl start nginx
    log_success "Nginx installed"
}

configure_nginx() {
    log_header "CONFIGURING HOST NGINX"
    local CONF="/etc/nginx/sites-available/$CANONICAL_DOMAIN"

    # Build www-redirect block (only when NEEDS_WWW_REDIRECT=true)
    local WWW_BLOCK=""
    if [[ "$NEEDS_WWW_REDIRECT" == true ]]; then
        WWW_BLOCK="
server {
    listen 80;
    server_name ${WWW_DOMAIN};
    return 301 http://${CANONICAL_DOMAIN}\$request_uri;
}
"
    fi

    cat > "$CONF" <<NGINX
${WWW_BLOCK}
server {
    listen 80;
    server_name ${CANONICAL_DOMAIN};

    client_max_body_size 100M;

    # Security headers (certbot preserves these when it upgrades this block to HTTPS)
    add_header X-Frame-Options           "SAMEORIGIN"                        always;
    add_header X-Content-Type-Options    "nosniff"                           always;
    add_header X-XSS-Protection          "1; mode=block"                     always;
    add_header Referrer-Policy           "strict-origin-when-cross-origin"   always;
    add_header Strict-Transport-Security "max-age=31536000; includeSubDomains" always;

    # phpMyAdmin (Docker container on port ${PHPMYADMIN_PORT})
    location = /phpmyadmin { return 301 /phpmyadmin/; }
    location /phpmyadmin/ {
        proxy_pass         http://127.0.0.1:${PHPMYADMIN_PORT}/;
        proxy_set_header   Host              \$host;
        proxy_set_header   X-Real-IP         \$remote_addr;
        proxy_set_header   X-Forwarded-For   \$proxy_add_x_forwarded_for;
        proxy_set_header   X-Forwarded-Proto \$scheme;
        proxy_read_timeout 120s;
    }

    # Proxy to Docker web container (port ${DOCKER_WEB_PORT})
    location / {
        proxy_pass         http://127.0.0.1:${DOCKER_WEB_PORT};
        proxy_set_header   Host              \$host;
        proxy_set_header   X-Real-IP         \$remote_addr;
        proxy_set_header   X-Forwarded-For   \$proxy_add_x_forwarded_for;
        proxy_set_header   X-Forwarded-Proto \$scheme;
        proxy_read_timeout 120s;
        proxy_buffering    off;
    }

    # Block hidden files (except .well-known for certbot)
    location ~ /\.(?!well-known) { deny all; }
}
NGINX

    ln -sf "$CONF" /etc/nginx/sites-enabled/
    rm -f /etc/nginx/sites-enabled/default

    nginx -t >> "$INSTALL_LOG" 2>&1 || { log_error "Nginx config test failed"; nginx -t; return 1; }
    systemctl reload nginx
    log_success "Nginx configured: $CANONICAL_DOMAIN → 127.0.0.1:${DOCKER_WEB_PORT} (+ /phpmyadmin → :${PHPMYADMIN_PORT})"
}

# ==========================================================================
# STEP 04 — SSL Certificate (Let's Encrypt / Certbot)
# ==========================================================================

install_ssl() {
    log_header "SSL CERTIFICATE — LET'S ENCRYPT"
    apt-get install -y certbot python3-certbot-nginx >> "$INSTALL_LOG" 2>&1

    local IP
    IP=$(curl -s -4 --max-time 8 ifconfig.me 2>/dev/null \
        || curl -s -4 --max-time 8 icanhazip.com 2>/dev/null \
        || echo "YOUR_VPS_IP")

    echo ""
    echo -e "${MAGENTA}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
    echo -e "${YELLOW}  DNS A RECORDS — add these before continuing${NC}"
    echo -e "${MAGENTA}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
    echo ""
    echo -e "  Your WEB server IP: ${CYAN}${IP}${NC}"
    echo ""
    echo -e "  Type  Name      Value"
    echo -e "  ──────────────────────────────────────────"
    echo -e "  A     @         ${IP}   ← main domain"
    [[ "$NEEDS_WWW_REDIRECT" == true ]] && \
    echo -e "  A     www       ${IP}   ← www redirect"
    echo -e "  A     mail      ${IP}   ← mail server (this VPS)"
    echo -e "  A     stream    <STREAMING_SERVER_IP>   ← IPTV server (separate VPS)"
    echo ""
    echo -e "${YELLOW}  Cloudflare tip: enable proxy (orange) for @ and www only.${NC}"
    echo -e "${YELLOW}  Keep 'stream' and 'mail' as grey cloud (DNS-only).${NC}"
    echo ""
    echo -e "  Verify: ${CYAN}https://dnschecker.org${NC}"
    echo ""
    echo -e "${MAGENTA}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
    echo -en "${CYAN}${BOLD}>>> Press Enter once DNS has propagated: ${NC}"
    read -r

    local domains=(-d "$CANONICAL_DOMAIN")
    [[ "$NEEDS_WWW_REDIRECT" == true ]] && domains+=(-d "$WWW_DOMAIN")

    if certbot --nginx "${domains[@]}" \
            --non-interactive --agree-tos --email "$ADMIN_EMAIL" --redirect \
            >> "$INSTALL_LOG" 2>&1; then
        log_success "SSL certificate installed for $CANONICAL_DOMAIN"
    else
        log_warning "Certbot failed — continuing without HTTPS"
        log_info "  Fix DNS then run manually:"
        log_info "  certbot --nginx -d $CANONICAL_DOMAIN --email $ADMIN_EMAIL"
    fi

    systemctl enable certbot.timer  2>/dev/null || true
    systemctl start  certbot.timer  2>/dev/null || true
    log_success "SSL auto-renewal enabled"
}

# ==========================================================================
# STEP 05 — Frontend Assets (Vite + Tailwind CSS)
# public/build/ and node_modules/ are in .gitignore.
# They must be produced on the VPS before containers serve any page.
# ==========================================================================

build_frontend() {
    log_header "BUILDING FRONTEND ASSETS (VITE + TAILWIND)"
    cd "$APP_DIR" || return 1

    log_wait "Installing npm dependencies"
    if ! npm ci --prefer-offline >> "$INSTALL_LOG" 2>&1; then
        log_warning "npm ci failed — retrying with npm install"
        npm install >> "$INSTALL_LOG" 2>&1 || { log_error "npm install failed — see $INSTALL_LOG"; return 1; }
    fi
    log_success "npm dependencies installed"

    log_wait "Building production assets (npm run build)"
    if ! npm run build >> "$INSTALL_LOG" 2>&1; then
        log_error "npm run build failed — see $INSTALL_LOG"
        return 1
    fi

    [[ -d "$APP_DIR/public/build" ]] || \
        { log_error "public/build/ not found after build"; return 1; }
    log_success "Frontend built → $APP_DIR/public/build/"
}

# ==========================================================================
# STEP 06 — Docker Compose Override (DB credentials + phpMyAdmin)
#
# IMPORTANT: Never put "web: ports:" in this override file.
# Docker Compose MERGES (not replaces) port lists across files.
# Ports for the base services are set in docker-compose.yml:
#   web → 127.0.0.1:8081:80
#   db  → internal only (never public)
# This file injects the auto-generated DB credentials and adds the
# phpMyAdmin service (a NEW service, so no merge conflict).
# ==========================================================================

write_compose_override() {
    log_header "WRITING DOCKER COMPOSE OVERRIDE (DB CREDENTIALS + PHPMYADMIN)"

    cat > "$APP_DIR/docker-compose.override.yml" <<OVERRIDE
# Auto-generated by websetup.sh on $(date)
# DO NOT EDIT MANUALLY — re-run websetup.sh to regenerate.
#
# NOTE: No "ports:" entries for base services here.  Docker Compose
# concatenates port lists from all files, so adding ports for web/db here
# would cause double-binding conflicts.  phpmyadmin is a NEW service, so
# its port mapping lives here safely (127.0.0.1 only — proxied by Nginx).

services:
  db:
    environment:
      MYSQL_ROOT_PASSWORD: "${MYSQL_ROOT_PASSWORD}"
      MYSQL_DATABASE: "${DB_NAME}"
      MYSQL_USER: "${DB_USERNAME}"
      MYSQL_PASSWORD: "${DB_PASSWORD}"

  phpmyadmin:
    image: phpmyadmin:latest
    container_name: alborada_phpmyadmin
    restart: unless-stopped
    environment:
      PMA_HOST: db
      PMA_ABSOLUTE_URI: "${SITE_URL}/phpmyadmin/"
      UPLOAD_LIMIT: 100M
    ports:
      - "127.0.0.1:${PHPMYADMIN_PORT}:80"
    depends_on:
      - db
    networks:
      - alborada
OVERRIDE

    chmod 600 "$APP_DIR/docker-compose.override.yml"
    log_success "docker-compose.override.yml written"
}

# ==========================================================================
# STEP 07 — Laravel .env (production)
# ==========================================================================

write_env() {
    log_header "WRITING LARAVEL .ENV"

    cat > "$APP_DIR/.env" <<ENV
APP_NAME="${APP_DISPLAY_NAME}"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=${SITE_URL}
ASSET_URL=${SITE_URL}

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
SESSION_SECURE_COOKIE=true

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local
QUEUE_CONNECTION=database

CACHE_STORE=database

REDIS_CLIENT=phpredis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# Mail — local Postfix mail server on this VPS (free, self-hosted).
# The app container relays via host.docker.internal:25 (host Postfix).
# DKIM/SPF/DMARC DNS records: see ${DNS_RECORDS_FILE}
# To switch to an external SMTP provider instead, use Admin → Settings → SMTP.
MAIL_MAILER=smtp
MAIL_SCHEME=smtp
MAIL_HOST=host.docker.internal
MAIL_PORT=25
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_FROM_ADDRESS=noreply@${CANONICAL_DOMAIN}
MAIL_FROM_NAME="${APP_DISPLAY_NAME}"

# Stripe — add keys after installation (Admin → Settings or edit .env directly)
STRIPE_PUBLIC_KEY=
STRIPE_SECRET_KEY=
STRIPE_WEBHOOK_SECRET=

# Social Login — configure in Admin → Settings → Social Login
GOOGLE_CLIENT_ID=
GOOGLE_CLIENT_SECRET=
GOOGLE_REDIRECT_URL=${SITE_URL}/auth/google/callback
FACEBOOK_CLIENT_ID=
FACEBOOK_CLIENT_SECRET=
FACEBOOK_REDIRECT_URL=${SITE_URL}/auth/facebook/callback

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

VITE_APP_NAME="${APP_DISPLAY_NAME}"
ENV

    # 644: owner (root) read/write, group+others read.
    # Must be world-readable so the Docker www-data user (UID 1000, not root)
    # can read .env at runtime, and key:generate can write APP_KEY into it.
    chmod 644 "$APP_DIR/.env"
    log_success ".env written at $APP_DIR/.env"
}

# ==========================================================================
# STEP 08 — Build Docker Image + Start All Containers
# ==========================================================================

start_containers() {
    log_header "BUILDING AND STARTING DOCKER CONTAINERS"
    cd "$APP_DIR" || return 1

    # ── Clean up any leftover state from a previous run ───────────────────────
    # If a prior install attempt failed mid-way, old containers and/or the
    # 'alborada' Docker network may be in a broken state.  A fresh 'up' on top
    # of stale state produces "driver failed to set up container networking".
    # 'down --remove-orphans' removes containers + networks but preserves the
    # named 'dbdata' volume so MySQL data is not lost on resume.
    log_progress "Removing any leftover containers and networks from previous runs"
    docker compose down --remove-orphans >> "$INSTALL_LOG" 2>&1 || true
    docker network rm alborada 2>/dev/null || true   # belt-and-suspenders cleanup
    log_success "Previous container state cleared"

    log_wait "Building PHP 8.4-FPM application image (may take a few minutes)"
    if ! docker compose build app >> "$INSTALL_LOG" 2>&1; then
        log_error "Docker image build failed — check $INSTALL_LOG"
        return 1
    fi
    log_success "Application image built"

    log_wait "Starting all containers: app, web (nginx), db (mysql), worker, phpmyadmin"
    if ! docker compose up -d >> "$INSTALL_LOG" 2>&1; then
        log_error "docker compose up failed — check $INSTALL_LOG"
        log_error "Run 'docker compose logs' in $APP_DIR for details"
        return 1
    fi

    # Wait for MySQL
    log_wait "Waiting for MySQL to be ready (up to 90 s)"
    local tries=30
    while [[ $tries -gt 0 ]]; do
        docker compose exec -T db mysqladmin ping -h localhost --silent 2>/dev/null && break
        sleep 3
        (( tries-- ))
    done
    [[ $tries -eq 0 ]] && { log_error "MySQL did not start in time — check: docker compose logs db"; return 1; }
    log_success "MySQL ready"

    # Verify the app can actually authenticate — if the dbdata volume was created
    # by a prior `docker compose up` (before the override was written with the
    # generated password), MySQL ignores MYSQL_PASSWORD and the app credentials
    # will be wrong.  Detect this and automatically wipe + reinitialise the volume.
    #
    # _mysql_auth_ok: retry for up to 60 s.  mysqladmin ping returns alive as
    # soon as the socket opens, but MySQL's init scripts (which create the user)
    # finish several seconds later.  We must keep retrying, not check once.
    _mysql_auth_ok() {
        local t
        for t in $(seq 1 20); do
            if docker compose exec -T db \
                   mysql -u alborada -p"${DB_PASSWORD}" \
                   -e "SELECT 1;" alborada >> "$INSTALL_LOG" 2>&1; then
                return 0
            fi
            sleep 3
        done
        return 1
    }

    log_progress "Verifying MySQL credentials (up to 60 s for init scripts to complete)"
    if ! _mysql_auth_ok; then
        log_warning "MySQL credential mismatch — the dbdata volume was initialised with a"
        log_warning "different password (e.g. from a manual 'docker compose up' before setup)."
        log_warning "Wiping the dbdata volume and reinitialising MySQL with the correct password."

        docker compose down >> "$INSTALL_LOG" 2>&1 || true
        docker volume rm alborada_dbdata >> "$INSTALL_LOG" 2>&1 || true

        log_wait "Restarting containers with fresh MySQL volume"
        if ! docker compose up -d >> "$INSTALL_LOG" 2>&1; then
            log_error "docker compose up failed after volume reset — check $INSTALL_LOG"
            return 1
        fi

        log_wait "Waiting for MySQL to reinitialise and create user (up to 90 s)"
        tries=30
        while [[ $tries -gt 0 ]]; do
            docker compose exec -T db mysqladmin ping -h localhost --silent 2>/dev/null && break
            sleep 3
            (( tries-- ))
        done
        [[ $tries -eq 0 ]] && { log_error "MySQL did not restart in time"; return 1; }

        # mysqladmin ping is alive — now wait for the user creation scripts to finish
        log_progress "Waiting for MySQL user initialisation to complete"
        if ! _mysql_auth_ok; then
            log_error "MySQL credential check still failing after volume reset."
            log_error "Expected: MYSQL_PASSWORD=${DB_PASSWORD}"
            log_error "Verify /var/www/alborada/docker-compose.override.yml contains that value."
            return 1
        fi
        log_success "MySQL reinitialised with correct credentials"
    else
        log_success "MySQL credentials verified"
    fi

    # Wait for PHP-FPM
    log_wait "Waiting for PHP-FPM container to respond"
    tries=15
    while [[ $tries -gt 0 ]]; do
        docker compose exec -T app php --version >/dev/null 2>&1 && break
        sleep 2
        (( tries-- ))
    done
    [[ $tries -eq 0 ]] && { log_error "App container did not respond — check: docker compose logs app"; return 1; }
    log_success "All containers started"

    docker compose ps >> "$INSTALL_LOG" 2>&1
}

# ==========================================================================
# STEP 09 — Laravel Application Initialization
# ==========================================================================

setup_laravel() {
    log_header "INITIALIZING LARAVEL APPLICATION"
    cd "$APP_DIR" || return 1

    # ── Pre-flight: verify MySQL credentials ─────────────────────────────
    # When step 08 (start_containers) is already marked done but migrations
    # still fail with "Access denied", the dbdata volume was created before
    # the override file was written (e.g. from a manual 'docker compose up').
    # Detect this here so the script auto-recovers even on a clean resume.
    #
    # _sl_mysql_auth_ok: retry for up to 60 s — mysqladmin ping returns alive
    # before MySQL's init scripts finish creating the user account.
    _sl_mysql_auth_ok() {
        local t
        for t in $(seq 1 20); do
            if docker compose exec -T db \
                   mysql -u alborada -p"${DB_PASSWORD}" \
                   -e "SELECT 1;" alborada >> "$INSTALL_LOG" 2>&1; then
                return 0
            fi
            sleep 3
        done
        return 1
    }

    log_progress "Verifying MySQL credentials before migrations (up to 60 s)"
    if ! _sl_mysql_auth_ok; then
        log_warning "MySQL credential mismatch detected — wiping dbdata volume and reinitialising"
        docker compose down >> "$INSTALL_LOG" 2>&1 || true
        docker volume rm alborada_dbdata >> "$INSTALL_LOG" 2>&1 || true

        log_wait "Restarting containers with fresh MySQL volume"
        docker compose up -d >> "$INSTALL_LOG" 2>&1 || { log_error "docker compose up failed"; return 1; }

        log_wait "Waiting for MySQL to reinitialise and create user (up to 90 s)"
        local tries=30
        while [[ $tries -gt 0 ]]; do
            docker compose exec -T db mysqladmin ping -h localhost --silent 2>/dev/null && break
            sleep 3
            (( tries-- ))
        done
        [[ $tries -eq 0 ]] && { log_error "MySQL did not restart in time"; return 1; }

        log_progress "Waiting for MySQL user initialisation to complete"
        if ! _sl_mysql_auth_ok; then
            log_error "MySQL credential check still failing after volume reset."
            log_error "Expected: MYSQL_PASSWORD=${DB_PASSWORD}"
            log_error "Verify /var/www/alborada/docker-compose.override.yml contains that value."
            return 1
        fi
        log_success "MySQL reinitialised with correct credentials"
    else
        log_success "MySQL credentials verified"
    fi

    # ── 9a. Composer install ──────────────────────────────────────────────
    # vendor/ is gitignored and must be installed here.
    # If the VPS blocks outbound connections to packagist.org or
    # api.github.com, pre-upload vendor/ from your local machine:
    #   Local: composer install --no-dev && tar -czf vendor.tar.gz vendor/
    #   Upload: scp vendor.tar.gz root@VPS_IP:/var/www/alborada/
    #   VPS:    cd /var/www/alborada && tar -xzf vendor.tar.gz
    # Then re-run websetup.sh — this step will be skipped automatically.
    if [[ -f "$APP_DIR/vendor/autoload.php" ]]; then
        log_info "vendor/autoload.php already present — regenerating autoloader only"
        docker compose exec -T app composer dump-autoload \
            --no-dev --optimize --no-interaction >> "$INSTALL_LOG" 2>&1 || true
        log_success "Composer: autoloader regenerated from existing vendor/"
    else
        log_wait "Running composer install --no-dev --optimize-autoloader"

        # Attempt 1: standard install
        if ! docker compose exec -T app \
                sh -c 'COMPOSER_PROCESS_TIMEOUT=300 composer install \
                    --no-dev --optimize-autoloader --no-interaction \
                    --no-scripts' >> "$INSTALL_LOG" 2>&1; then

            log_warning "First attempt failed — retrying with packagist mirror"

            # Attempt 2: configure the Tencent mirror (avoids GitHub API CDN)
            # and retry once more
            docker compose exec -T app \
                composer config --global repos.packagist composer \
                https://mirrors.cloud.tencent.com/composer/ \
                >> "$INSTALL_LOG" 2>&1 || true

            if ! docker compose exec -T app \
                    sh -c 'COMPOSER_PROCESS_TIMEOUT=300 composer install \
                        --no-dev --optimize-autoloader --no-interaction \
                        --no-scripts' >> "$INSTALL_LOG" 2>&1; then

                log_error "composer install failed on both attempts."
                log_error ""
                log_error "This VPS cannot reach packagist.org or api.github.com."
                log_error "Pre-build vendor/ on your local machine and upload it:"
                log_error ""
                log_error "  ON YOUR LOCAL MACHINE:"
                log_error "    cd /path/to/alborada"
                log_error "    composer install --no-dev"
                log_error "    tar -czf vendor.tar.gz vendor/"
                log_error "    scp vendor.tar.gz root@YOUR_VPS_IP:/var/www/alborada/"
                log_error ""
                log_error "  ON THE VPS:"
                log_error "    cd /var/www/alborada && tar -xzf vendor.tar.gz"
                log_error ""
                log_error "Then re-run: sudo /var/www/alborada/websetup.sh"
                log_error "The script will resume from this step automatically."
                return 1
            fi
        fi

        # Restore global packagist config to default after mirror usage
        docker compose exec -T app \
            composer config --global repos.packagist composer \
            https://packagist.org >> "$INSTALL_LOG" 2>&1 || true

        # Run post-install scripts now that vendor/ is present
        log_progress "Running composer post-install scripts (package:discover)"
        docker compose exec -T app \
            composer run-script post-autoload-dump --no-interaction \
            >> "$INSTALL_LOG" 2>&1 || \
            log_warning "post-autoload-dump scripts had warnings (non-fatal)"

        log_success "Composer: vendor/ installed"
    fi

    # ── 9b. Generate application key ────────────────────────────────────
    # .env is owned by root; make it writable by www-data (UID 1000) before
    # running key:generate so the command can write APP_KEY into the file.
    log_progress "Generating APP_KEY"
    chmod 666 "$APP_DIR/.env"
    docker compose exec -T app php artisan key:generate --force >> "$INSTALL_LOG" 2>&1 \
        || { log_error "key:generate failed"; return 1; }
    chmod 644 "$APP_DIR/.env"
    # Verify the key was actually written
    grep -q 'APP_KEY=base64:' "$APP_DIR/.env" \
        || { log_error ".env APP_KEY is still blank after key:generate"; return 1; }
    log_success "APP_KEY generated"

    # ── 9c. Clear any stale config cache ────────────────────────────────
    log_progress "Clearing stale config cache"
    docker compose exec -T app php artisan config:clear >> "$INSTALL_LOG" 2>&1 || true

    # ── 9d. File permissions ─────────────────────────────────────────────
    # Must run BEFORE db:seed — MediaContentSeeder downloads poster images
    # into public/uploads/, which fails if the directory is still owned by
    # root (fresh git clone) instead of www-data (UID 1000 inside Docker).
    log_progress "Setting write permissions on storage/, bootstrap/cache/, and public upload directories"

    # Laravel framework dirs
    docker compose exec -T app chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true
    chmod -R 775 "$APP_DIR/storage" "$APP_DIR/bootstrap/cache" 2>/dev/null || true

    # Public upload directories — must be writable by www-data (UID 1000) inside Docker.
    # On a fresh git clone these are owned by root or the deploy user, so they must be
    # chmod'd to 775 or chown'd to www-data.  Without this, any file upload (and the
    # seeder's poster downloads) fails on the server even though it works locally.
    for _dir in uploaded uploads profile featured bank-slips; do
        mkdir -p "$APP_DIR/public/$_dir"
        chmod 775 "$APP_DIR/public/$_dir"
    done
    docker compose exec -T app chown -R www-data:www-data \
        public/uploaded public/uploads public/profile public/featured public/bank-slips \
        2>/dev/null || true

    log_success "File permissions set"

    # ── 9e. Run all migrations ───────────────────────────────────────────
    log_wait "Running database migrations"
    if ! docker compose exec -T app php artisan migrate --force >> "$INSTALL_LOG" 2>&1; then
        log_error "Migrations failed — check $INSTALL_LOG"
        return 1
    fi
    log_success "Migrations complete"

    # ── 9f. Seed: permissions + languages + media content ───────────────
    # DatabaseSeeder calls: PermissionSeeder, LanguageSeeder, MediaContentSeeder
    # It does NOT call UserSeeder (which has hardcoded credentials).
    # We create the admin user manually below with generated credentials.
    log_wait "Seeding database (permissions, languages, media content)"
    if ! docker compose exec -T app php artisan db:seed --force >> "$INSTALL_LOG" 2>&1; then
        log_warning "DatabaseSeeder failed — some defaults may be missing (non-fatal)"
    else
        log_success "Database seeded"
    fi

    # ── 9g. Create admin user with generated credentials ────────────────
    # UserSeeder creates a 'Super Admin' role and admin@example.com/111111.
    # We bypass UserSeeder and instead create the admin with our secure,
    # generated credentials.  type=1 matches the project's integer enum.
    log_progress "Creating admin user: $ADMIN_EMAIL"
    docker compose exec -T app php artisan tinker --execute="
\$role = \Spatie\Permission\Models\Role::firstOrCreate(
    ['name' => 'Super Admin', 'guard_name' => 'web']
);
\$role->syncPermissions(\Spatie\Permission\Models\Permission::all());

\$user = \App\Models\User::updateOrCreate(
    ['email' => '$ADMIN_EMAIL'],
    [
        'name'              => 'Administrator',
        'password'          => bcrypt('$ADMIN_PASSWORD'),
        'type'              => 1,
        'status'            => 1,
        'email_verified_at' => now(),
    ]
);
\$user->syncRoles('Super Admin');
echo \$user->wasRecentlyCreated ? 'Admin user created.' : 'Admin user updated.';
echo PHP_EOL . 'Role: Super Admin assigned.';
    " >> "$INSTALL_LOG" 2>&1 \
        || log_warning "Admin user creation via tinker failed — register manually at $SITE_URL/register"

    # ── 9h. Storage symlink ──────────────────────────────────────────────
    # Links public/storage → storage/app/public so uploaded files are accessible.
    log_progress "Creating storage symlink (public/storage → storage/app/public)"
    docker compose exec -T app php artisan storage:link >> "$INSTALL_LOG" 2>&1 || true
    log_success "Storage symlink created"

    log_success "Laravel application initialized"
}

# ==========================================================================
# STEP 10 — Free Email Server (Postfix + OpenDKIM)
#
# Self-hosted, zero-cost outbound mail for welcome emails, renewal
# reminders, receipts, etc.  No Mailgun/Brevo account needed.
#
# How it works:
#   - Postfix runs on the VPS host and listens on port 25.
#   - Only localhost and the Docker network (172.16.0.0/12) may relay —
#     the firewall never exposes port 25 to the internet for relaying.
#   - The Laravel app container sends via host.docker.internal:25.
#   - OpenDKIM signs every outgoing message so mail passes DKIM checks.
#   - SPF / DKIM / DMARC DNS records are written to $DNS_RECORDS_FILE —
#     ADD THEM to your DNS or mail will land in spam.
#
# NOTE: some VPS providers block OUTBOUND port 25 by default
# (DigitalOcean, Hetzner for new accounts, …).  If test mail never
# arrives, open a support ticket asking them to unblock port 25, or
# fall back to an external SMTP provider in Admin → Settings → SMTP.
# ==========================================================================

install_mail_server() {
    log_header "INSTALLING FREE EMAIL SERVER (POSTFIX + OPENDKIM)"
    export DEBIAN_FRONTEND=noninteractive

    local MAIL_HOSTNAME="mail.${CANONICAL_DOMAIN}"
    local KEY_DIR="/etc/opendkim/keys/${CANONICAL_DOMAIN}"

    # ── 1. Install packages (preseed postfix so it never prompts) ─────────
    log_wait "Installing Postfix, OpenDKIM and mail utilities"
    echo "postfix postfix/main_mailer_type string Internet Site" | debconf-set-selections
    echo "postfix postfix/mailname string ${CANONICAL_DOMAIN}"   | debconf-set-selections
    apt-get install -y postfix opendkim opendkim-tools mailutils >> "$INSTALL_LOG" 2>&1 \
        || { log_error "Package install failed — see $INSTALL_LOG"; return 1; }
    log_success "Postfix + OpenDKIM installed"

    # ── 2. Postfix main configuration ─────────────────────────────────────
    log_progress "Configuring Postfix"
    postconf -e "myhostname = ${MAIL_HOSTNAME}"
    postconf -e "mydomain = ${CANONICAL_DOMAIN}"
    postconf -e "myorigin = ${CANONICAL_DOMAIN}"
    postconf -e "mydestination = \$myhostname, ${CANONICAL_DOMAIN}, localhost.localdomain, localhost"
    postconf -e "inet_interfaces = all"
    postconf -e "inet_protocols = ipv4"
    # Relaying allowed ONLY from localhost and the Docker network.
    # UFW additionally restricts inbound :25 to 172.16.0.0/12 (step 11),
    # so this box can never be abused as an open relay.
    postconf -e "mynetworks = 127.0.0.0/8 172.16.0.0/12"
    postconf -e "smtpd_relay_restrictions = permit_mynetworks, defer_unauth_destination"
    postconf -e "smtpd_banner = \$myhostname ESMTP"
    postconf -e "smtpd_helo_required = yes"
    postconf -e "disable_vrfy_command = yes"
    # Outbound TLS (to Gmail, Outlook, …) — keep opportunistic encryption.
    postconf -e "smtp_tls_security_level = may"
    # Inbound TLS must stay OFF.  Port 25 is only reachable from localhost and
    # the Docker network (never leaves this machine), and if STARTTLS is
    # advertised, Symfony Mailer auto-upgrades and then rejects the connection
    # because no certificate can match the name 'host.docker.internal'.
    postconf -e "smtpd_tls_security_level = none"
    postconf -e "message_size_limit = 26214400"    # 25 MB

    # ── 3. DKIM key + OpenDKIM configuration ──────────────────────────────
    log_progress "Generating 2048-bit DKIM key (selector: ${DKIM_SELECTOR})"
    mkdir -p "$KEY_DIR"
    if [[ ! -f "${KEY_DIR}/${DKIM_SELECTOR}.private" ]]; then
        opendkim-genkey -b 2048 -d "$CANONICAL_DOMAIN" -D "$KEY_DIR" \
            -s "$DKIM_SELECTOR" -v >> "$INSTALL_LOG" 2>&1 \
            || { log_error "DKIM key generation failed"; return 1; }
    else
        log_info "DKIM key already exists — keeping it (DNS record stays valid)"
    fi
    chown -R opendkim:opendkim /etc/opendkim
    chmod 600 "${KEY_DIR}/${DKIM_SELECTOR}.private"

    cat > /etc/opendkim.conf <<DKIMCONF
# Auto-generated by websetup.sh
Syslog                  yes
UMask                   002
Mode                    sv
SubDomains              no
AutoRestart             yes
AutoRestartRate         10/1h
Background              yes
DNSTimeout              5
Canonicalization        relaxed/simple
SignatureAlgorithm      rsa-sha256
OversignHeaders         From
KeyTable                refile:/etc/opendkim/key.table
SigningTable            refile:/etc/opendkim/signing.table
ExternalIgnoreList      /etc/opendkim/trusted.hosts
InternalHosts           /etc/opendkim/trusted.hosts
Socket                  inet:8891@localhost
PidFile                 /run/opendkim/opendkim.pid
UserID                  opendkim
DKIMCONF

    cat > /etc/opendkim/key.table <<KEYTABLE
${DKIM_SELECTOR}._domainkey.${CANONICAL_DOMAIN} ${CANONICAL_DOMAIN}:${DKIM_SELECTOR}:${KEY_DIR}/${DKIM_SELECTOR}.private
KEYTABLE

    cat > /etc/opendkim/signing.table <<SIGNTABLE
*@${CANONICAL_DOMAIN} ${DKIM_SELECTOR}._domainkey.${CANONICAL_DOMAIN}
SIGNTABLE

    cat > /etc/opendkim/trusted.hosts <<TRUSTED
127.0.0.1
localhost
172.16.0.0/12
*.${CANONICAL_DOMAIN}
TRUSTED

    # ── 4. Hook OpenDKIM into Postfix as a milter ─────────────────────────
    postconf -e "milter_default_action = accept"
    postconf -e "milter_protocol = 6"
    postconf -e "smtpd_milters = inet:localhost:8891"
    postconf -e "non_smtpd_milters = inet:localhost:8891"

    # ── 5. Start services ─────────────────────────────────────────────────
    systemctl enable opendkim postfix >> "$INSTALL_LOG" 2>&1 || true
    systemctl restart opendkim
    systemctl restart postfix
    if ! systemctl is-active --quiet postfix; then
        log_error "Postfix failed to start — check: journalctl -u postfix --no-pager | tail -n 40"
        return 1
    fi
    if ! systemctl is-active --quiet opendkim; then
        log_error "OpenDKIM failed to start — check: journalctl -u opendkim --no-pager | tail -n 40"
        return 1
    fi
    log_success "Postfix and OpenDKIM running"

    # ── 6. Write the DNS records the operator must add ────────────────────
    local IP DKIM_VALUE
    IP=$(curl -s -4 --max-time 8 ifconfig.me 2>/dev/null || echo "YOUR_VPS_IP")
    # mail.txt splits the record into multiple quoted chunks — flatten them.
    DKIM_VALUE=$(grep -o '"[^"]*"' "${KEY_DIR}/${DKIM_SELECTOR}.txt" 2>/dev/null \
        | tr -d '"' | tr -d '\n')

    cat > "$DNS_RECORDS_FILE" <<DNSREC
# ============================================================
# EMAIL DNS RECORDS for ${CANONICAL_DOMAIN} — generated $(date)
# Add ALL of these in your DNS provider, or mail will go to spam.
# ============================================================

# 1) A record for the mail host
Type: A
Name: mail
Value: ${IP}

# 2) MX record
Type: MX
Name: @
Value: mail.${CANONICAL_DOMAIN}
Priority: 10

# 3) SPF
Type: TXT
Name: @
Value: v=spf1 a mx ip4:${IP} ~all

# 4) DKIM  (selector: ${DKIM_SELECTOR})
Type: TXT
Name: ${DKIM_SELECTOR}._domainkey
Value: ${DKIM_VALUE}

# 5) DMARC
Type: TXT
Name: _dmarc
Value: v=DMARC1; p=quarantine; rua=mailto:admin@${CANONICAL_DOMAIN}; fo=1

# 6) Reverse DNS (PTR) — set in your VPS PROVIDER's control panel,
#    not in your DNS zone:  ${IP} → mail.${CANONICAL_DOMAIN}

# Test deliverability after DNS propagates: https://www.mail-tester.com
DNSREC
    chmod 600 "$DNS_RECORDS_FILE"
    log_success "Email DNS records written to $DNS_RECORDS_FILE"

    # ── 7. Verify Postfix is listening on :25 ─────────────────────────────
    if ss -tlnp | grep -q ':25 '; then
        log_success "Postfix listening on port 25"
    else
        log_error "Postfix is not listening on port 25"
        return 1
    fi

    log_info "Test after DNS is set:  echo 'Hello' | mail -s 'Test' -a 'From: noreply@${CANONICAL_DOMAIN}' you@example.com"
    log_warning "If test mail never arrives, your VPS provider may block OUTBOUND port 25 —"
    log_warning "ask their support to unblock it, or use an external SMTP in Admin → Settings → SMTP."
}

# ==========================================================================
# STEP 11 — Firewall (UFW)
# ==========================================================================

configure_firewall() {
    log_header "CONFIGURING UFW FIREWALL"

    ufw --force reset >> "$INSTALL_LOG" 2>&1
    ufw default deny incoming
    ufw default allow outgoing
    ufw allow ssh
    ufw allow 'Nginx Full'
    # Postfix: reachable only from Docker containers (app/worker → host :25).
    # Port 25 is NOT open to the internet — this box cannot be used as a relay.
    ufw allow from 172.16.0.0/12 to any port 25 proto tcp comment 'Postfix relay from Docker' >> "$INSTALL_LOG" 2>&1
    ufw --force enable

    log_success "Firewall enabled"
    log_info   "  Open ports:"
    log_info   "    22    (SSH)"
    log_info   "    80    (HTTP  → HTTPS redirect via Nginx)"
    log_info   "    443   (HTTPS → web panel + /phpmyadmin via Nginx)"
    log_info   "    25    (Postfix — Docker network 172.16.0.0/12 ONLY, not public)"
    log_info   "  Blocked: 8081/8082 (127.0.0.1 only), 3306 (Docker internal only)"
    ufw status >> "$INSTALL_LOG" 2>&1
}

# ==========================================================================
# STEP 12 — Laravel Scheduler Cron Job
# ==========================================================================
# Scheduled jobs (defined in routes/console.php):
#   ProcessSubscriptionRenewalsJob — daily at midnight
#   SendRenewalRemindersJob        — daily at 08:00
#   SendExpiryAlertsJob            — daily at 09:00
#   SyncXtreamStatusJob            — every 4 hours
# ==========================================================================

configure_cron() {
    log_header "CONFIGURING LARAVEL SCHEDULER CRON"

    # Use --project-directory so cron picks up both docker-compose.yml
    # and docker-compose.override.yml regardless of working directory.
    local DOCKER_BIN
    DOCKER_BIN=$(command -v docker || echo "/usr/bin/docker")
    local JOB="* * * * * ${DOCKER_BIN} compose --project-directory ${APP_DIR} exec -T app php artisan schedule:run >> /var/log/laravel-scheduler.log 2>&1"

    if crontab -l 2>/dev/null | grep -q "artisan schedule:run"; then
        log_info "Scheduler cron already exists — skipping"
    else
        (crontab -l 2>/dev/null || true; echo "$JOB") | crontab -
        log_success "Scheduler cron installed for root"
    fi

    log_info "Active scheduled jobs:"
    log_info "  • ProcessSubscriptionRenewalsJob — daily at midnight"
    log_info "  • SendRenewalRemindersJob         — daily at 08:00"
    log_info "  • SendExpiryAlertsJob             — daily at 09:00"
    log_info "  • SyncXtreamStatusJob             — every 4 hours"
    log_info "Scheduler log: /var/log/laravel-scheduler.log"
}

# ==========================================================================
# STEP 13 — Production Cache & Optimization
# ==========================================================================

optimize_production() {
    log_header "BUILDING PRODUCTION CACHE"
    cd "$APP_DIR" || return 1

    log_wait "Caching config, routes, views, and events"
    docker compose exec -T app php artisan config:cache >> "$INSTALL_LOG" 2>&1
    docker compose exec -T app php artisan route:cache  >> "$INSTALL_LOG" 2>&1
    docker compose exec -T app php artisan view:cache   >> "$INSTALL_LOG" 2>&1
    docker compose exec -T app php artisan event:cache  >> "$INSTALL_LOG" 2>&1 || true
    docker compose exec -T app php artisan optimize     >> "$INSTALL_LOG" 2>&1

    log_success "Production cache built"
}

# ==========================================================================
# STEP — Whole-project file permissions
#
# Runs AFTER every step that writes into the app tree (composer, seeders,
# production cache) so nothing is left owned by root.  www-data inside the
# container is UID/GID 1000 (remapped in docker/php/Dockerfile), so a host-
# side chown to 1000:1000 makes the tree owned by the app user both on the
# host and inside Docker.
# ==========================================================================

set_project_permissions() {
    log_header "SETTING WHOLE-PROJECT FILE PERMISSIONS"
    cd "$APP_DIR" || return 1

    log_wait "Applying ownership www-data (UID 1000) to $APP_DIR"
    chown -R 1000:1000 "$APP_DIR" 2>/dev/null || true

    # Baseline: directories 755, files 644.  vendor/ and node_modules/ are
    # skipped — composer/npm manage their own executable bits and re-chmodding
    # tens of thousands of files there breaks their .bin/ tools for nothing.
    log_progress "Applying baseline permissions (directories 755, files 644)"
    find "$APP_DIR" \( -path "$APP_DIR/node_modules" -o -path "$APP_DIR/vendor" \) -prune \
        -o -type d -print0 | xargs -0 -r chmod 755 2>/dev/null || true
    find "$APP_DIR" \( -path "$APP_DIR/node_modules" -o -path "$APP_DIR/vendor" \) -prune \
        -o -type f -print0 | xargs -0 -r chmod 644 2>/dev/null || true

    # Writable dirs: Laravel framework dirs + public upload dirs
    log_progress "Applying write permissions (775) to storage, cache and upload dirs"
    chmod -R 775 "$APP_DIR/storage" "$APP_DIR/bootstrap/cache" 2>/dev/null || true
    for _dir in uploaded uploads profile featured bank-slips; do
        [ -d "$APP_DIR/public/$_dir" ] && chmod -R 775 "$APP_DIR/public/$_dir"
    done

    # Restore executable bits stripped by the baseline pass, and lock secrets
    chmod 755 "$APP_DIR"/*.sh "$APP_DIR/artisan" 2>/dev/null || true
    chmod 640 "$APP_DIR/.env" 2>/dev/null || true
    chmod 600 "$APP_DIR/docker-compose.override.yml" 2>/dev/null || true

    log_success "Whole-project permissions set"
}

# ==========================================================================
# STEP 14 — Verify Installation
# ==========================================================================

verify_install() {
    log_header "VERIFYING INSTALLATION"
    cd "$APP_DIR" || return 0

    _chk() {
        local label="$1"
        shift
        if "$@" >/dev/null 2>&1; then
            log_success "$label"
        else
            log_warning "$label — NOT OK"
        fi
    }

    _chk "Host Nginx running"        systemctl is-active --quiet nginx
    _chk "App container running"     docker compose ps --status running | grep -q alborada_app
    _chk "Web (Nginx) container"     docker compose ps --status running | grep -q alborada_nginx
    _chk "MySQL container running"   docker compose ps --status running | grep -q alborada_mysql
    _chk "Worker container running"  docker compose ps --status running | grep -q alborada_worker
    _chk "phpMyAdmin container"      docker compose ps --status running | grep -q alborada_phpmyadmin
    _chk "Postfix running"           systemctl is-active --quiet postfix
    _chk "OpenDKIM running"          systemctl is-active --quiet opendkim
    _chk "SSL certificate present"   test -f "/etc/letsencrypt/live/$CANONICAL_DOMAIN/fullchain.pem"
    _chk "Scheduler cron active"     crontab -l 2>/dev/null | grep -q "schedule:run"
    _chk "Frontend assets built"     test -d "$APP_DIR/public/build"
    _chk "vendor/ present"           test -d "$APP_DIR/vendor"
    _chk "Storage symlink present"   test -L "$APP_DIR/public/storage"

    local code
    code=$(curl -s -o /dev/null -w "%{http_code}" --max-time 8 \
        "http://127.0.0.1:${DOCKER_WEB_PORT}" 2>/dev/null || echo 0)
    if [[ "$code" =~ ^(200|301|302)$ ]]; then
        log_success "Web app responding on :${DOCKER_WEB_PORT} (HTTP $code)"
    else
        log_warning "Web app not responding on :${DOCKER_WEB_PORT} (HTTP $code)"
    fi

    code=$(curl -s -o /dev/null -w "%{http_code}" --max-time 8 \
        "http://127.0.0.1:${PHPMYADMIN_PORT}" 2>/dev/null || echo 0)
    if [[ "$code" =~ ^(200|301|302)$ ]]; then
        log_success "phpMyAdmin responding on :${PHPMYADMIN_PORT} (HTTP $code)"
    else
        log_warning "phpMyAdmin not responding on :${PHPMYADMIN_PORT} (HTTP $code)"
    fi
}

# ==========================================================================
# Final Summary
# ==========================================================================

show_summary() {
    local IP
    IP=$(curl -s -4 --max-time 8 ifconfig.me 2>/dev/null || echo "YOUR_VPS_IP")

    log_highlight "${APP_DISPLAY_NAME} — WEB SERVER INSTALLATION COMPLETE"

    echo -e "${YELLOW}WEB PANEL${NC}"
    echo -e "  URL         : ${CYAN}${SITE_URL}${NC}"
    echo -e "  Admin panel : ${CYAN}${SITE_URL}/admin${NC}"
    echo -e "  Admin email : ${CYAN}${ADMIN_EMAIL}${NC}"
    echo -e "  Admin pass  : ${CYAN}${ADMIN_PASSWORD}${NC}"
    echo ""

    echo -e "${YELLOW}PHPMYADMIN (DATABASE MANAGEMENT)${NC}"
    echo -e "  URL      : ${CYAN}${SITE_URL}/phpmyadmin${NC}"
    echo -e "  Login    : ${CYAN}${DB_USERNAME}${NC} / ${CYAN}${DB_PASSWORD}${NC}"
    echo -e "  Root     : ${CYAN}root${NC} / ${CYAN}${MYSQL_ROOT_PASSWORD}${NC}"
    echo ""

    echo -e "${YELLOW}EMAIL SERVER (POSTFIX — FREE, SELF-HOSTED)${NC}"
    echo -e "  The app already sends mail through the local Postfix server."
    echo -e "  ${RED}${BOLD}ACTION REQUIRED:${NC} add the SPF/DKIM/DMARC DNS records from:"
    echo -e "    ${CYAN}cat ${DNS_RECORDS_FILE}${NC}"
    echo -e "  Also set reverse DNS (PTR) in your VPS provider panel: ${CYAN}${IP} → mail.${CANONICAL_DOMAIN}${NC}"
    echo -e "  Test deliverability: ${CYAN}https://www.mail-tester.com${NC}"
    echo -e "  ${YELLOW}Note: if your provider blocks outbound port 25, ask support to unblock it,${NC}"
    echo -e "  ${YELLOW}or configure an external SMTP in Admin → Settings → SMTP instead.${NC}"
    echo ""

    echo -e "${YELLOW}DATABASE${NC}"
    echo -e "  DB name  : $DB_NAME"
    echo -e "  DB user  : $DB_USERNAME"
    echo -e "  DB pass  : $DB_PASSWORD"
    echo -e "  Root PW  : $MYSQL_ROOT_PASSWORD"
    echo ""

    echo -e "${YELLOW}STEP A — INSTALL THE STREAMING SERVER (SEPARATE VPS)${NC}"
    echo -e "  On a second Ubuntu 22.04 VPS, run: ${CYAN}sudo ./streamsetup.sh${NC}"
    echo -e "  (See install-stream.txt for the full guide.)"
    echo ""

    echo -e "${YELLOW}STEP B — CONNECT WEB PANEL TO THE STREAMING SERVER${NC}"
    echo -e "  1. Open Admin Panel → Settings → IPTV"
    echo -e "  2. Set ${CYAN}xtream_base_url = http://STREAMING_SERVER_IP:8080${NC}"
    echo -e "  3. Enter your XUI.ONE admin username and password"
    echo -e "  4. Set  ${CYAN}iptv_provisioning_enabled = 1${NC}"
    echo -e "  5. Click Test Connection → should return 'Connection successful'"
    echo ""

    echo -e "${YELLOW}STEP C — CONFIGURE STRIPE (PAYMENTS)${NC}"
    echo -e "  Edit: ${CYAN}${APP_DIR}/.env${NC}"
    echo -e "    STRIPE_PUBLIC_KEY=pk_live_..."
    echo -e "    STRIPE_SECRET_KEY=sk_live_..."
    echo -e "    STRIPE_WEBHOOK_SECRET=whsec_..."
    echo -e "  Then: ${CYAN}cd ${APP_DIR} && docker compose exec app php artisan config:cache${NC}"
    echo -e "  Webhook endpoint: ${CYAN}${SITE_URL}/stripe/webhook${NC}"
    echo -e "  Events: payment_intent.succeeded, payment_intent.payment_failed"
    echo ""

    echo -e "${YELLOW}STEP D — TEST END-TO-END${NC}"
    echo -e "  1. Use Stripe test card: ${CYAN}4242 4242 4242 4242${NC} (any date, any CVC)"
    echo -e "  2. Register a customer and complete a subscription purchase"
    echo -e "  3. Verify: XUI.ONE → Lines → new line created on the streaming server"
    echo -e "  4. Verify: customer welcome email with Xtream Codes login"
    echo ""

    echo -e "${YELLOW}DOCKER MANAGEMENT${NC}"
    echo -e "  Status  : ${CYAN}cd ${APP_DIR} && docker compose ps${NC}"
    echo -e "  App logs: ${CYAN}docker compose logs -f app${NC}"
    echo -e "  Worker  : ${CYAN}docker compose logs -f worker${NC}"
    echo -e "  Restart : ${CYAN}docker compose restart app web worker phpmyadmin${NC}"
    echo -e "  Rebuild : ${CYAN}docker compose build app && docker compose up -d${NC}"
    echo ""

    echo -e "${YELLOW}UPDATING THE APPLICATION${NC}"
    echo -e "  cd ${APP_DIR}"
    echo -e "  git pull"
    echo -e "  npm ci && npm run build"
    echo -e "  docker compose build app && docker compose up -d"
    echo -e "  docker compose exec app php artisan migrate --force"
    echo -e "  docker compose exec app php artisan optimize"
    echo ""

    echo -e "${YELLOW}FILES${NC}"
    echo -e "  Application  : ${CYAN}${APP_DIR}${NC}"
    echo -e "  .env         : ${CYAN}${APP_DIR}/.env${NC}"
    echo -e "  Install log  : ${CYAN}${INSTALL_LOG}${NC}"
    echo -e "  Credentials  : ${CYAN}${CREDENTIALS_FILE}${NC}  (chmod 600)"
    echo -e "  Email DNS    : ${CYAN}${DNS_RECORDS_FILE}${NC}  (chmod 600)"
    echo -e "  Scheduler log: ${CYAN}/var/log/laravel-scheduler.log${NC}"
    echo ""

    echo -e "${RED}${BOLD}SAVE THESE CREDENTIALS — shown above and stored in ${CREDENTIALS_FILE}${NC}"
}

# ==========================================================================
# Help
# ==========================================================================

show_help() {
    echo ""
    echo -e "${CYAN}${APP_DISPLAY_NAME} — Web Server Deployment${NC}"
    echo ""
    echo -e "${YELLOW}Usage:${NC}"
    echo "  sudo ./websetup.sh [--domain yourdomain.com]"
    echo ""
    echo -e "${YELLOW}This script sets up the WEB server only.${NC}"
    echo "  The IPTV streaming server (XUI.ONE) is installed on a SEPARATE"
    echo "  VPS with streamsetup.sh — see install-stream.txt."
    echo ""
    echo -e "${YELLOW}Prerequisites:${NC}"
    echo "  • Ubuntu 22.04 or 24.04 LTS VPS (fresh install)"
    echo "  • Minimum: 2 vCPU / 4 GB RAM / 40 GB SSD"
    echo "  • Project files at $APP_DIR (git clone or scp)"
    echo "  • Domain name with DNS control"
    echo ""
    echo -e "${YELLOW}What this script installs:${NC}"
    echo "  1. System packages + Node.js ${NODE_VERSION} LTS"
    echo "  2. Docker CE + Docker Compose plugin"
    echo "  3. Host Nginx (TLS termination + reverse proxy)"
    echo "  4. Let's Encrypt SSL certificate (auto-renewing)"
    echo "  5. Vite/Tailwind frontend build (npm run build)"
    echo "  6. Laravel 12 in Docker (PHP 8.4-FPM + MySQL 8 + queue worker)"
    echo "     ↳ composer install, migrate, seed, admin user"
    echo "  7. phpMyAdmin (Docker, served at https://yourdomain.com/phpmyadmin)"
    echo "  8. Free email server (Postfix + OpenDKIM, self-hosted)"
    echo "  9. UFW firewall"
    echo "  10. Laravel scheduler cron"
    echo ""
    echo -e "${YELLOW}Port layout:${NC}"
    echo "  443  HTTPS (web panel + /phpmyadmin, proxied by Nginx)"
    echo "  80   HTTP → HTTPS redirect"
    echo "  8081 Docker web container (localhost only, Nginx proxy target)"
    echo "  8082 phpMyAdmin container (localhost only, Nginx proxy target)"
    echo "  25   Postfix (localhost + Docker network only, never public relay)"
    echo "  3306 MySQL (Docker internal only, never public)"
    echo ""
}

# ==========================================================================
# Argument Parsing
# ==========================================================================

parse_args() {
    while [[ $# -gt 0 ]]; do
        case "$1" in
            --domain)
                validate_domain "$2" || { log_error "Invalid domain: $2"; exit 1; }
                SITE_URL="https://${CANONICAL_DOMAIN}"
                ADMIN_EMAIL="admin@${CANONICAL_DOMAIN}"
                shift 2
                ;;
            --help|-h)
                show_help; exit 0
                ;;
            *)
                log_error "Unknown option: $1"; show_help; exit 1
                ;;
        esac
    done
}

# ==========================================================================
# Main
# ==========================================================================

main() {
    setup_logging

    log_highlight "${APP_DISPLAY_NAME} — WEB SERVER SETUP"

    parse_args "$@"
    check_root
    check_ubuntu
    check_disk 20

    if check_resume; then
        verify_project_files
    else
        verify_project_files
        generate_credentials
        [[ -z "$DOMAIN" ]] && get_domain
    fi

    echo ""
    echo -e "${MAGENTA}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
    echo -e "${MAGENTA}  INSTALLATION PLAN — WEB SERVER${NC}"
    echo -e "${MAGENTA}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
    echo ""
    echo -e "  Domain     : ${CYAN}${CANONICAL_DOMAIN}${NC}"
    echo -e "  Web panel  : ${CYAN}${SITE_URL}${NC}"
    echo -e "  Admin      : ${CYAN}${ADMIN_EMAIL}${NC}"
    echo -e "  phpMyAdmin : ${CYAN}${SITE_URL}/phpmyadmin${NC}"
    echo -e "  Mail       : ${CYAN}mail.${CANONICAL_DOMAIN}${NC} (Postfix, self-hosted)"
    echo ""
    echo -e "  Steps:"
    echo -e "    01. System packages + Node.js ${NODE_VERSION} LTS"
    echo -e "    02. Docker CE + Compose plugin"
    echo -e "    03. Host Nginx install + configure"
    echo -e "    04. Let's Encrypt SSL"
    echo -e "    05. Frontend build  (npm ci + npm run build)"
    echo -e "    06. Docker Compose override  (DB credentials + phpMyAdmin)"
    echo -e "    07. Laravel .env  (production)"
    echo -e "    08. Build + start containers  (PHP 8.4, MySQL 8, worker, phpMyAdmin)"
    echo -e "    09. Laravel init  (composer, migrate, seed, admin user)"
    echo -e "    10. Free email server  (Postfix + OpenDKIM)"
    echo -e "    11. UFW firewall"
    echo -e "    12. Scheduler cron"
    echo -e "    13. Production cache  (config, routes, views)"
    echo -e "    14. File permissions  (whole project → www-data)"
    echo -e "    15. Verification"
    echo ""
    echo -e "  ${YELLOW}The IPTV streaming server is installed separately with streamsetup.sh${NC}"
    echo ""
    echo -e "${MAGENTA}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
    echo -en "${CYAN}${BOLD}>>> Proceed with installation? (y/N): ${NC}"
    read -n 1 -r REPLY; echo ""
    [[ $REPLY =~ ^[Yy]$ ]] || { log_warning "Installation cancelled"; exit 0; }
    echo ""

    run_step "01_system_packages"    install_system_packages
    run_step "02_docker"             install_docker
    run_step "03_nginx_install"      install_nginx
    run_step "04_nginx_configure"    configure_nginx
    run_step "05_ssl"                install_ssl
    run_step "06_frontend_build"     build_frontend
    run_step "07_compose_override"   write_compose_override
    run_step "08_env_file"           write_env
    run_step "09_containers"         start_containers
    run_step "10_laravel_setup"      setup_laravel
    run_step "11_mail_server"        install_mail_server
    run_step "12_firewall"           configure_firewall
    run_step "13_cron"               configure_cron
    run_step "14_optimize"           optimize_production
    run_step "14b_permissions"       set_project_permissions
    run_step "15_verify"             verify_install

    # Remove state file only on full success
    rm -f "$STATE_FILE"

    show_summary
    echo "[$(_ts)] Web server installation completed successfully" >> "$INSTALL_LOG"
    exit 0
}

main "$@"
