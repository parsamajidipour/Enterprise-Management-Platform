# Security Policy

This is a portfolio/demo project, not a production service handling real
customer data. That said, it's built and reviewed with the same discipline
I'd apply to a production system, and the sections below describe its
actual current security posture honestly — including known gaps.

## Reporting a Vulnerability

Please report vulnerabilities privately via
[GitHub Security Advisories](../../security/advisories/new) for this
repository rather than opening a public issue. I aim to acknowledge reports
within a few days.

## Supported Versions

There is no versioned release line — only the latest commit on `main` is
maintained. There are no LTS branches and no backported fixes.

## Threat Model

### Assets

- User credentials and Laravel Sanctum API tokens
- Work order, asset registry, and inspection data (including asset GIS
  coordinates)
- Technician profile data: name, email, phone number, and location history
  submitted from the field
- Uploaded evidence files (photos attached to inspections/work orders)
- The application database (MySQL)

### Trust boundaries

- **Public internet → Nginx reverse proxy**: untrusted input enters here;
  all HTTP request validation downstream assumes this boundary is not
  otherwise trusted.
- **Nginx → Nuxt frontend / Laravel API**: the edge tier forwards traffic to
  the app tier; both are currently deployed as internal Docker services with
  no network segmentation beyond the compose network.
- **Laravel API → MySQL**: the only component with direct data-tier access;
  all persistence goes through Eloquent (parameterized queries).
- **Mobile app → API**: the Flutter technician app authenticates via
  Sanctum and talks to the same API surface as the web admin console. In the
  demo build this has run over plain HTTP with
  `usesCleartextTraffic` enabled on Android (see `mobile/README.md`) — this
  is acceptable for a local demo only and must not be used as-is against a
  real deployment.
- **CMMS adapter boundary**: the "fake CMMS" service simulates an external
  third-party system and its endpoints are intentionally unauthenticated in
  the demo, standing in for what would be a separately-trusted external
  integration in production.

### Authentication & authorization — current state

- **Authentication**: Laravel Sanctum, token-based.
- **Authorization**: `spatie/laravel-permission` roles are seeded and
  assigned to users (see `RolesAndPermissionsSeeder`), but **no Laravel
  Policies exist yet and no route or controller currently checks
  `hasRole()`/`can()`**. In practice, any authenticated user can call any
  authenticated endpoint regardless of assigned role. This is a known,
  unimplemented gap, not a partially-enforced system.
- **Rate limiting**: no `throttle` middleware is currently applied to the
  login endpoint or any other route.
- **Security headers**: no custom security-header middleware (CSP, HSTS,
  etc.) is currently configured beyond framework defaults.

## Known Dependency Findings

Run via CI on every push (`composer audit`, `npm audit`); current known
findings that are **not** fixed, with reasoning:

- **`laravel/framework` — CVE-2026-48019.** See the dedicated section below
  for the mitigation actually implemented for this one.
- **Frontend build tooling (Nuxt/Vite/Tailwind dev tooling)**: `npm audit`
  currently reports high-severity advisories in transitive dev dependencies
  of the build toolchain (not runtime/shipped code). Resolving them requires
  a breaking major upgrade of Nuxt itself. CI's `npm audit` step is
  threshold-gated to fail only on critical findings for this reason.

## CVE-2026-48019 — Application-Layer Mitigation

**What it is:** a CRLF injection (CWE-93) in Laravel's built-in `email`
validation rule. A value like `victim@example.com\r\nBcc: attacker@evil.com`
passes the stock `email` rule, so if that value later flows into anything
that treats it as a raw email/mail header, an attacker can inject extra
headers (e.g. add a `Bcc`).

**Why it isn't patched via Composer:** the fix shipped only in Laravel
12.60.0 and 13.10.0. This project is pinned to `laravel/framework: ^10.10`,
and the *entire* 9.x/10.x/11.x/early-12.x line is affected — there is no
patched 10.x release to update to. Laravel 10 itself is EOL. Taking the fix
via Composer would mean a major-version upgrade (10 → 12+), which is a
breaking change out of scope for this pass. `composer audit` continues to
report this finding in CI (non-blocking, not hidden) until that upgrade
happens.

**Where it actually applies:** a full audit of every `FormRequest` and
inline validator in the backend found exactly **one** input using the
`email` rule: `LoginRequest::rules()['email']` (`app/Http/Requests/Api/Auth/LoginRequest.php`).
There is no registration, password-reset, or notification/contact-email
endpoint in this codebase — `grep -rn "'email'" app` and a full listing of
`app/Http/Requests` were used to confirm this rather than assumed.

**Mitigation implemented:** `app/Rules/NoCrlf.php`, a small `ValidationRule`
that rejects any string containing `\r` or `\n`, applied alongside the
built-in `email` rule specifically on `LoginRequest`:

```php
'email' => ['required', 'email', new NoCrlf()],
```

This is intentionally **not** a global middleware stripping/rejecting CRLF
on all request input — that would break legitimate multi-line fields (e.g.
inspection notes/comments). The rule is scoped to email-typed inputs only,
applied per-field.

Regression coverage: `tests/Feature/Auth/LoginCrlfInjectionTest.php` asserts
a CRLF-bearing email is rejected with `422` and that a normal email still
logs in successfully.

**Limitations of this mitigation:**

- It only covers the one email input that exists *today*. Any future
  `FormRequest` with an `email`-typed field must explicitly add `new NoCrlf()`
  too — this is not enforced automatically project-wide, by design (to avoid
  the blast radius of a global filter).
- It addresses the CRLF-injection symptom at the input boundary. It does not
  address whatever the underlying Laravel `email` rule advisory considers
  the root cause inside the framework itself; the real fix is still the
  Laravel 12.60+/13.10+ upgrade.
- This project doesn't currently send outbound email at all (no
  Notification/Mailable classes exist), so today there's no downstream
  consumer that would actually turn this into a real Bcc-injection exploit —
  but the login field was still hardened since validating trust at the
  input boundary shouldn't depend on what today's downstream code happens
  to do with it.

## Secret Scanning

CI runs `gitleaks` on every push and pull request. The project's git
history was previously scrubbed (via `git-filter-repo`) of a leaked
`.env` file, a hardcoded Laravel `APP_KEY`, and other client-specific
sensitive documents from an earlier phase of this project.
