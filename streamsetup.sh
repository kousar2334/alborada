#!/bin/bash
# ==========================================================================
# Moissanite Visions — STREAMING SERVER Setup
# XUI.ONE 1.5.13 IPTV Streaming Panel (dedicated VPS)
# ==========================================================================
# Compatible : Ubuntu 22.04 LTS ONLY (XUI.ONE requirement — fresh install)
# Run as     : sudo ./streamsetup.sh [--domain yourdomain.com]
#
# This script sets up the STREAMING server only. The Laravel web panel
# is installed on a SEPARATE VPS with websetup.sh.
#
# Port layout after installation:
#   :8080  → XUI.ONE admin panel (public)
#   :2086  → client streaming port (customers' IPTV apps connect here)
#   :25461 → XUI.ONE internal management port
#   :3306  → MariaDB (localhost only, never public)
#
# The web panel connects to this server via:
#   xtream_base_url = http://THIS_SERVER_IP:8080
#   (set in the web panel: Admin → Settings → IPTV)
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
INSTALL_LOG="/var/log/alborada-stream-install.log"
LOG_MAX_BYTES=10485760          # 10 MB — rotate when exceeded
STATE_FILE="/root/.alborada_stream_install_state"
CREDENTIALS_FILE="/root/.alborada_stream_credentials"

APP_DISPLAY_NAME="Moissanite Visions"

IPTV_PANEL_PORT=8080            # XUI.ONE admin panel
XUI_CLIENT_PORT=2086            # customer streaming port (Xtream Codes API)

# XUI.ONE installer source. XUI.ONE is a LICENSED commercial panel — the
# installer URL is tied to your purchase and the vendor rotates these paths.
# Override without editing this file:  XUI_INSTALLER_URL="https://..." ./streamsetup.sh
# Space-separated fallbacks are tried in order until one returns a real script.
XUI_INSTALLER_URL="${XUI_INSTALLER_URL:-}"
XUI_INSTALLER_URL_CANDIDATES=(
    "https://xtream-masters.com/guide/resources.php?file=xui-one/install.sh"
    "https://tut.xtream-masters.com/files/xui-one/install.sh"
)

# ── Runtime state ──────────────────────────────────────────────────────────
DOMAIN=""
CANONICAL_DOMAIN=""

XUI_DB_PASS=""

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
        echo "  ALBORADA BOX — STREAMING SERVER INSTALLATION LOG"
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
    XUI_DB_PASS=$(generate_password 24)
    save_credentials
    log_success "Credentials written to $CREDENTIALS_FILE (chmod 600)"
}

save_credentials() {
    printf '# Moissanite Visions Streaming Server Credentials — %s\n# XUI.ONE Database\nXUI_DB_NAME=xui_one\nXUI_DB_USER=xui_user\nXUI_DB_PASS=%s\n' \
        "$(date)" "$XUI_DB_PASS" \
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
        echo "# Moissanite Visions Streaming Install State — $(date)"
        echo "DOMAIN=$DOMAIN"
        echo "CANONICAL_DOMAIN=$CANONICAL_DOMAIN"
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

    d="${d#www.}"
    CANONICAL_DOMAIN="$d"
    DOMAIN="$d"
    return 0
}

get_domain() {
    echo ""
    echo -e "${MAGENTA}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
    echo -e "${MAGENTA}  DOMAIN CONFIGURATION${NC}"
    echo -e "${MAGENTA}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
    echo ""
    echo -e "${YELLOW}  Enter your MAIN domain (the one the web panel uses).${NC}"
    echo -e "${YELLOW}  Customers will stream from: http://stream.YOURDOMAIN:${XUI_CLIENT_PORT}${NC}"
    echo -e "${GREEN}  Example:${NC} ${CYAN}alboradabox.com${NC}"
    echo ""
    while true; do
        echo -en "${CYAN}${BOLD}>>> Domain name: ${NC}"
        read -r _input
        validate_domain "$_input" && break
        log_warning "Please try again with a valid domain name"
    done
    echo ""
    log_success "Domain set: $CANONICAL_DOMAIN"
    log_info    "  IPTV admin   : http://THIS_SERVER_IP:${IPTV_PANEL_PORT}"
    log_info    "  IPTV streams : http://stream.${CANONICAL_DOMAIN}:${XUI_CLIENT_PORT}"
    echo ""
}

