#!/bin/bash
# ==========================================================================
# Moissanite Visions — Automated VPS Setup
# Laravel 12 IPTV Platform + XUI Xtream Streaming Server
# ==========================================================================
# Compatible : Ubuntu 22.04 LTS / 24.04 LTS (fresh install)
# Run as     : sudo ./autosetup.sh [--domain yourdomain.com]
#
# Port layout after installation:
#   :443  → Host Nginx (HTTPS/TLS) → 127.0.0.1:8081 (Docker web container)
#   :80   → HTTP → HTTPS redirect
#   :8080 → XUI Xtream IPTV panel  (host-level, public, not proxied)
#   :3306 → MySQL                  (127.0.0.1 only, never public)
#
# Docker → XUI Xtream communication:
#   The app container uses http://host.docker.internal:8080 to reach XUI Xtream.
#   (localhost inside Docker refers to the container, not the VPS host.)
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
INSTALL_LOG="/var/log/alborada-install.log"
LOG_MAX_BYTES=10485760          # 10 MB — rotate when exceeded
STATE_FILE="/root/.alborada_install_state"
CREDENTIALS_FILE="/root/.alborada_credentials"

APP_DISPLAY_NAME="Moissanite Visions"
APP_DIR="/var/www/alborada"

DB_HOST="db"                    # Docker Compose service name
DB_PORT="3306"
DB_NAME="alborada"
DB_USERNAME="alborada"

IPTV_PANEL_PORT=8080            # XUI Xtream — host-level
DOCKER_WEB_PORT=8081            # Docker web container (proxied by host Nginx)
NODE_VERSION=20                 # Node.js LTS major version

# XUI.ONE installer source. XUI.ONE is a LICENSED commercial panel — the
# installer URL is tied to your purchase and the vendor rotates these paths.
# Override without editing this file:  XUI_INSTALLER_URL="https://..." ./autosetup.sh
# Space-separated fallbacks are tried in order until one returns a real script.
XUI_INSTALLER_URL="${XUI_INSTALLER_URL:-}"
XUI_INSTALLER_URL_CANDIDATES=(
    "https://xtream-masters.com/guide/resources.php?file=xui-one/install.sh"
    "https://tut.xtream-masters.com/files/xui-one/install.sh"
)

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
        echo "  ALBORADA BOX — VPS INSTALLATION LOG"
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

