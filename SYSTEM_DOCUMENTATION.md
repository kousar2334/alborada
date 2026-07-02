# Moissanite Radiance — System Documentation

> A comprehensive multi-tenant IPTV subscription platform built on Laravel 12.

---

## Table of Contents

1. [System Overview](#1-system-overview)
2. [Architecture](#2-architecture)
3. [User Roles](#3-user-roles)
4. [Authentication & Authorization](#4-authentication--authorization)
5. [Subscription & Payment Flow](#5-subscription--payment-flow)
6. [IPTV Provisioning](#6-iptv-provisioning)
7. [Reseller System](#7-reseller-system)
8. [Content Management](#8-content-management)
9. [Support Ticket System](#9-support-ticket-system)
10. [Admin Panel](#10-admin-panel)
11. [REST API (Reseller API)](#11-rest-api-reseller-api)
12. [Email Notifications](#12-email-notifications)
13. [Database Schema](#13-database-schema)
14. [Services](#14-services)
15. [Route Map](#15-route-map)
16. [Configuration & Environment](#16-configuration--environment)
17. [Installation & Setup](#17-installation--setup)
18. [Technology Stack](#18-technology-stack)

---

## 1. System Overview

Moissanite Radiance is a **white-label IPTV subscription management platform**. It allows a business to:

- Sell IPTV streaming subscriptions directly to customers.
- Onboard resellers who buy credits and resell subscriptions under their own brand.
- Automatically provision IPTV accounts on an Xtream Codes panel upon payment approval.
- Manage billing through Stripe, SSL Commerce, or manual bank transfer.
- Generate invoices, handle support tickets, and produce business reports — all from a single admin dashboard.

```
                         ┌──────────────────┐
                         │   Public Website  │
                         │  (Home / Blog /   │
                         │   Pricing / CTA)  │
                         └────────┬─────────┘
                                  │
               ┌──────────────────┼──────────────────┐
               ▼                  ▼                   ▼
        ┌────────────┐   ┌─────────────────┐  ┌───────────────┐
        │  Customer  │   │    Reseller     │  │  Admin Panel  │
        │   Portal   │   │     Portal      │  │               │
        └─────┬──────┘   └────────┬────────┘  └───────┬───────┘
              │                   │                    │
              ▼                   ▼                    ▼
     ┌──────────────────────────────────────────────────────────┐
     │                     Laravel Backend                       │
     │   Controllers · Services · Models · Jobs · Mail           │
     └──────────────┬──────────────────────────┬────────────────┘
                    │                          │
          ┌─────────▼──────────┐    ┌──────────▼──────────┐
          │  Xtream Codes IPTV │    │  Stripe / SSLCommerz │
          │       Panel        │    │    Payment Gateways  │
          └────────────────────┘    └─────────────────────┘
```

---

## 2. Architecture

The project follows a standard **Laravel MVC** structure with an additional service layer for complex business logic.

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Backend/       # Admin panel controllers
│   │   ├── Frontend/      # Customer & reseller controllers
│   │   └── Api/           # Reseller REST API controllers
│   ├── Middleware/        # IsAdmin, IsCustomer, IsReseller, LogApiRequest
│   └── Requests/          # Form request validation
├── Models/                # Eloquent models
├── Services/              # Business logic
│   ├── StripeService.php
│   ├── IptvProvisioningService.php
│   ├── XtreamCodesService.php
│   ├── WhmcsService.php
│   └── InvoiceService.php
├── Mail/                  # Mailable classes
├── Jobs/                  # Queued jobs
├── Notifications/         # In-app notifications
├── Repository/            # Data access layer
├── Helpers/               # Global helper functions
└── View/Components/       # Blade UI components

routes/
├── web.php                # Public + member routes
├── admin.php              # Admin panel routes
├── api.php                # Reseller REST API routes
└── console.php            # Artisan commands

database/
├── migrations/            # 30+ migration files
├── seeders/               # Database seeders
└── factories/             # Model factories

resources/
├── views/
│   ├── backend/           # Admin Blade templates
│   └── frontend/          # Customer/reseller Blade templates
└── js/                    # Vite/Tailwind frontend assets
```

---

## 3. User Roles

The system defines three user types stored in `users.type`:

| Role | Value | Portal | Description |
|------|-------|--------|-------------|
| **Admin** | `1` | `/admin/*` | Full system access; manages everything |
| **Customer** | `2` | `/member/*` | Purchases and manages their own subscriptions |
| **Reseller** | `3` | `/reseller/*` | Purchases credits, creates and manages client accounts |

Permissions are enforced by:
- **Middleware** (`IsAdmin`, `IsCustomer`, `IsReseller`) on route groups.
- **Spatie Laravel Permission** for granular role-based access control within the admin panel.

---

## 4. Authentication & Authorization

### Customer / Reseller Auth
- Registration → Login → Forgot Password → Reset Password flows.
- Each user type has its own auth controllers (`MemberAuthController`, `ResellerAuthController`).
- Social login supported via **Laravel Socialite** (OAuth providers configurable from admin settings).
- Session-based authentication for web portals.

### Admin Auth
- Separate login route (`/admin/login`) handled by `UserAuthController`.
- Spatie roles and permissions control which admin users can access which modules.

### API Auth
- Reseller API uses **Laravel Sanctum** token-based authentication.
- Tokens are created/revoked from the reseller portal.
- Every API request is logged by the `LogApiRequest` middleware into an `api_logs` table.

---

## 5. Subscription & Payment Flow

### Customer Subscription Journey

```
1. Browse Pricing Plans
        │
        ▼
2. Select Plan → Confirmation Page
        │
        ├──► Free/Trial Plan ─────────────────► Instant Activation
        │
        ├──► Stripe Payment  (fully automated — no admin approval)
        │         │
        │         ├─ Create PaymentIntent
        │         ├─ Customer enters card details
        │         ├─ Payment succeeds → Subscription set "active" by BOTH the
        │         │   Stripe webhook (StripeWebhookController) and the browser
        │         │   return (stripeSuccess); whichever fires first wins
        │         └─ ProvisionSubscriptionJob dispatched automatically
        │             (idempotent — guarded on an existing xtream_line_id, so
        │              webhook + return never double-provision)
        │
        └──► Bank Transfer  (manual review)
                  │
                  ├─ Customer submits reference + uploaded slip on checkout
                  └─ Subscription created as "pending" → admin approves

── Provisioning (runs automatically for Stripe, or on admin approval) ──
IptvProvisioningService::provision()
        ├─ Creates Xtream Codes user account (username / password / line_id)
        ├─ Generates a paid Invoice + PDF (DomPDF) and emails the receipt
        ├─ Two-way WHMCS sync (if enabled): client → order → service → paid invoice
        └─ Sends welcome email with IPTV credentials (M3U + EPG URLs)

Subscription is Active
        └─ Customer accesses credentials from member dashboard
```

> **SSLCommerz:** `config/sslcommerz.php` and the DB columns exist, but the
> gateway is **not wired** in this build. Stripe + bank transfer are the active
> methods; SSLCommerz can be added later for regional (Bangladesh) billing.

### Payment Methods

| Method | Gateway | Configuration |
|--------|---------|---------------|
| Credit/Debit Card | Stripe | Keys in `settings`; auto-provisions on success |
| Bank Transfer | Manual | Customer uploads slip; admin approves in Subscriptions |
| Online Payment | SSL Commerce | Config present, **not wired** in this build |

### Payment Links
Admins can generate **token-based payment links** (`/pay/{token}`) that allow customers to pay for a specific plan without logging in. Links have an expiry date.

### Invoices
- Auto-generated by `IptvProvisioningService` when a subscription is provisioned (Stripe success or admin approval).
- Invoice numbers follow format `INV-YYYY-XXXXX`.
- PDF generated via **DomPDF** and emailed to the customer as a receipt.

---

## 6. IPTV Provisioning

Provisioning is handled by `IptvProvisioningService`, which calls `XtreamCodesService` to interact with the Xtream Codes panel API.

### Provisioning Flow

```
Admin Approves Subscription
        │
        ▼
IptvProvisioningService::provision($subscription)
        │
        ├─ Generate unique xtream_username / xtream_password
        ├─ XtreamCodesService::createUser(...)
        │         └─ POST to Xtream Codes API
        │               ├─ max_connections (from PricingPlan)
        │               ├─ expiry_date (based on plan duration_days)
        │               ├─ streaming quality
        │               └─ catchup_days / DVR settings
        ├─ Save line_id returned by Xtream Codes
        ├─ InvoiceService::generate($subscription)
        │         └─ Create Invoice record + PDF + email
        └─ Send welcome email with credentials (M3U URL, EPG URL)
```

### Account Lifecycle

| Event | Action on Xtream Codes |
|-------|------------------------|
| Subscription approved | Account created |
| Subscription expires | Account banned/suspended |
| Subscription renewed | Expiry date extended |
| Admin re-provisions | Account updated |
| Subscription deleted | Account removed |

### WHMCS Two-Way Sync (Optional)

When WHMCS credentials **and** `whmcs_sync_enabled` are set (Admin → Settings → IPTV),
provisioning drives a full billing lifecycle in WHMCS via `WhmcsService`:

**Outbound (app → WHMCS)** during `IptvProvisioningService`:
1. Ensure a WHMCS **client** (`AddClient` / `UpdateClient`) — stored on `users.whmcs_client_id`.
2. Place an **order** for the configured product (`whmcs_product_id`) via `AddOrder`, then `AcceptOrder` to provision the module (`ModuleCreate`).
3. Mark the generated invoice **paid** via `AddInvoicePayment`.
4. On suspend/expire/delete → `ModuleSuspend` / `ModuleTerminate`; on reactivate → `ModuleUnsuspend`.

The WHMCS `order_id`, `service_id`, and `invoice_id` are stored on `user_subscriptions`
for later lifecycle calls and inbound matching. Set the **IPTV Product ID** to `0` to sync
only the client record (no orders).

**Inbound (WHMCS → app)** via `POST /whmcs/webhook` (`WhmcsWebhookController`):
- Authenticated by an **HMAC-SHA256** of the raw body using `whmcs_webhook_secret`, sent in the `X-WHMCS-Signature` header.
- Body: `{ "event": "invoice_paid|service_suspended|service_unsuspended|service_terminated", "service_id": N, "invoice_id": N }`.
- Matches the local `UserSubscription` by `whmcs_service_id` / `whmcs_invoice_id` and reflects the state (activate + provision on paid, suspend/reactivate/cancel on the service events).
- Configure a WHMCS action hook to POST these events; the WHMCS instance itself is infrastructure outside this repo.

All WHMCS calls are logged to `api_logs`.

---

## 7. Reseller System

Resellers operate a **credit-based** sub-platform within Moissanite Radiance.

### Credit Flow

```
Admin tops up reseller credits
        │
        ▼
Reseller balance increases (users.credits)
        │
        ▼
Reseller creates a client
        │
        ├─ Selects plan for client
        ├─ System deducts plan price × (1 + markup%) from credits
        ├─ Client account (users.type = customer) is created
        └─ IPTV provisioning runs automatically (no admin approval needed)
```

### Reseller Dashboard

| Section | Description |
|---------|-------------|
| Overview | Total clients, active clients, total subscriptions, total revenue |
| Clients | Add, view, manage client subscriptions |
| Credits | Current balance, top-up requests, transfer history |
| API Keys | Generate/revoke Sanctum tokens for the Reseller API |

### Markup
Each reseller has a `markup_percentage` field on their user record. When a reseller creates a client subscription, the cost deducted from their credits is: `plan_price × (1 + markup / 100)`.

### Credit Transaction Log
Every credit movement (debit/credit) is recorded in `reseller_credit_logs` with:
- Type (credit/debit)
- Amount
- Balance after transaction
- Description and reference

---

## 8. Content Management

### Blog
- Full blog with posts, categories, tags, and comments.
- Multi-language support via `blog_translations` table.
- Comment moderation from the admin panel.

### Static Pages
- Custom pages (About, Terms, Privacy, etc.) managed from admin.
- Multi-language via `page_translations`.

### Homepage Builder
- Dynamic homepage sections managed from `HomePageBuilderController`.
- Section content stored in `home_page_sections`.

### Featured Content
Content types managed for the homepage showcase:

| Type | Description |
|------|-------------|
| `movie` | Featured movies |
| `series` | TV series |
| `sports_event` | Live sporting events (with event date) |
| `new_release` | New releases |

### App Downloader Codes
Device-specific installation codes/links for:

| Device | Type Key |
|--------|----------|
| Amazon Firestick | `firestick` |
| Android | `android` |
| iOS | `ios` |
| Smart TV | `smart_tv` |
| Desktop | `desktop` |

### Media Management
Centralized file upload/management system. Files are organized and deletable from the admin media library.

### Menus
Dynamic menus (header/footer) managed via the Menu Builder. Menu items link to pages, URLs, or blog categories.

### Multi-language
The platform supports multiple languages. Strings are stored in the `translations` table, keyed by a `Language` record. Admins can add/edit language strings from the backend.

---

## 9. Support Ticket System

### Ticket Lifecycle

```
Customer creates ticket (TKT-XXXXXX auto-generated)
        │
        ▼ Status: New (1)
        │
Admin is assigned / replies
        │
        ▼ Status: In Progress (2)
        │
Either party can close
        │
        ▼ Status: Closed (3)
        │
Customer can reopen
        │
        ▼ Status: Re-Opened (4)
```

### Status Reference

| Code | Label |
|------|-------|
| 1 | New |
| 2 | In Progress |
| 3 | Closed |
| 4 | Re-Opened |

### Features
- Ticket number auto-generation (`TKT-XXXXXX`).
- Admin assignment.
- Threaded replies.
- Customer can close or reopen tickets.

### 24/7 AI Customer Service (Chat Widget)
For always-on support, an external chat/AI widget is injected on every public page.
Configure it at **Admin → Settings → Chat Widget**: toggle `chat_widget_enabled` and paste
the provider's embed script into `chat_widget_code`. Any provider works — Tidio, Crisp, or
Tawk.to all offer free tiers with an AI chatbot. The script is rendered before `</body>` on
all pages that extend `frontend.layouts.master`.

---

## 10. Admin Panel

The admin panel is protected by the `IsAdmin` middleware and further guarded by Spatie role permissions.

### Dashboard
- Real-time business stats (total members, active subscriptions, revenue).
- Member registration chart (via AJAX).

### Module Overview

| Module | Key Actions |
|--------|------------|
| **Members** | List, create, edit, delete members; reset passwords |
| **Subscriptions** | List (with filters), approve, reject, delete; send payment links; reprovision |
| **Pricing Plans** | CRUD; configure IPTV specs per plan |
| **Resellers** | List resellers; top-up credits; view credit logs |
| **Support Tickets** | List, assign, reply, change status |
| **Blog** | Posts CRUD; categories, tags; comment moderation |
| **Pages** | Static page CRUD |
| **Users & Roles** | Admin user management; Spatie role & permission management |
| **Media** | Upload & manage files |
| **App Downloader Codes** | Manage device installation codes |
| **Featured Content** | Manage movies/series/events showcase |
| **Reports** | Revenue, active subscribers, expiring subscriptions, reseller performance; CSV export |
| **API Logs** | View all reseller API request history |
| **Notifications** | System notifications inbox |

### Settings

| Section | Contents |
|---------|----------|
| **Environment** | App name, URL, debug mode, timezone |
| **SMTP** | Mail host, port, credentials, sender name |
| **Social Login** | OAuth provider keys for Socialite |
| **IPTV / Xtream Codes** | API URL, admin credentials |
| **WHMCS** | WHMCS API credentials (optional) |
| **Payment Settings** | Stripe publishable/secret keys, webhook secret |
| **Site Settings** | Logo, contact info, SEO meta, analytics code |
| **Appearance** | Primary color, custom CSS, color theme |
| **Menu Builder** | Header and footer menu items |
| **Home Builder** | Homepage section ordering and content |

---

## 11. REST API (Reseller API)

Base URL: `/api/v1/`

Authentication: Bearer token (Sanctum) — obtained from the reseller portal.

All requests are logged in `api_logs`.

### Public API (no auth)

For player apps (XCIPTV, Smarters, etc.) to display promotional / "coming soon" tiles:

| Method | Endpoint | Description |
|--------|----------|-------------|
| `GET` | `/api/v1/featured-content` | Active featured movies/series/sports events (cached 10 min) |

Returns `{ "success": true, "data": [ { id, title, subtitle, type, type_label, genre, release_year, rating, event_date, badge_text, thumbnail, trailer_url, youtube_id } ] }`.

### Endpoints (Reseller — Sanctum token)

> Reseller routes are served under the `/api/reseller/v1/` prefix.

#### Authentication
Tokens are managed from the reseller web portal (no API endpoint for token creation).

#### Clients

| Method | Endpoint | Description |
|--------|----------|-------------|
| `GET` | `/api/v1/clients` | List the reseller's clients with active subscriptions |
| `POST` | `/api/v1/clients` | Create a new client and provision an IPTV subscription |
| `POST` | `/api/v1/clients/{id}/suspend` | Suspend a client's IPTV access |
| `POST` | `/api/v1/clients/{id}/reactivate` | Reactivate a suspended client |

#### Plans

| Method | Endpoint | Description |
|--------|----------|-------------|
| `GET` | `/api/v1/plans` | List all available pricing plans |

#### Credits

| Method | Endpoint | Description |
|--------|----------|-------------|
| `GET` | `/api/v1/credits` | Get the reseller's current credit balance |

#### Notifications

| Method | Endpoint | Description |
|--------|----------|-------------|
| `GET` | `/api/v1/notifications` | List the reseller's notifications |

### Create Client — Request Body

```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "phone": "+1234567890",
  "plan_id": 3
}
```

### Create Client — Response (201)

```json
{
  "message": "Client created successfully",
  "client": {
    "id": 42,
    "name": "John Doe",
    "xtream_username": "user_abc123",
    "xtream_password": "pass_xyz789",
    "m3u_url": "http://iptv.example.com/get.php?username=...",
    "epg_url": "http://iptv.example.com/xmltv.php?username=..."
  }
}
```

---

## 12. Email Notifications

| Trigger | Mail Class | Recipient |
|---------|-----------|-----------|
| Subscription approved | `WelcomeMail` | Customer |
| Invoice generated | `InvoiceMail` | Customer |
| Subscription about to expire | Expiry reminder | Customer |
| Subscription rejected | Rejection notice | Customer |
| Payment link generated | `PaymentLinkMail` | Customer |
| Contact form submission | `ContactMail` | Admin |
| Ticket reply | `TicketReplyMail` | Customer/Admin |

All mail settings (SMTP host, port, credentials) are configurable from the admin settings panel and stored in the `settings` table / `.env`.

---

## 13. Database Schema

### Core Tables

#### `users`
| Column | Type | Notes |
|--------|------|-------|
| `id` | bigint | PK |
| `name` | string | |
| `email` | string | unique |
| `password` | string | hashed |
| `type` | tinyint | 1=admin, 2=customer, 3=reseller |
| `status` | tinyint | 1=active, 0=inactive |
| `phone` | string | nullable |
| `company_name` | string | nullable |
| `reseller_id` | bigint | FK → users (for reseller clients) |
| `credits` | decimal | reseller credit balance |
| `markup_percentage` | decimal | reseller markup |
| `xtream_username` | string | nullable |
| `xtream_password` | string | nullable |
| `stripe_customer_id` | string | nullable |
| `whmcs_client_id` | integer | nullable |

#### `user_subscriptions`
| Column | Type | Notes |
|--------|------|-------|
| `id` | bigint | PK |
| `user_id` | bigint | FK → users |
| `plan_id` | bigint | FK → pricing_plans |
| `status` | string | active / pending / rejected |
| `payment_method` | string | stripe / bank / sslcommerz / free |
| `transaction_id` | string | nullable |
| `amount` | decimal | |
| `xtream_username` | string | provisioned credentials |
| `xtream_password` | string | provisioned credentials |
| `line_id` | string | Xtream Codes line ID |
| `invoice_id` | bigint | FK → invoices |
| `start_date` | date | nullable |
| `end_date` | date | nullable |
| `admin_note` | text | nullable |

#### `pricing_plans`
| Column | Type | Notes |
|--------|------|-------|
| `id` | bigint | PK |
| `title` | string | |
| `duration_days` | integer | |
| `price` | decimal | |
| `status` | tinyint | 1=active |
| `max_connections` | integer | IPTV spec |
| `streaming_quality` | string | IPTV spec |
| `catchup_days` | integer | IPTV spec |
| `dvr_enabled` | boolean | IPTV spec |
| `is_trial` | boolean | |

#### `invoices`
| Column | Type | Notes |
|--------|------|-------|
| `invoice_number` | string | INV-YYYY-XXXXX |
| `user_id` | bigint | FK |
| `subscription_id` | bigint | FK |
| `subtotal` | decimal | |
| `tax` | decimal | |
| `total` | decimal | |
| `status` | string | paid / unpaid / draft |
| `pdf_path` | string | storage path |
| `due_date` | date | |
| `paid_at` | timestamp | nullable |

#### `support_tickets`
| Column | Type | Notes |
|--------|------|-------|
| `ticket_number` | string | TKT-XXXXXX |
| `user_id` | bigint | FK |
| `assigned_to` | bigint | FK → users (admin) |
| `subject` | string | |
| `status` | tinyint | 1=new, 2=in-progress, 3=closed, 4=reopened |
| `priority` | string | |
| `department` | string | |

#### `reseller_credit_logs`
| Column | Type | Notes |
|--------|------|-------|
| `reseller_id` | bigint | FK |
| `user_id` | bigint | nullable FK (client) |
| `type` | string | credit / debit |
| `amount` | decimal | |
| `balance_after` | decimal | running balance |
| `description` | string | |

#### `payment_links`
| Column | Type | Notes |
|--------|------|-------|
| `token` | string | unique, URL-safe |
| `user_id` | bigint | FK |
| `plan_id` | bigint | FK |
| `expires_at` | timestamp | |
| `used_at` | timestamp | nullable |

---

## 14. Services

### `StripeService`
Wraps the Stripe PHP SDK.

| Method | Description |
|--------|-------------|
| `createPaymentIntent($amount, $currency, $customerId)` | Initiate Stripe payment |
| `createOrRetrieveCustomer($user)` | Get or create Stripe customer |
| `constructWebhookEvent($payload, $sig)` | Validate & parse Stripe webhook |
| `chargeForRenewal($subscription)` | Charge stored payment method for renewal |
| `refund($paymentIntentId)` | Issue refund |

### `IptvProvisioningService`
Orchestrates the full provisioning lifecycle.

| Method | Description |
|--------|-------------|
| `provision($subscription)` | Create IPTV account + send credentials |
| `suspend($subscription)` | Ban IPTV account |
| `reactivate($subscription)` | Unban IPTV account |
| `reprovision($subscription)` | Update IPTV account details |
| `delete($subscription)` | Remove IPTV account |

### `XtreamCodesService`
Direct integration with the Xtream Codes panel REST API.

| Method | Description |
|--------|-------------|
| `createUser(array $data)` | POST new user to Xtream panel |
| `updateUser($lineId, array $data)` | Update existing user |
| `deleteUser($lineId)` | Delete user |
| `getUserInfo($lineId)` | Fetch user details |
| `banUser($lineId)` | Suspend user access |
| `unbanUser($lineId)` | Restore user access |
| `getM3uUrl($credentials)` | Build M3U playlist URL |
| `getEpgUrl($credentials)` | Build EPG URL |

### `WhmcsService`
Optional sync layer for WHMCS billing platform.

| Method | Description |
|--------|-------------|
| `createClient($user)` | Create client in WHMCS |
| `createOrder($client, $plan)` | Create billing order |
| `provisionService($orderId)` | Mark service as provisioned |

### `InvoiceService`
Handles invoice creation and delivery.

| Method | Description |
|--------|-------------|
| `generate($subscription)` | Create Invoice record |
| `generatePdf($invoice)` | Render DomPDF and store |
| `sendEmail($invoice)` | Email invoice to customer |

---

## 15. Route Map

### Public Routes (`routes/web.php`)

| Route | Controller | Description |
|-------|-----------|-------------|
| `GET /` | `HomepageController` | Landing page |
| `GET /pricing` | `HomepageController` | Pricing plans |
| `GET /contact` | `ContactController` | Contact page |
| `POST /contact` | `ContactController` | Submit contact form |
| `GET /blog` | `BlogController` | Blog list |
| `GET /blog/{slug}` | `BlogController` | Blog post |
| `GET /page/{slug}` | `PageController` | Static page |
| `POST /newsletter/subscribe` | `NewsletterController` | Subscribe |
| `GET /pay/{token}` | `SubscriptionController` | Public payment link |
| `POST /stripe/webhook` | `StripeWebhookController` | Stripe webhook |

### Member Routes (`/member/*`)

| Route | Description |
|-------|-------------|
| `GET /member/login` | Login page |
| `POST /member/login` | Authenticate |
| `GET /member/register` | Registration page |
| `POST /member/register` | Create account |
| `GET /member/dashboard` | Member dashboard |
| `GET /member/subscriptions` | My subscriptions |
| `POST /member/subscriptions/purchase/{plan}` | Initiate purchase |
| `GET /member/support-tickets` | My tickets |
| `POST /member/support-tickets` | Create ticket |
| `GET /member/support-tickets/{id}` | View ticket |
| `POST /member/support-tickets/{id}/reply` | Reply to ticket |
| `GET /member/account` | Account settings |
| `GET /member/setup-guide` | IPTV setup guide |

### Reseller Routes (`/reseller/*`)

| Route | Description |
|-------|-------------|
| `GET /reseller/dashboard` | Reseller dashboard |
| `GET /reseller/clients` | Client list |
| `POST /reseller/clients` | Add client |
| `GET /reseller/credits` | Credit balance & history |
| `POST /reseller/credits/request-topup` | Request credit top-up |
| `POST /reseller/credits/transfer` | Transfer credits to client |
| `GET /reseller/api-keys` | API token management |
| `POST /reseller/api-keys` | Generate new API token |

### Admin Routes (`/admin/*`)

| Route | Description |
|-------|-------------|
| `GET /admin/dashboard` | Admin dashboard |
| `GET /admin/members` | Member list |
| `GET /admin/subscriptions` | Subscription list |
| `POST /admin/subscriptions/{id}/approve` | Approve subscription |
| `POST /admin/subscriptions/{id}/reject` | Reject subscription |
| `GET /admin/pricing-plans` | Pricing plan list |
| `GET /admin/resellers` | Reseller list |
| `POST /admin/resellers/{id}/topup` | Top up reseller credits |
| `GET /admin/support-tickets` | Support ticket list |
| `GET /admin/reports/revenue` | Revenue report |
| `GET /admin/settings` | System settings |

### API Routes (`/api/v1/*`)

| Method | Route | Description |
|--------|-------|-------------|
| `GET` | `/api/v1/clients` | List clients |
| `POST` | `/api/v1/clients` | Create client |
| `POST` | `/api/v1/clients/{id}/suspend` | Suspend client |
| `POST` | `/api/v1/clients/{id}/reactivate` | Reactivate client |
| `GET` | `/api/v1/plans` | List plans |
| `GET` | `/api/v1/credits` | Get credit balance |
| `GET` | `/api/v1/notifications` | Get notifications |

---

## 16. Configuration & Environment

### Required `.env` Variables

```dotenv
# Application
APP_NAME="Moissanite Radiance"
APP_ENV=production
APP_KEY=base64:...
APP_DEBUG=false
APP_URL=https://yourdomain.com

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=alborada
DB_USERNAME=root
DB_PASSWORD=secret

# Mail (SMTP)
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=587
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="${APP_NAME}"

# Stripe
STRIPE_KEY=pk_live_...
STRIPE_SECRET=sk_live_...
STRIPE_WEBHOOK_SECRET=whsec_...

# SSL Commerce (Bangladesh)
SSLCOMMERZ_STORE_ID=...
SSLCOMMERZ_STORE_PASSWORD=...
SSLCOMMERZ_IS_SANDBOX=false

# Queue
QUEUE_CONNECTION=database
```

### Settings Table
Dynamic settings (IPTV credentials, social login keys, site branding) are stored in the `settings` table and managed from the admin panel. No `.env` change needed for these.

---

## 17. Installation & Setup

### Prerequisites
- PHP 8.2+
- Composer 2
- MySQL 8+
- Node.js 18+ with npm
- An Xtream Codes panel with API access
- A Stripe account (for card payments)

### Steps

```bash
# 1. Clone the repository
git clone <repo-url> alborada
cd alborada

# 2. Install PHP dependencies
composer install --no-dev --optimize-autoloader

# 3. Install and build frontend assets
npm install && npm run build

# 4. Configure environment
cp .env.example .env
php artisan key:generate
# Edit .env with your database, mail, Stripe credentials

# 5. Run migrations and seed
php artisan migrate --seed

# 6. Create storage symlink
php artisan storage:link

# 7. Cache configuration (production)
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 8. Set up queue worker (for async jobs)
php artisan queue:work --daemon

# 9. Set up scheduler (add to cron)
# * * * * * cd /path-to-app && php artisan schedule:run >> /dev/null 2>&1
```

### Docker
A `docker-compose.yml` is included for containerized development:

```bash
docker-compose up -d
```

### First Admin Account
After seeding, log in at `/admin/login` with the credentials defined in `DatabaseSeeder.php`.

---

## 18. Technology Stack

| Layer | Technology |
|-------|-----------|
| **Framework** | Laravel 12 (PHP 8.2) |
| **Database** | MySQL 8 (via Eloquent ORM) |
| **Frontend** | Blade templates + Tailwind CSS 4 + Vite |
| **API Auth** | Laravel Sanctum |
| **OAuth** | Laravel Socialite |
| **Roles/Permissions** | Spatie Laravel Permission |
| **Payment — Cards** | Stripe PHP SDK v20 |
| **Payment — BD** | SSL Commerce |
| **IPTV Panel** | Xtream Codes (REST API) |
| **Billing Sync** | WHMCS (optional) |
| **PDF Generation** | DomPDF (`barryvdh/laravel-dompdf`) |
| **Toast Notifications** | `brian2694/laravel-toastr` |
| **Phone Validation** | `propaganistas/laravel-phone` |
| **HTTP Client** | Laravel HTTP (Guzzle) |
| **Queues** | Database-driven queue |
| **Cache** | File/Redis (configurable) |
| **Testing** | PHPUnit 11 |
| **Dev Tools** | Laravel Debugbar, Laravel Pail, Laravel Pint |

---

*Documentation generated: 2026-05-13*