# ==========================================================================
# Preflight Checks
# ==========================================================================

check_root() {
    [[ $EUID -eq 0 ]] || { log_error "Must run as root.  Try: sudo ./streamsetup.sh"; exit 1; }
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
        log_warning "Ubuntu 24.04 detected — XUI.ONE installer is NOT compatible with 24.04"
        log_warning "XUI.ONE step will likely fail. Downgrade to Ubuntu 22.04 LTS is strongly recommended."
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

# ==========================================================================
# STEP 01 — System Packages
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
        ca-certificates git openssl unzip zip lsb-release file \
        ufw fail2ban >> "$INSTALL_LOG" 2>&1
    log_success "Essential system packages installed"
}

# ==========================================================================
# STEP 02 — XUI.ONE 1.5.13 IPTV Streaming Panel (Ubuntu 22.04)
#
# Source: https://xtream-masters.com/guide/how_to_install_xui_one_ubuntu_22_04.php
#
# Installed directly on the host (no Docker on this server).
# Port layout:
#   :8080  — XUI.ONE admin panel
#   :2086  — client streaming port (customers use this)
#   :25461 — internal XUI.ONE management port
#
# MariaDB is installed on the host exclusively for XUI.ONE.
# ==========================================================================

install_xui_one() {
    log_header "INSTALLING XUI.ONE 1.5.13 — IPTV STREAMING PANEL"

    local IP
    IP=$(curl -s -4 --max-time 8 ifconfig.me 2>/dev/null || echo "YOUR_VPS_IP")

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
    # The firewall step re-enables it with the correct rules.
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

    # ── 4. MariaDB (host-level) ────────────────────────────────────────────────
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
        log_error "Get the current installer link from your XUI.ONE account/license,"
        log_error "then re-run (resumes from this step):"
        log_error "    XUI_INSTALLER_URL=\"https://your-installer-url/install.sh\" $0"
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
}

# ==========================================================================
# STEP 03 — Firewall (UFW)
# ==========================================================================

configure_firewall() {
    log_header "CONFIGURING UFW FIREWALL"

    ufw --force reset >> "$INSTALL_LOG" 2>&1
    ufw default deny incoming
    ufw default allow outgoing
    ufw allow ssh
    ufw allow "${IPTV_PANEL_PORT}/tcp"  comment 'XUI.ONE admin panel'
    ufw allow "${XUI_CLIENT_PORT}/tcp"  comment 'XUI.ONE client streaming port'
    ufw allow "25461/tcp"               comment 'XUI.ONE internal management'
    ufw --force enable

    log_success "Firewall enabled"
    log_info   "  Open ports:"
    log_info   "    22    (SSH)"
    log_info   "    ${IPTV_PANEL_PORT}  (XUI.ONE admin panel)"
    log_info   "    ${XUI_CLIENT_PORT}  (XUI.ONE client streaming)"
    log_info   "    25461 (XUI.ONE internal management)"
    log_info   "  Blocked: 3306 (MariaDB — localhost only)"
    ufw status >> "$INSTALL_LOG" 2>&1
}

# ==========================================================================
# STEP 04 — Verify Installation
# ==========================================================================

verify_install() {
    log_header "VERIFYING INSTALLATION"

    _chk() {
        local label="$1"
        shift
        if "$@" >/dev/null 2>&1; then
            log_success "$label"
        else
            log_warning "$label — NOT OK"
        fi
    }

    _chk "MariaDB running"           systemctl is-active --quiet mariadb
    _chk "xui-one.service running"   systemctl is-active --quiet xui-one
    _chk "UFW firewall active"       ufw status | grep -q "Status: active"

    local code
    code=$(curl -s -o /dev/null -w "%{http_code}" --max-time 8 \
        "http://localhost:${IPTV_PANEL_PORT}" 2>/dev/null || echo 0)
    if [[ "$code" =~ ^(200|301|302|403)$ ]]; then
        log_success "XUI.ONE responding on :${IPTV_PANEL_PORT} (HTTP $code)"
    else
        log_warning "XUI.ONE not responding on :${IPTV_PANEL_PORT} (HTTP $code)"
    fi
}