log_banner() {
    local width=54
    local pad=$(( (width - ${#1}) / 2 ))
    local line
    printf -v line '%*s' "$width" '' && line="${line// /─}"
    echo -e "\n${CYAN}${line}${NC}"
    printf "${CYAN}%*s%s%*s${NC}\n" "$pad" "" "$1" "$pad" ""
    echo -e "${CYAN}${line}${NC}\n"
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
    printf '# Moissanite Visions Credentials — %s\nMYSQL_ROOT_PASSWORD=%s\nDB_PASSWORD=%s\nADMIN_PASSWORD=%s\n' \
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
        echo "# Moissanite Visions Install State — $(date)"
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
    echo -e "${YELLOW}  Customers stream IPTV at: http://stream.YOURDOMAIN:${XUI_CLIENT_PORT}${NC}"
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
    log_info    "  IPTV streams : http://stream.${CANONICAL_DOMAIN}:${XUI_CLIENT_PORT}"
    [[ "$NEEDS_WWW_REDIRECT" == true ]] && \
        log_info "  WWW redirect : https://$WWW_DOMAIN → $SITE_URL"
    echo ""
}

# ==========================================================================
# Preflight Checks
# ==========================================================================

check_root() {
    [[ $EUID -eq 0 ]] || { log_error "Must run as root.  Try: sudo ./autosetup.sh"; exit 1; }
    log_info "Running as root: OK"
}

check_ubuntu() {
    local os ver
    os=$(lsb_release -si 2>/dev/null || grep '^ID=' /etc/os-release | cut -d= -f2 | tr -d '"')
    ver=$(lsb_release -sr 2>/dev/null || grep '^VERSION_ID=' /etc/os-release | cut -d= -f2 | tr -d '"')
    log_info "OS: $os $ver"
    [[ "$os" == "Ubuntu" ]] || log_warning "Tested on Ubuntu only (running: $os)"
    if [[ "$ver" == "22.04" ]]; then
        log_success "Ubuntu 22.04 LTS — fully supported"
    elif [[ "$ver" == "24.04" ]]; then
        log_warning "Ubuntu 24.04 detected — XUI Xtream installer is NOT compatible with 24.04"
        log_warning "XUI Xtream step will likely fail. Downgrade to Ubuntu 22.04 LTS is strongly recommended."
        echo -en "${YELLOW}Continue anyway? (y/N): ${NC}"
        read -r _r; echo ""
        [[ "$_r" =~ ^[Yy]$ ]] || { log_warning "Aborted. Re-provision with Ubuntu 22.04 LTS."; exit 0; }
    else
        log_warning "Recommended: Ubuntu 22.04 LTS (detected: $ver) — proceeding but untested"
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
        echo -e "    ├── autosetup.sh"
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
# container on 127.0.0.1:8081.  XUI Xtream owns port 8080 exclusively.
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
    log_success "Nginx configured: $CANONICAL_DOMAIN → 127.0.0.1:${DOCKER_WEB_PORT}"
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
    echo -e "  Your VPS IP: ${CYAN}${IP}${NC}"
    echo ""
    echo -e "  Type  Name      Value"
    echo -e "  ──────────────────────────────────────────"
    echo -e "  A     @         ${IP}   ← main domain"
    [[ "$NEEDS_WWW_REDIRECT" == true ]] && \
    echo -e "  A     www       ${IP}   ← www redirect"
    echo -e "  A     stream    ${IP}   ← IPTV (keep DNS-only / grey cloud in Cloudflare)"
    echo ""
    echo -e "${YELLOW}  Cloudflare tip: enable proxy (orange) for @ and www only.${NC}"
    echo -e "${YELLOW}  Keep 'stream' as grey cloud — Cloudflare cannot proxy IPTV video.${NC}"
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
# STEP 06 — Docker Compose Override (DB credentials only)
#
# IMPORTANT: Never put "web: ports:" in this override file.
# Docker Compose MERGES (not replaces) port lists across files.
# Ports are set correctly in docker-compose.yml:
#   web → 127.0.0.1:8081:80   (keeps port 8080 free for XUI Xtream)
#   db  → 127.0.0.1:3306:3306 (localhost only, never public)
# This file only injects the auto-generated DB credentials.
# ==========================================================================

write_compose_override() {
    log_header "WRITING DOCKER COMPOSE OVERRIDE (DB CREDENTIALS)"

    cat > "$APP_DIR/docker-compose.override.yml" <<OVERRIDE
# Auto-generated by autosetup.sh on $(date)
# DO NOT EDIT MANUALLY — re-run autosetup.sh to regenerate.
#
# NOTE: No "ports:" entries here.  Docker Compose concatenates port lists
# from all files, so adding ports here would cause double-binding conflicts
# with XUI Xtream on port 8080.  Ports live only in docker-compose.yml.

services:
  db:
    environment:
      MYSQL_ROOT_PASSWORD: "${MYSQL_ROOT_PASSWORD}"
      MYSQL_DATABASE: "${DB_NAME}"
      MYSQL_USER: "${DB_USERNAME}"
      MYSQL_PASSWORD: "${DB_PASSWORD}"
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

# SMTP — configure in Admin → Settings → SMTP after installation
# Emails currently log to storage/logs/laravel.log (safe default)
MAIL_MAILER=log
MAIL_SCHEME=tls
MAIL_HOST=
MAIL_PORT=587
MAIL_USERNAME=
MAIL_PASSWORD=
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

    log_wait "Starting all containers: app, web (nginx), db (mysql), worker"
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
    # When step 09 (start_containers) is already marked done but migrations
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
    # Then re-run autosetup.sh — this step will be skipped automatically.
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
                log_error "Then re-run: sudo /var/www/alborada/autosetup.sh"
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
# STEP 10 — XUI.ONE 1.5.13 IPTV Streaming Panel (Ubuntu 22.04)
#
# Source: https://xtream-masters.com/guide/how_to_install_xui_one_ubuntu_22_04.php
#
# Installed directly on the host (not in Docker).
# Port layout:
#   :8080  — XUI.ONE admin panel
#   :2086  — client streaming port (customers use this)
#   :25461 — internal XUI.ONE management port
#
# Docker web container is on :8081 — no conflict.
#
# Docker → XUI.ONE communication:
#   http://host.docker.internal:8080  (extra_hosts set in docker-compose.yml)
#
# MariaDB is installed on the HOST for XUI.ONE.
# Docker MySQL runs inside containers only (no host port binding).
# ==========================================================================

XUI_CLIENT_PORT=2086

install_xui_one() {
    log_header "INSTALLING XUI.ONE 1.5.13 — IPTV STREAMING PANEL"

    # The web platform does NOT require a local panel — it can point at any
    # Xtream Codes-compatible panel via Admin → Settings → IPTV (xtream_base_url).
    # Skip the local install to finish setup and connect an external panel later:
    #   SKIP_XUI=1 ./autosetup.sh   (or  XUI_INSTALLER_URL=skip )
    if [[ "${SKIP_XUI:-0}" == "1" || "${XUI_INSTALLER_URL:-}" == "skip" ]]; then
        log_warning "Skipping local XUI.ONE install (SKIP_XUI set)."
        log_info "Connect an external Xtream/XUI panel later at: ${SITE_URL}/admin → Settings → IPTV"
        log_info "Set xtream_base_url, admin credentials, and iptv_provisioning_enabled = 1 there."
        return 0
    fi

    local IP XUI_DB_PASS
    IP=$(curl -s -4 --max-time 8 ifconfig.me 2>/dev/null || echo "YOUR_VPS_IP")
    XUI_DB_PASS=$(generate_password 24)

    echo ""
    echo -e "${MAGENTA}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
    echo -e "${YELLOW}  XUI.ONE 1.5.13 — Ubuntu 22.04 Installer${NC}"
    echo -e "${MAGENTA}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
    echo ""
    echo -e "  Admin panel : ${CYAN}http://${IP}:${IPTV_PANEL_PORT}${NC}"
    echo -e "  Stream port : ${CYAN}${XUI_CLIENT_PORT}${NC}  (customers connect here)"
    echo ""
    echo -e "${YELLOW}  When the installer prompts you:${NC}"
    echo -e "    Installation type → ${CYAN}main${NC}"
    echo -e "    MySQL host        → ${CYAN}127.0.0.1${NC}"
    echo -e "    Admin panel port  → ${CYAN}${IPTV_PANEL_PORT}${NC}"
    echo -e "    Client port       → ${CYAN}${XUI_CLIENT_PORT}${NC}"
    echo ""
    echo -en "${CYAN}>>> Press Enter to begin XUI.ONE installation: ${NC}"
    read -r

    # ── 1. Hostname ───────────────────────────────────────────────────────────
    log_progress "Setting hostname"
    hostnamectl set-hostname "xui.${CANONICAL_DOMAIN}" >> "$INSTALL_LOG" 2>&1
    grep -q "xui.${CANONICAL_DOMAIN}" /etc/hosts || \
        echo "127.0.0.1 xui.${CANONICAL_DOMAIN}" >> /etc/hosts
    log_success "Hostname: xui.${CANONICAL_DOMAIN}"

    # Disable UFW temporarily — installer needs unrestricted outbound access.
    # Step 11 (configure_firewall) re-enables it with the correct rules.
    ufw disable >> "$INSTALL_LOG" 2>&1 || true

    # ── 2. Legacy libssl1.1 (Ubuntu 22.04 ships OpenSSL 3; XUI.ONE needs 1.1) ─
    # The exact point-release filename rotates as Ubuntu publishes security
    # updates, so a single pinned URL eventually 404s. Try a candidate list and
    # verify each download is a real .deb before installing.
    log_wait "Installing legacy libssl1.1"
    local ssl_base="http://security.ubuntu.com/ubuntu/pool/main/o/openssl"
    local ssl_candidates=(
        "libssl1.1_1.1.1f-1ubuntu2.24_amd64.deb"
        "libssl1.1_1.1.1f-1ubuntu2.23_amd64.deb"
        "libssl1.1_1.1.1f-1ubuntu2.22_amd64.deb"
        "libssl1.1_1.1.1f-1ubuntu2.21_amd64.deb"
    )
    local ssl_installed=0 ssl_file
    for ssl_file in "${ssl_candidates[@]}"; do
        rm -f /tmp/libssl1.1.deb
        wget -q "${ssl_base}/${ssl_file}" -O /tmp/libssl1.1.deb >> "$INSTALL_LOG" 2>&1 || true
        # A 404 still writes a small HTML body — require a genuine Debian package.
        if [[ -s /tmp/libssl1.1.deb ]] && file /tmp/libssl1.1.deb 2>/dev/null | grep -qi "Debian binary package"; then
            dpkg -i /tmp/libssl1.1.deb >> "$INSTALL_LOG" 2>&1 || \
                apt-get install -f -y >> "$INSTALL_LOG" 2>&1
            ssl_installed=1
            log_info "libssl1.1 source: ${ssl_file}"
            break
        fi
    done
    ldconfig
    if ldconfig -p | grep -q "libssl.so.1.1"; then
        log_success "libssl1.1 installed"
    else
        log_error "libssl1.1 could not be installed — XUI.ONE binaries will not run"
        log_error "Fetch a working libssl1.1_*_amd64.deb from ${ssl_base} and install it manually, then re-run"
        return 1
    fi

    # ── 3. Python 2 (XUI.ONE internal scripts require Python 2) ──────────────
    # python2 ships in the 22.04 'universe' repo — no PPA needed. The deadsnakes
    # PPA does NOT provide python2 for jammy and only slows this step down.
    log_wait "Installing Python 2"
    add-apt-repository -y universe >> "$INSTALL_LOG" 2>&1 || true
    apt-get update >> "$INSTALL_LOG" 2>&1
    apt-get install -y python2 python2-dev >> "$INSTALL_LOG" 2>&1
    if ! command -v python2 >/dev/null 2>&1; then
        log_error "python2 could not be installed from the universe repo — XUI.ONE scripts require it"
        return 1
    fi
    ln -sf /usr/bin/python2 /usr/bin/python        2>/dev/null || true
    ln -sf /usr/bin/python2 /usr/local/bin/python2.7 2>/dev/null || true
    curl -sSL https://bootstrap.pypa.io/pip/2.7/get-pip.py -o /tmp/get-pip.py 2>/dev/null || true
    if [[ -s /tmp/get-pip.py ]]; then
        python2 /tmp/get-pip.py >> "$INSTALL_LOG" 2>&1 || true
        python2 -m pip install --upgrade "setuptools<45" "paramiko<2.9" >> "$INSTALL_LOG" 2>&1 || true
    fi
    log_success "Python 2 installed: $(python --version 2>&1)"

    # ── 4. MariaDB (host-level — separate from Docker MySQL) ──────────────────
    # Docker MySQL runs inside containers only (no host port binding).
    # Host MariaDB is exclusively for XUI.ONE.
    log_wait "Installing MariaDB"
    apt-get install -y mariadb-server mariadb-client >> "$INSTALL_LOG" 2>&1
    systemctl enable mariadb >> "$INSTALL_LOG" 2>&1
    systemctl start mariadb

    log_progress "Configuring MariaDB for XUI.ONE"
    local MYCNF="/etc/mysql/mariadb.conf.d/99-xui-one.cnf"
    # NOTE: do NOT set default_authentication_plugin here — that is a MySQL-8-only
    # server variable. MariaDB 10.6 (the 22.04 default) refuses to start on it,
    # which previously broke the whole XUI.ONE install. mysql_native_password is
    # already the MariaDB default and is set per-user in the CREATE USER below.
    cat > "$MYCNF" <<MYCNFBLOCK
# XUI.ONE required settings
[mysqld]
sql_mode                      = NO_ENGINE_SUBSTITUTION
innodb_strict_mode            = 0
max_allowed_packet            = 256M
innodb_buffer_pool_size       = 1G
innodb_file_per_table         = 1
wait_timeout                  = 28800
interactive_timeout           = 28800
max_connections               = 500
MYCNFBLOCK
    systemctl restart mariadb
    if ! systemctl is-active --quiet mariadb; then
        log_error "MariaDB failed to start after applying ${MYCNF}"
        log_error "Check: journalctl -u mariadb --no-pager | tail -n 40"
        return 1
    fi

    log_progress "Creating XUI.ONE database and user"
    mysql -u root <<MYSQL >> "$INSTALL_LOG" 2>&1
CREATE DATABASE IF NOT EXISTS xui_one
    DEFAULT CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS 'xui_user'@'localhost' IDENTIFIED BY '${XUI_DB_PASS}';
GRANT ALL PRIVILEGES ON xui_one.* TO 'xui_user'@'localhost';
FLUSH PRIVILEGES;
MYSQL
    if ! mysql -u root -e "USE xui_one" >> "$INSTALL_LOG" 2>&1; then
        log_error "XUI.ONE database 'xui_one' was not created — aborting"
        return 1
    fi
    log_success "MariaDB configured  (DB: xui_one  user: xui_user)"

    # ── 5. XUI.ONE installer ──────────────────────────────────────────────────
    log_wait "Downloading XUI.ONE 1.5.13 installer"

    # Build the URL list: operator override first, then known vendor fallbacks.
    local xui_urls=()
    [[ -n "$XUI_INSTALLER_URL" ]] && xui_urls+=("$XUI_INSTALLER_URL")
    xui_urls+=("${XUI_INSTALLER_URL_CANDIDATES[@]}")

    local xui_got=0 xui_url
    for xui_url in "${xui_urls[@]}"; do
        rm -f /tmp/xui-install.sh
        # -f fails on HTTP errors, -L follows the vendor's redirects to the CDN.
        curl -fL --max-time 60 "$xui_url" -o /tmp/xui-install.sh >> "$INSTALL_LOG" 2>&1 || true
        # A dead/changed URL yields an empty file or an HTML error page — don't bash that.
        if [[ -s /tmp/xui-install.sh ]] && head -c 64 /tmp/xui-install.sh | grep -qE '^#!|bash|sh'; then
            xui_got=1
            log_info "XUI.ONE installer source: ${xui_url}"
            break
        fi
    done

    if [[ "$xui_got" -ne 1 ]]; then
        log_error "Could not fetch a valid XUI.ONE installer from any known URL."
        log_error "XUI.ONE is a LICENSED product and the vendor rotates/removes installer URLs."
        log_error ""
        log_error "Option A — install the panel now: get the current installer link from your"
        log_error "  XUI.ONE account/license, then re-run (resumes from this step):"
        log_error "    XUI_INSTALLER_URL=\"https://your-installer-url/install.sh\" $0"
        log_error ""
        log_error "Option B — skip the local panel and finish the platform install; connect an"
        log_error "  external Xtream/XUI panel afterwards via Admin → Settings → IPTV:"
        log_error "    SKIP_XUI=1 $0"
        log_error ""
        log_error "(Do not substitute an unofficial 'cracked' installer — it is unlicensed and unsafe.)"
        return 1
    fi
    chmod +x /tmp/xui-install.sh

    log_info "Launching XUI.ONE installer — answer prompts as shown above"
    bash /tmp/xui-install.sh

    # ── 6. Post-install patches ───────────────────────────────────────────────

    # Patch 1 — force legacy SSL library path
    log_progress "Patch 1/5: SSL library path"
    echo "/usr/lib/x86_64-linux-gnu" > /etc/ld.so.conf.d/xui-one-libssl.conf
    ldconfig

    # Patch 2 — systemd service unit
    log_progress "Patch 2/5: systemd service"
    cat > /etc/systemd/system/xui-one.service <<SERVICE
[Unit]
Description=XUI.ONE IPTV Streaming Panel
After=network.target mariadb.service
Wants=mariadb.service

[Service]
Type=forking
ExecStart=/bin/bash /home/xui/content/start.sh
ExecStop=/bin/bash /home/xui/content/stop.sh
Restart=on-failure
RestartSec=10
User=root
LimitNOFILE=655350

[Install]
WantedBy=multi-user.target
SERVICE
    systemctl daemon-reload
    systemctl enable xui-one.service >> "$INSTALL_LOG" 2>&1
    systemctl start  xui-one.service
    log_success "xui-one.service enabled and started"

    # Patch 3 — file descriptor limits
    log_progress "Patch 3/5: file descriptor limits"
    grep -q '655350' /etc/security/limits.conf || {
        echo '* soft nofile 655350' >> /etc/security/limits.conf
        echo '* hard nofile 655350' >> /etc/security/limits.conf
    }
    sed -i 's/^#*DefaultLimitNOFILE=.*/DefaultLimitNOFILE=655350/' /etc/systemd/system.conf
    systemctl daemon-reexec >> "$INSTALL_LOG" 2>&1

    # Patch 4 — GeoIP database
    log_progress "Patch 4/5: GeoIP database"
    if [[ -d /home/xui/content ]]; then
        chattr -i /home/xui/content/GeoLite2.mmdb 2>/dev/null || true
        wget -q "https://xtream-masters.com/guide/resources.php?file=xui/GeoLite2.mmdb" \
            -O /home/xui/content/GeoLite2.mmdb >> "$INSTALL_LOG" 2>&1 \
            && chattr +i /home/xui/content/GeoLite2.mmdb 2>/dev/null || true
        log_success "GeoIP database updated"
    else
        log_warning "GeoIP patch skipped — /home/xui/content not found"
    fi

    # Patch 5 — FFmpeg segfault fix
    log_progress "Patch 5/5: FFmpeg"
    apt-get install -y ffmpeg >> "$INSTALL_LOG" 2>&1
    if [[ -d /home/xui/content/bin/ffmpeg ]]; then
        mv /home/xui/content/bin/ffmpeg/ffmpeg \
           /home/xui/content/bin/ffmpeg/ffmpeg.bak 2>/dev/null || true
        ln -sf "$(which ffmpeg)" /home/xui/content/bin/ffmpeg/ffmpeg
        log_success "FFmpeg symlink patched"
    fi

    # ── 7. Verify ─────────────────────────────────────────────────────────────
    # Give XUI.ONE a grace period to bind its port, then treat a persistent
    # failure as a real error so run_step stops instead of marking this "done".
    local xui_ok=0 attempt
    for attempt in 1 2 3 4 5 6; do
        if ss -tlnp | grep -q ":${IPTV_PANEL_PORT}"; then
            xui_ok=1
            break
        fi
        sleep 5
    done
    if [[ "$xui_ok" -eq 1 ]]; then
        log_success "XUI.ONE is listening on port ${IPTV_PANEL_PORT}"
    else
        log_error "XUI.ONE did not open port ${IPTV_PANEL_PORT} after ~30s — install did not complete"
        log_error "Check: systemctl status xui-one  &&  journalctl -u xui-one --no-pager | tail -n 40"
        return 1
    fi

    # Save XUI.ONE DB credentials alongside app credentials
    printf '\n# XUI.ONE Database\nXUI_DB_NAME=xui_one\nXUI_DB_USER=xui_user\nXUI_DB_PASS=%s\n' \
        "$XUI_DB_PASS" >> "$CREDENTIALS_FILE"

    echo ""
    echo -e "${MAGENTA}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
    echo -e "${GREEN}  XUI.ONE installation complete${NC}"
    echo ""
    echo -e "  Admin panel : ${CYAN}http://${IP}:${IPTV_PANEL_PORT}${NC}"
    echo -e "  Stream URL  : ${CYAN}http://stream.${CANONICAL_DOMAIN}:${XUI_CLIENT_PORT}${NC}"
    echo -e "  DB password : ${CYAN}${XUI_DB_PASS}${NC}  (also saved to ${CREDENTIALS_FILE})"
    echo ""
    echo -e "${YELLOW}  To connect web panel → XUI.ONE:${NC}"
    echo -e "  1. Open  : ${CYAN}${SITE_URL}/admin${NC} → Settings → IPTV"
    echo -e "  2. Set   : ${CYAN}xtream_base_url = http://host.docker.internal:${IPTV_PANEL_PORT}${NC}"
    echo -e "     ${YELLOW}(host.docker.internal — NOT localhost — app runs inside Docker)${NC}"
    echo -e "  3. Enter : your XUI.ONE admin credentials"
    echo -e "  4. Set   : ${CYAN}iptv_provisioning_enabled = 1${NC}"
    echo -e "${MAGENTA}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
    echo ""
    echo -en "${CYAN}>>> Press Enter to continue with remaining setup steps: ${NC}"
    read -r
}

# ==========================================================================
# STEP 11 — Firewall (UFW)
# ==========================================================================

configure_firewall() {
    log_header "CONFIGURING UFW FIREWALL"

    ufw --force reset >> "$INSTALL_LOG" 2>&1
    ufw default deny incoming
    ufw default allow outgoing

    # ── Allow Docker container egress (CRITICAL) ─────────────────────────────
    # Outbound traffic from containers (Stripe, 8K CMS, Let's Encrypt, SMTP, …)
    # is ROUTED through the host and traverses the netfilter FORWARD chain — NOT
    # the OUTPUT chain that "ufw default allow outgoing" governs. UFW ships with
    # DEFAULT_FORWARD_POLICY="DROP", which silently drops every packet a
    # container sends to the internet. The symptom is calls to api.stripe.com
    # timing out (curl errno 28 "Connection timed out") even though the host
    # itself has working internet. Set the forward policy to ACCEPT before
    # enabling UFW; Docker's own DOCKER-USER rules still isolate the containers.
    sed -i 's/^DEFAULT_FORWARD_POLICY=.*/DEFAULT_FORWARD_POLICY="ACCEPT"/' /etc/default/ufw
    log_info "  DEFAULT_FORWARD_POLICY set to ACCEPT (Docker container egress)"

    ufw allow ssh
    ufw allow 'Nginx Full'
    ufw allow "${IPTV_PANEL_PORT}/tcp"  comment 'XUI.ONE admin panel'
    ufw allow "${XUI_CLIENT_PORT}/tcp"  comment 'XUI.ONE client streaming port'
    ufw allow "25461/tcp"               comment 'XUI.ONE internal management'
    ufw --force enable

    # Re-apply Docker's iptables/NAT rules. `ufw reset` + `ufw enable` rebuild
    # the netfilter ruleset and can wipe the FORWARD/MASQUERADE chains Docker
    # installed at boot, which also breaks container egress. Restarting the
    # daemon re-inserts them on top of the freshly-enabled UFW ruleset.
    systemctl restart docker >> "$INSTALL_LOG" 2>&1 || true
    log_info "  Docker restarted to restore NAT rules after UFW enable"

    log_success "Firewall enabled"
    log_info   "  Open ports:"
    log_info   "    22    (SSH)"
    log_info   "    80    (HTTP  → HTTPS redirect via Nginx)"
    log_info   "    443   (HTTPS → Docker web container via Nginx)"
    log_info   "    ${IPTV_PANEL_PORT}   (XUI.ONE admin panel)"
    log_info   "    ${XUI_CLIENT_PORT}   (XUI.ONE client streaming)"
    log_info   "    25461 (XUI.ONE internal management)"
    log_info   "  Blocked: 8081 (Docker internal only), 3306 (DB internal only)"
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
        "http://localhost:${IPTV_PANEL_PORT}" 2>/dev/null || echo 0)
    if [[ "$code" =~ ^(200|301|302|403)$ ]]; then
        log_success "XUI Xtream responding on :${IPTV_PANEL_PORT} (HTTP $code)"
    else
        log_warning "XUI Xtream not responding on :${IPTV_PANEL_PORT} (install manually if skipped)"
    fi
}

# ==========================================================================
# Final Summary
# ==========================================================================

show_summary() {
    local IP
    IP=$(curl -s -4 --max-time 8 ifconfig.me 2>/dev/null || echo "YOUR_VPS_IP")

    log_highlight "${APP_DISPLAY_NAME} — INSTALLATION COMPLETE"

    echo -e "${YELLOW}WEB PANEL${NC}"
    echo -e "  URL         : ${CYAN}${SITE_URL}${NC}"
    echo -e "  Admin panel : ${CYAN}${SITE_URL}/admin${NC}"
    echo -e "  Admin email : ${CYAN}${ADMIN_EMAIL}${NC}"
    echo -e "  Admin pass  : ${CYAN}${ADMIN_PASSWORD}${NC}"
    echo ""

    echo -e "${YELLOW}IPTV STREAMING SERVER (XUI.ONE 1.5.13)${NC}"
    echo -e "  Admin URL   : ${CYAN}http://${IP}:${IPTV_PANEL_PORT}${NC}"
    echo -e "  Stream URL  : ${CYAN}http://stream.${CANONICAL_DOMAIN}:${XUI_CLIENT_PORT}${NC}"
    echo -e "  Admin port  : ${IPTV_PANEL_PORT}  (XUI.ONE panel)"
    echo -e "  Client port : ${XUI_CLIENT_PORT}  (customers use this — Xtream Codes API)"
    echo -e "  (Use the stream URL when customers configure their IPTV apps)"
    echo ""

    echo -e "${YELLOW}DATABASE${NC}"
    echo -e "  DB name  : $DB_NAME"
    echo -e "  DB user  : $DB_USERNAME"
    echo -e "  DB pass  : $DB_PASSWORD"
    echo -e "  Root PW  : $MYSQL_ROOT_PASSWORD"
    echo ""

    echo -e "${YELLOW}STEP A — CONNECT WEB PANEL TO IPTV SERVER${NC}"
    echo -e "  1. Open Admin Panel → Settings → IPTV"
    echo -e "  2. Set ${CYAN}xtream_base_url = http://host.docker.internal:${IPTV_PANEL_PORT}${NC}"
    echo -e "     ${YELLOW}Important: use 'host.docker.internal', NOT 'localhost'${NC}"
    echo -e "     ${YELLOW}(The web app runs inside Docker; localhost = container, not VPS)${NC}"
    echo -e "  3. Enter your XUI Xtream admin username and password"
    echo -e "  4. Set  ${CYAN}iptv_provisioning_enabled = 1${NC}"
    echo -e "  5. Click Test Connection → should return 'Connection successful'"
    echo ""

    echo -e "${YELLOW}STEP B — CONFIGURE SMTP (EMAIL)${NC}"
    echo -e "  Admin Panel → Settings → SMTP Settings"
    echo -e "  Recommended: Mailgun (free up to 1,000 emails/month)"
    echo -e "  Until configured, all emails are logged to storage/logs/laravel.log"
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

    echo -e "${YELLOW}STEP D — LOAD IPTV CONTENT${NC}"
    echo -e "  XUI Xtream Admin → Bouquets → Add Source"
    echo -e "  Test content (dev only): https://iptv-org.github.io/iptv/index.m3u"
    echo ""

    echo -e "${YELLOW}STEP E — TEST END-TO-END${NC}"
    echo -e "  1. Use Stripe test card: ${CYAN}4242 4242 4242 4242${NC} (any date, any CVC)"
    echo -e "  2. Register a customer and complete a subscription purchase"
    echo -e "  3. Verify: XUI Xtream → Lines → new line created"
    echo -e "  4. Verify: customer welcome email with Xtream Codes login"
    echo -e "  5. Test IPTV app: XCIPTV / IPTV Smarters → Xtream Codes API login"
    echo -e "     Server: ${CYAN}http://stream.${CANONICAL_DOMAIN}:${XUI_CLIENT_PORT}${NC}"
    echo ""

    echo -e "${YELLOW}DOCKER MANAGEMENT${NC}"
    echo -e "  Status  : ${CYAN}cd ${APP_DIR} && docker compose ps${NC}"
    echo -e "  App logs: ${CYAN}docker compose logs -f app${NC}"
    echo -e "  Worker  : ${CYAN}docker compose logs -f worker${NC}"
    echo -e "  Restart : ${CYAN}docker compose restart app web worker${NC}"
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
    echo -e "  Scheduler log: ${CYAN}/var/log/laravel-scheduler.log${NC}"
    echo ""

    echo -e "${RED}${BOLD}SAVE THESE CREDENTIALS — shown above and stored in ${CREDENTIALS_FILE}${NC}"
}

# ==========================================================================
# Help
# ==========================================================================

show_help() {
    echo ""
    echo -e "${CYAN}${APP_DISPLAY_NAME} — Automated VPS Deployment${NC}"
    echo ""
    echo -e "${YELLOW}Usage:${NC}"
    echo "  sudo ./autosetup.sh [--domain yourdomain.com]"
    echo ""
    echo -e "${YELLOW}Prerequisites:${NC}"
    echo "  • Ubuntu 22.04 or 24.04 LTS VPS (fresh install)"
    echo "  • Minimum: 4 vCPU / 8 GB RAM / 100 GB SSD"
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
    echo "  7. XUI.ONE 1.5.13 IPTV panel (admin :$IPTV_PANEL_PORT  streams :$XUI_CLIENT_PORT)"
    echo "  8. UFW firewall"
    echo "  9. Laravel scheduler cron"
    echo ""
    echo -e "${YELLOW}Port layout:${NC}"
    echo "  443  HTTPS (web panel, proxied by Nginx)"
    echo "  80   HTTP → HTTPS redirect"
    echo "  8080 XUI Xtream IPTV panel (host-level, public)"
    echo "  8081 Docker web container (localhost only, Nginx proxy target)"
    echo "  3306 MySQL (localhost only, never public)"
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

    log_highlight "${APP_DISPLAY_NAME} — AUTOMATED VPS SETUP"

    parse_args "$@"
    check_root
    check_ubuntu
    check_disk 25

    if check_resume; then
        verify_project_files
    else
        verify_project_files
        generate_credentials
        [[ -z "$DOMAIN" ]] && get_domain
    fi

    echo ""
    echo -e "${MAGENTA}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
    echo -e "${MAGENTA}  INSTALLATION PLAN${NC}"
    echo -e "${MAGENTA}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
    echo ""
    echo -e "  Domain    : ${CYAN}${CANONICAL_DOMAIN}${NC}"
    echo -e "  Web panel : ${CYAN}${SITE_URL}${NC}"
    echo -e "  Admin     : ${CYAN}${ADMIN_EMAIL}${NC}"
    echo -e "  IPTV      : ${CYAN}http://stream.${CANONICAL_DOMAIN}:${XUI_CLIENT_PORT}${NC}"
    echo ""
    echo -e "  Steps:"
    echo -e "    01. System packages + Node.js ${NODE_VERSION} LTS"
    echo -e "    02. Docker CE + Compose plugin"
    echo -e "    03. Host Nginx install + configure"
    echo -e "    04. Let's Encrypt SSL"
    echo -e "    05. Frontend build  (npm ci + npm run build)"
    echo -e "    06. Docker Compose override  (DB credentials)"
    echo -e "    07. Laravel .env  (production)"
    echo -e "    08. Build + start containers  (PHP 8.4, MySQL 8, worker)"
    echo -e "    09. Laravel init  (composer, migrate, seed, admin user)"
    echo -e "    10. XUI.ONE 1.5.13 IPTV panel  (ports ${IPTV_PANEL_PORT} admin / ${XUI_CLIENT_PORT} streams)"
    echo -e "    11. UFW firewall"
    echo -e "    12. Scheduler cron"
    echo -e "    13. Production cache  (config, routes, views)"
    echo -e "    14. Verification"
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
    run_step "11_xui_one"            install_xui_one
    run_step "12_firewall"           configure_firewall
    run_step "13_cron"               configure_cron
    run_step "14_optimize"           optimize_production
    run_step "15_verify"             verify_install

    # Remove state file only on full success
    rm -f "$STATE_FILE"

    show_summary
    echo "[$(_ts)] Installation completed successfully" >> "$INSTALL_LOG"
    exit 0
}

main "$@"
