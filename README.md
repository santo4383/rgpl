# Online MCQ Practice & Examination Platform

A production-ready, enterprise-grade Online Multiple-Choice Question (MCQ) Practice and Examination Platform tailored for competitive exam aspirants (e.g., MPSC, Engineering, Competitive Services).

Built with **Laravel 11**, **PostgreSQL**, **Livewire 3**, **Alpine.js**, and **Tailwind CSS**.

---

## Key Modules & Capabilities

### 1. Authentication & Role-Based Access Control (RBAC)
- **Mobile Number + SMS OTP Authentication**: Seamless login and registration with cooldown timer and expiry constraints.
- **Strict Role-Based Protection**: Distinct authorization barriers for `STUDENT` and `SUPER_ADMIN` with custom 403 Forbidden views and route middleware.
- **Session Management**: Secure session handling with custom 419 token expiration handling.

### 2. Course, Category & Question Bank Management
- **3-Tier Hierarchy**: Course &rarr; Category / Subject &rarr; Subcategory / Topic.
- **Multilingual MCQ Authoring**: Full support for both English and Marathi (Devanagari script) questions, options, and explanations.
- **Rich Media & Formatting**: Support for formatted questions, mathematical notation, and option-specific answer keys with explanations.
- **Tagging & Metadata**: Difficulty levels (`EASY`, `MEDIUM`, `HARD`), marks allocation, and status management (`ACTIVE`, `DRAFT`).

### 3. Student Experience & Practice Mode
- **Adaptive Practice Engine**: Filter by course, category, subcategory, and difficulty.
- **Free Quota Enforcement**: Free users are governed by a configurable quota limit (default 500 questions per course). Subscribed users enjoy unlimited access.
- **Real-Time Interactive Feedback**: Instant answer verification with detailed conceptual explanations.
- **Student Dashboard**: Visual metrics for questions solved, accuracy rate, target courses, and recent practice sessions.

### 4. Subscriptions & Manual Payment Workflow
- **Subscription Plans**: Flexible plan management (e.g., Course-Specific or All-Access Pass, customizable duration and pricing).
- **Manual Payment Method**: Students transfer funds via UPI QR code or direct NEFT/IMPS Bank Transfer.
- **Payment Verification Flow**: Students upload payment receipt and submit UTR / reference number.
- **Admin Verification Portal**: Super Admins review payment proof, approve subscriptions (which automatically activates course access), or reject invalid payments with reasons.

### 5. Timed Examination & Mock Test Framework
- **Exam Builder**: Admin-configured exams with duration (minutes), passing percentage, negative marking configuration, and per-question mark weightage.
- **Marks Allocation Safeguard**: Real-time validation preventing question marks from exceeding the maximum exam score.
- **Single-Attempt Enforcement**: Prevents students from retaking an exam once started or completed.
- **Interactive Exam Interface**:
  - Live persistent countdown timer.
  - Question status palette (Answered, Marked for Review, Unvisited).
  - One-click navigation and auto-submission on timeout.
- **Instant Result & Comprehensive Review**: Full score card, percentage, pass/fail status, and question-by-question answer review.

### 6. Administration, Audit Logging & Security Hardening
- **Executive Analytics Dashboard** (`/admin/dashboard`):
  - Real-time KPIs: Approved revenue, pending payments, total students, published questions, mock exam attempts, and platform pass rate.
  - Quick-access shortcuts and pending payment alerts.
- **Audit Logging System** (`/admin/audit-logs`):
  - Comprehensive immutable log of all sensitive actions (`LOGIN`, `PAYMENT_APPROVED`, `PAYMENT_REJECTED`, `SETTINGS_UPDATED`, `EXAM_ATTEMPT`, etc.).
  - Tracks user ID, client IP address, user agent, timestamp, before/after JSON state diffs, and inspect modal.
- **Dynamic Platform Settings Manager** (`/admin/settings`):
  - In-memory and cache-backed settings for site branding, default free quota, OTP expiry/cooldown, and banking/UPI payment details.
- **Custom Error Views**:
  - Beautifully styled Tailwind error templates for `404 Not Found`, `403 Forbidden`, `419 Page Expired`, and `500 Server Error`.

---

## Technical Stack

| Layer | Technology |
|---|---|
| **Backend Framework** | Laravel 11.x (PHP 8.2+) |
| **Database** | PostgreSQL 16+ |
| **Frontend Engine** | Livewire 3 + Alpine.js |
| **Styling & UI** | Tailwind CSS + Vite |
| **State & Caching** | Laravel Cache + PostgreSQL Sessions |
| **Testing** | PHPUnit Feature Tests (100% Pass) |

---

## Getting Started

### 1. Prerequisites
- **PHP**: 8.2 or higher (with `pdo_pgsql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `bcmath`)
- **Composer**: Latest version
- **Node.js**: 18+ and `npm`
- **PostgreSQL**: Running instance with a database named `mcq`

### 2. Environment Setup
```bash
# Clone repository and navigate to project
cd mcq-platform

# Install PHP dependencies
composer install

# Install NPM dependencies
npm install

# Copy environment configuration
cp .env.example .env
```

### 3. Database Configuration
Edit `.env` to configure your PostgreSQL credentials:
```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=mcq
DB_USERNAME=postgres
DB_PASSWORD=your_password
```

### 4. Migrations & Seeders
Run migrations and load default seeders (roles, sample courses, settings, and subscription plans):
```bash
php artisan migrate --force
php artisan db:seed
```

### 5. Build Assets & Start Server
```bash
# Compile frontend assets
npm run build

# Start local server
php artisan serve
```
The application will be accessible at: `http://127.0.0.1:8000`

---

## Default Administrative Access

| Role | Mobile Number | OTP | Dashboard URL |
|---|---|---|---|
| **Super Admin** | `9999988888` | (Sent via SMS / Logged) | `http://127.0.0.1:8000/admin/dashboard` |

> *Note:* In development or testing environment without active SMS gateway, OTP codes are logged to `storage/logs/laravel.log`.

---

## Running Automated Tests

The platform includes an extensive automated test suite covering all authentication flows, practice quotas, subscription approvals, exam timing, negative marking, and audit logging.

```bash
# Run all automated feature and unit tests
php artisan test
```

Current Test Coverage: **32 Tests, 115 Assertions, 100% Passing**.