# ==========================================================================
# Final Summary
# ==========================================================================

show_summary() {
    local IP
    IP=$(curl -s -4 --max-time 8 ifconfig.me 2>/dev/null || echo "YOUR_VPS_IP")

    log_highlight "${APP_DISPLAY_NAME} — STREAMING SERVER INSTALLATION COMPLETE"

    echo -e "${YELLOW}IPTV STREAMING SERVER (XUI.ONE 1.5.13)${NC}"
    echo -e "  Admin URL   : ${CYAN}http://${IP}:${IPTV_PANEL_PORT}${NC}"
    echo -e "  Stream URL  : ${CYAN}http://stream.${CANONICAL_DOMAIN}:${XUI_CLIENT_PORT}${NC}"
    echo -e "  Admin port  : ${IPTV_PANEL_PORT}  (XUI.ONE panel)"
    echo -e "  Client port : ${XUI_CLIENT_PORT}  (customers use this — Xtream Codes API)"
    echo -e "  (Use the stream URL when customers configure their IPTV apps)"
    echo ""

    echo -e "${YELLOW}XUI.ONE DATABASE${NC}"
    echo -e "  DB name  : xui_one"
    echo -e "  DB user  : xui_user"
    echo -e "  DB pass  : ${XUI_DB_PASS}"
    echo ""

    echo -e "${YELLOW}STEP A — DNS RECORD${NC}"
    echo -e "  Add in your DNS provider (DNS-only / grey cloud in Cloudflare):"
    echo -e "    A    stream    ${CYAN}${IP}${NC}"
    echo ""

    echo -e "${YELLOW}STEP B — CONNECT THE WEB PANEL TO THIS SERVER${NC}"
    echo -e "  On the WEB server's admin panel:"
    echo -e "  1. Open  : ${CYAN}https://${CANONICAL_DOMAIN}/admin${NC} → Settings → IPTV"
    echo -e "  2. Set   : ${CYAN}xtream_base_url = http://${IP}:${IPTV_PANEL_PORT}${NC}"
    echo -e "  3. Enter : your XUI.ONE admin credentials (set during install)"
    echo -e "  4. Set   : ${CYAN}iptv_provisioning_enabled = 1${NC}"
    echo -e "  5. Click Test Connection → should return 'Connection successful'"
    echo ""

    echo -e "${YELLOW}STEP C — LOAD IPTV CONTENT${NC}"
    echo -e "  XUI.ONE Admin → Bouquets → Add Source"
    echo -e "  Test content (dev only): https://iptv-org.github.io/iptv/index.m3u"
    echo -e "  Production: purchase reseller credits and connect their Xtream Codes API"
    echo ""

    echo -e "${YELLOW}MANAGEMENT${NC}"
    echo -e "  Status  : ${CYAN}systemctl status xui-one${NC}"
    echo -e "  Restart : ${CYAN}systemctl restart xui-one${NC}"
    echo -e "  Logs    : ${CYAN}journalctl -u xui-one -f${NC}"
    echo -e "  MariaDB : ${CYAN}systemctl status mariadb${NC}"
    echo ""

    echo -e "${YELLOW}FILES${NC}"
    echo -e "  Install log : ${CYAN}${INSTALL_LOG}${NC}"
    echo -e "  Credentials : ${CYAN}${CREDENTIALS_FILE}${NC}  (chmod 600)"
    echo ""

    echo -e "${RED}${BOLD}SAVE THESE CREDENTIALS — shown above and stored in ${CREDENTIALS_FILE}${NC}"
}

