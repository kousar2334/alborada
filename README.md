# Moissanite Radiance — IPTV Subscription Platform

A multi-tenant IPTV subscription management platform built on **Laravel 12**. It handles customer subscriptions, reseller management, automatic IPTV provisioning via Xtream Codes, and payment processing through Stripe, SSL Commerce, and bank transfer.

---

## Documentation

**[Full System Documentation →](SYSTEM_DOCUMENTATION.md)**

The documentation covers:

- [System Overview](SYSTEM_DOCUMENTATION.md#1-system-overview) — architecture diagram and platform purpose
- [User Roles](SYSTEM_DOCUMENTATION.md#3-user-roles) — Admin, Customer, Reseller
- [Authentication & Authorization](SYSTEM_DOCUMENTATION.md#4-authentication--authorization) — session auth, Sanctum API tokens, OAuth
- [Subscription & Payment Flow](SYSTEM_DOCUMENTATION.md#5-subscription--payment-flow) — Stripe, bank transfer, SSL Commerce, invoice generation
- [IPTV Provisioning](SYSTEM_DOCUMENTATION.md#6-iptv-provisioning) — Xtream Codes integration, account lifecycle
- [Reseller System](SYSTEM_DOCUMENTATION.md#7-reseller-system) — credit-based billing, client management, markup
- [Content Management](SYSTEM_DOCUMENTATION.md#8-content-management) — blog, pages, homepage builder, featured content
- [Support Ticket System](SYSTEM_DOCUMENTATION.md#9-support-ticket-system) — ticket lifecycle and statuses
- [Admin Panel](SYSTEM_DOCUMENTATION.md#10-admin-panel) — all admin modules and settings
- [REST API](SYSTEM_DOCUMENTATION.md#11-rest-api-reseller-api) — Reseller API endpoints
- [Email Notifications](SYSTEM_DOCUMENTATION.md#12-email-notifications) — all triggered emails
- [Database Schema](SYSTEM_DOCUMENTATION.md#13-database-schema) — key table structures
- [Services](SYSTEM_DOCUMENTATION.md#14-services) — StripeService, IptvProvisioningService, XtreamCodesService, WhmcsService, InvoiceService
- [Route Map](SYSTEM_DOCUMENTATION.md#15-route-map) — all public, member, reseller, admin, and API routes
- [Configuration & Environment](SYSTEM_DOCUMENTATION.md#16-configuration--environment) — required `.env` variables
- [Installation & Setup](SYSTEM_DOCUMENTATION.md#17-installation--setup) — step-by-step setup guide
- [Technology Stack](SYSTEM_DOCUMENTATION.md#18-technology-stack) — full dependency list

---

## Quick Start

```bash
# Install dependencies
composer install && npm install && npm run build

# Configure environment
cp .env.example .env && php artisan key:generate

# Run migrations
php artisan migrate --seed

# Link storage
php artisan storage:link

# Start development server
php artisan serve
```

See [Installation & Setup](SYSTEM_DOCUMENTATION.md#17-installation--setup) for full production deployment instructions.

---

## Tech Stack

| | |
|---|---|
| **Backend** | Laravel 12, PHP 8.2 |
| **Frontend** | Blade, Tailwind CSS 4, Vite |
| **Auth** | Sanctum (API), Socialite (OAuth), Spatie Permissions |
| **Payments** | Stripe, SSL Commerce, Bank Transfer |
| **IPTV** | Xtream Codes REST API |
| **PDF** | DomPDF |
| **Queue** | Database |

---

## License

MIT