# ==========================================================================
# Help
# ==========================================================================

show_help() {
    echo ""
    echo -e "${CYAN}${APP_DISPLAY_NAME} — Streaming Server Deployment${NC}"
    echo ""
    echo -e "${YELLOW}Usage:${NC}"
    echo "  sudo ./streamsetup.sh [--domain yourdomain.com]"
    echo ""
    echo -e "${YELLOW}This script sets up the IPTV STREAMING server only.${NC}"
    echo "  The Laravel web panel is installed on a SEPARATE VPS with"
    echo "  websetup.sh — see install-web.txt."
    echo ""
    echo -e "${YELLOW}Prerequisites:${NC}"
    echo "  • Ubuntu 22.04 LTS VPS (fresh install — XUI.ONE requirement)"
    echo "  • Minimum: 4 vCPU / 8 GB RAM / 100 GB SSD / 1 Gbps port"
    echo "  • A valid XUI.ONE license"
    echo ""
    echo -e "${YELLOW}What this script installs:${NC}"
    echo "  1. System packages"
    echo "  2. XUI.ONE 1.5.13 IPTV panel (libssl1.1, Python 2, MariaDB, patches)"
    echo "  3. UFW firewall"
    echo "  4. Verification"
    echo ""
    echo -e "${YELLOW}Port layout:${NC}"
    echo "  8080  XUI.ONE admin panel (public)"
    echo "  2086  Client streaming port (customers' IPTV apps)"
    echo "  25461 XUI.ONE internal management"
    echo "  3306  MariaDB (localhost only, never public)"
    echo ""
    echo -e "${YELLOW}Environment overrides:${NC}"
    echo "  XUI_INSTALLER_URL=\"https://.../install.sh\"  — use a specific installer URL"
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

    log_highlight "${APP_DISPLAY_NAME} — STREAMING SERVER SETUP"

    parse_args "$@"
    check_root
    check_ubuntu
    check_disk 25

    if ! check_resume; then
        generate_credentials
        [[ -z "$DOMAIN" ]] && get_domain
    fi

    local IP
    IP=$(curl -s -4 --max-time 8 ifconfig.me 2>/dev/null || echo "YOUR_VPS_IP")

    echo ""
    echo -e "${MAGENTA}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
    echo -e "${MAGENTA}  INSTALLATION PLAN — STREAMING SERVER${NC}"
    echo -e "${MAGENTA}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
    echo ""
    echo -e "  Domain      : ${CYAN}${CANONICAL_DOMAIN}${NC}"
    echo -e "  IPTV admin  : ${CYAN}http://${IP}:${IPTV_PANEL_PORT}${NC}"
    echo -e "  IPTV streams: ${CYAN}http://stream.${CANONICAL_DOMAIN}:${XUI_CLIENT_PORT}${NC}"
    echo ""
    echo -e "  Steps:"
    echo -e "    01. System packages"
    echo -e "    02. XUI.ONE 1.5.13 IPTV panel  (ports ${IPTV_PANEL_PORT} admin / ${XUI_CLIENT_PORT} streams)"
    echo -e "    03. UFW firewall"
    echo -e "    04. Verification"
    echo ""
    echo -e "  ${YELLOW}The Laravel web panel is installed separately with websetup.sh${NC}"
    echo ""
    echo -e "${MAGENTA}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
    echo -en "${CYAN}${BOLD}>>> Proceed with installation? (y/N): ${NC}"
    read -n 1 -r REPLY; echo ""
    [[ $REPLY =~ ^[Yy]$ ]] || { log_warning "Installation cancelled"; exit 0; }
    echo ""

    run_step "01_system_packages"    install_system_packages
    run_step "02_xui_one"            install_xui_one
    run_step "03_firewall"           configure_firewall
    run_step "04_verify"             verify_install

    # Remove state file only on full success
    rm -f "$STATE_FILE"

    show_summary
    echo "[$(_ts)] Streaming server installation completed successfully" >> "$INSTALL_LOG"
    exit 0
}

main "$@"
