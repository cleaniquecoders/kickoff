# Changelog

All notable changes to `kickoff` will be documented in this file.

## 1.28.0 - 2026-06-25

### New

- **Admin > Settings > Authentication** — a new settings page with a public-registration toggle, now backed by a real `admin.settings.authentication` route + Livewire component (the previously dead reference removed in 1.27.1). When disabled, Fortify's registration routes are dropped (`AppServiceProvider` filters `config('fortify.features')`), the login "Sign up" link hides via `config('admin.public_registration')`, and only administrators can create accounts. The toggle is DB-stored via `AuthenticationSettings` (seeded from the new `REGISTRATION_ENABLED` env default), so admins can flip it at runtime without a redeploy.
- **General settings: timezone** — `GeneralSettings` gains an admin-editable `timezone` (a select seeded from `config('app.timezone')`). `AppServiceProvider` lays it over `config('app.timezone')` and calls `date_default_timezone_set()` at boot so all date/time functions use it.

**Full Changelog**: https://github.com/cleaniquecoders/kickoff/compare/1.27.1...1.28.0

## 1.27.1 - 2026-06-25

### Fixed

- **Admin settings sidebar links** now deep-link to the correct section instead of the settings hub:
  - **Mail → Settings** → `/admin/settings/email` (was `/admin/settings`)
  - **Settings → General** → `/admin/settings/general` (was `/admin/settings`)
  - **Settings → Notifications** added → `/admin/settings/notifications` (previously missing — replaced a dead `admin.settings.authentication` route that silently hid)
  

All three settings sections (`general`, `email`, `notifications`) served by `admin.settings.show` are now reachable from the Administration sidebar with correct URLs. Recorded as gotcha #18 in `CLAUDE.md`.

**Full Changelog**: https://github.com/cleaniquecoders/kickoff/compare/1.27.0...1.27.1

## 1.27.0 - 2026-06-19

### Fixed

- **Test email now tracks opens** — the Settings test email was sent via `Mail::raw` (plaintext), so the mailhistory open pixel (HTML-only) never injected and the email could never reach "Opened". It now sends via `DefaultMail` (an HTML Mailable); `DefaultMail` uses `InteractsWithMailMetadata` to carry the metadata hash.
- **Open/click tracking on by default** — `config/mailhistory.php` defaults `tracking.open`/`tracking.click` to `true` (was `false`), so tracking works without a per-deploy env var (set `MAILHISTORY_TRACK_*=false` to opt out). The tracking routes are public so email clients can load the pixel.

## 1.26.0 - 2026-06-19

### Fixed

- **Telescope `/telescope` 404 when `TELESCOPE_ENABLED` isn't injected** — `config/telescope.php` now defaults `enabled` to `true` (was `false`). Telescope must register its dashboard route so `/telescope` and the sidebar item work even when a deploy/platform doesn't pass the env var (a patched project's runtime `.env` may lack it). Only the exception watcher records; dashboard access stays gated by `access.telescope`. Set `TELESCOPE_ENABLED=false` to opt out.

## 1.25.0 - 2026-06-19

### New

- **Nested Administration menu** — one collapsible Administration group nesting Identity, Mail, Backups, Settings, Developers; Resources group at the bottom. Every leaf is route + gate guarded.
- **Mail History** — outbound-email audit log at Administration → Mail → History (`admin.mail-history.index`) over `cleaniquecoders/mailhistory`, wired end-to-end (package + publish step + route + gate + component + views).
- **Full SMTP Mail Settings** — complete SMTP form (Mailer, Encryption, Host, Port, Username, Password, From) + send-test-email; `MailSettings` applied to runtime mail config.

### Improvements

- g8stack-style group headers (`[icon] Label ›`) with a vertical guide line; nested sub-groups re-sync on `livewire:navigated`.
- Long sidebar labels truncate with an ellipsis (`[data-flux-navlist-item] [data-content]{min-width:0}`).
- Portable menu `child()` helper — candidate route/ability pairs + `$routeParams`.

**Full Changelog**: https://github.com/cleaniquecoders/kickoff/compare/1.24.0...1.25.0

## 1.24.0 - 2026-06-18

### Row-click detail flyouts (#46)

Admin list pages now open a **detail flyout when you click a row** — a consistent, faster way to inspect a record without leaving the list.

- **Roles** — flyout shows role info + permissions management (reuses `admin.roles.show`); 3-dot menu kept for writes.
- **Users** — detail flyout (status, verification, roles, joined) with inline actions: edit, manage access, reset password, resend verification, suspend/activate, delete. Row checkbox and 3-dot menu are click-isolated.
- **Audit-trail** — static index converted to a Livewire component; **read-only** detail flyout. The detail markup is shared between the flyout and the show page, so the deep-link URL still works.

#### Convention

Added **"Flyout vs Modal vs Dedicated Page — when to use which"** to `stubs/CLAUDE.md` (decision table, heuristics, and the parent-owns-state row-click→flyout pattern).

**Full Changelog**: https://github.com/cleaniquecoders/kickoff/compare/1.23.0...1.24.0

## 1.23.0 - 2026-06-18

### Highlights

#### New

- **Laravel MCP Kit** — generated projects now bootstrap a ready-to-use MCP server via `cleaniquecoders/laravel-mcp-kit` (token-authenticated `mcp/tasks` endpoint + local STDIO transport), with the required Gate abilities mapped into the permission model. (#39)
- **Sidebar UX** — Horizon/Telescope/Artisan Runner open in a new tab (with a right-aligned external-link icon); menu groups collapse by default and auto-expand the active group. (#41, #42, #43)
- **`flux:main` page container** — standardised page width/padding (`max-w-7xl` + `mx-auto` + `p-6 lg:p-8`); removed redundant per-page wrappers that caused double padding. (#41)
- **Telescope enabled by default** — exception watcher only; dashboard still gated to local/staging. (#44)
- **Refreshed Kickoff logo** — clean single-`K` mark. (#45)

#### Fixes (fresh-provision runtime errors)

- `/login` — re-enabled `Features::passkeys()` (the starter-kit passkey views need it) → no more `RouteNotFoundException [passkey.login-options]`. (#39)
- `/security/audit-trail` — added a `uuid` column to the `audits` table → no more `UrlGenerationException`. (#39)
- `/telescope` — removed the self-redirect routes that caused `ERR_TOO_MANY_REDIRECTS`. (#39)
- `GET /` — `REDIS_PASSWORD` now ships as `null` so passwordless local Redis works out of the box. (#39)
- `/admin/roles` — pinned the Spatie `Role`/`Permission` guard to `web` so `auth:sanctum`'s default-guard flip no longer breaks `Role::withCount('users')` (500). (#40)

**Full Changelog**: https://github.com/cleaniquecoders/kickoff/compare/1.22.0...1.23.0

## 1.22.0 - 2026-06-12

### What's New

#### Artisan Runner — Superadmin Only (#37)

Pre-configured [cleaniquecoders/laravel-artisan-runner](https://github.com/cleaniquecoders/laravel-artisan-runner) — run allowlisted Artisan commands from a Livewire UI with queued execution, logs, and notifications.

- Route gated with `can:access.artisan-runner` (the package has no built-in gate — middleware is the only access control)
- `admin.access.artisan-runner` permission seeded but **deliberately not granted to any role** — superadmin-only via wildcard; grant explicitly to share
- Gate wired in `AdminServiceProvider` (Horizon/Telescope pattern), menu item under Audit & Monitoring
- Safe manual command allowlist defaults; docs guide + `ARTISAN_RUNNER_NOTIFY_EMAIL` env

#### Fixes

- Register the artisan-runner Livewire namespace app-side — works around the package's facade `method_exists` check that breaks component resolution on Livewire 4 ([upstream #5](https://github.com/cleaniquecoders/laravel-artisan-runner/issues/5))
- Ship artisan-runner logo SVGs in stubs — upstream export-ignores `/art` so composer installs have no assets ([upstream #6](https://github.com/cleaniquecoders/laravel-artisan-runner/issues/6)) (#38)

Verified in sandbox: superadmin → ALLOW, administrator → DENY; page renders with logo.

**Full Changelog**: https://github.com/cleaniquecoders/kickoff/compare/1.21.0...1.22.0

## 1.21.0 - 2026-06-12

### What's New

#### Collapsible Sidebar & Menu (#27)

- Desktop toggle between full sidebar and icon-only rail, persisted via cookie (zero-flicker SSR under `wire:navigate`)
- Flyout submenus next to the rail when collapsed, with tooltips
- Collapsible menu groups (multi-open) with smooth grid-rows transitions and rotating chevrons (#36)

#### User Management Overhaul (#28)

- Users index as a full Livewire component: search, role/status filters, status-aligned stats, bulk delete + bulk role assignment
- Create/edit users via flyout panels with an invite flow (user sets own password via reset link)
- Manage Access flyout: role toggles + direct permission toggles with inherited-via-role hints
- Account suspension (`suspended_at` + middleware), admin-triggered password reset & verification resend
- Role CRUD with protected-role and in-use guards
- Supersedes the interim `UserIndex`/`UserPanel` components from 1.20.0

#### Pre-configured Packages (#29, #30, #31)

- [cleaniquecoders/laravel-config-webhook](https://github.com/cleaniquecoders/laravel-config-webhook) — outgoing webhooks admin UI, `webhooks` queue in Horizon
- [cleaniquecoders/laravel-config-backup](https://github.com/cleaniquecoders/laravel-config-backup) — encrypted config backup/restore, Spatie settings allowlist pre-filled
- [cleaniquecoders/laravel-config-sso](https://github.com/cleaniquecoders/laravel-config-sso) — database-backed SSO providers admin UI
- New `admin.manage.{webhooks,config-backup,sso}` permissions seeded to administrator

#### Fixes

- Starter-kit drift breaking fresh projects: Fortify passkeys feature + trait, phpunit `CACHE_STORE`, password-policy test env, `model_has_permissions.uuid` nullable (#32)
- `REDIS_PASSWORD=null` default so fresh projects work with non-Docker Redis (#34)
- Correct `vendor:publish` tags for the config-* packages (package-tools shortName) (#35)

Generated projects pass **79/79** Pest tests out of the box.

**Full Changelog**: https://github.com/cleaniquecoders/kickoff/compare/1.20.0...1.21.0

## 1.19.1 - 2026-05-18

### Bug Fixes

- **Kebab-case DB name bug** — the snake-case substitution for `DB_DATABASE` / `MINIO_BUCKET` in `.env` never ran because a redundant `Update .env.example` step replaced `${PROJECT_NAME}` with the raw kebab name first. Projects like `g8member-app` ended up with `DB_DATABASE=g8member-app`, which MySQL rejects with `Unknown database 'g8member-app'`. Fix: removed the duplicate step; `setupEnvironmentFile()` now snake-cases the DB lines before the generic placeholder pass.

### New Features

- **Auto-create database with SQLite fallback** — new `setupDatabase()` step runs right after package install. If `DB_CONNECTION=mysql` it attempts `CREATE DATABASE IF NOT EXISTS` via the `mysql` CLI using the `.env` credentials (ignoring the `CHANGE_ME_BEFORE_DEPLOY` placeholder). If MySQL isn't reachable or the CLI is missing, kickoff rewrites `.env` to `DB_CONNECTION=sqlite` and touches `database/database.sqlite` so setup completes cleanly on machines without MySQL running.
  
- **stubs/bin/install** now reads `DB_CONNECTION` from `.env` and only attempts MySQL provisioning when applicable — SQLite projects skip the `mysql` CLI calls cleanly.
  

Verified with both `bin/sandbox run` and the originally-failing hyphenated name `developers-hub-my g8member-app` — `.env` now correctly contains `DB_DATABASE=g8member_app` and the database is auto-created.

**Full Changelog**: https://github.com/cleaniquecoders/kickoff/compare/1.19.0...1.19.1

## 1.19.0 - 2026-05-18

### Bug Fixes

- **`operations:install` no longer crashes with `RedisException`** during bootstrap. The dragon-code package passes options through Spatie Laravel Data's `OptionsData::from(...)`, which boots the data structure cache. That cache resolves `CACHE_STORE` → falls back to `CACHE_DRIVER=redis` from the stub `.env`, then authenticates against local Redis with the `REDIS_PASSWORD=CHANGE_ME_BEFORE_DEPLOY` placeholder — and fails when local Redis has no password configured.
  
  `runTasks()` now wraps bootstrap artisan calls in `withSafeBootstrapEnv()`, which `putenv()`s `CACHE_STORE=array`, `CACHE_DRIVER=array`, `SESSION_DRIVER=array` for the duration so the data structure cache stays in-memory and Redis is never touched during setup.
  

### New Features

- **Post-install summary** printed at the end of `kickoff start`. Tells the developer exactly what to configure before running the app:
  - Every `CHANGE_ME_BEFORE_DEPLOY` value in `.env` (superadmin, DB, Redis, Meili, MinIO)
  - Cache / session / queue driver defaults and when to switch off Redis
  - Mail (defaults to Mailpit)
  - Spatie Settings reminder — application-level settings live in the DB, not `.env`
  - Docker Compose one-liner to spin up local services
  - `composer dev` to run the app, plus optional `boost:install`, `horizon`, `telescope:install` follow-ups
  

### Maintenance

- Root `CLAUDE.md` documents the bootstrap-vs-Redis trap as gotcha #11 with a rule: always wrap new bootstrap artisan calls in `withSafeBootstrapEnv()` if they might boot the application container.

Verified end-to-end with `bin/sandbox run`.

**Full Changelog**: https://github.com/cleaniquecoders/kickoff/compare/1.18.2...1.19.0

## 1.18.2 - 2026-05-18

### Documentation

- `stubs/CLAUDE.md` now documents `dragon-code/laravel-deploy-operations` conventions for generated projects:
  - New **Deploy Operations** subsection under Architecture with the command cheat sheet and a *migration vs. operation vs. seeder* decision table
  - Added to the **Packages → Core** list
  - Added to **Quick Reference** with `make:operation`, `operations`, `operations:status`
  - New **DO**: use Deploy Operations for one-off post-deploy data tasks
  - New **DON'T**: don't use the `deploy-operations:*` prefix; don't bury one-off backfills inside migrations
  - New **Gotchas**: command namespace pitfall (`operations:*`, not `deploy-operations:*`) and idempotency reminder
  

Closes the documentation gap left by 1.18.0 / 1.18.1 so AI assistants and developers in generated projects know when and how to use the package without rediscovering the namespace pitfall.

**Full Changelog**: https://github.com/cleaniquecoders/kickoff/compare/1.18.1...1.18.2

## 1.18.1 - 2026-05-18

### Bug Fixes

- Use correct `operations:*` command namespace for `dragon-code/laravel-deploy-operations`. The previous release (1.18.0) referenced non-existent commands (`deploy-operations:install`, `deploy-operations`, etc.), which caused bootstrap to fail with `There are no commands defined in the "deploy-operations" namespace`.

#### Command corrections

| Wrong | Correct |
|---|---|
| `deploy-operations:install` | `operations:install` |
| `deploy-operations` | `operations` |
| `deploy-operations:status` | `operations:status` |
| `make:deploy-operation` | `make:operation` |
| `deploy_operations` table | `operations` table |

#### Files touched

- `src/StartCommand.php` — bootstrap calls `php artisan operations:install`
- `stubs/bin/deploy` — runs `php artisan operations --force` after migrations
- `stubs/docs/04-deployment/01-deployment.md` — corrected all command references, added `operations:rollback` and `operations:fresh` examples
- `stubs/docs/04-deployment/README.md` — corrected deploy-flow snippets

**Full Changelog**: https://github.com/cleaniquecoders/kickoff/compare/1.18.0...1.18.1

## 1.18.0 - 2026-05-18

### What's Changed

#### Features

- Add [`dragon-code/laravel-deploy-operations`](https://github.com/TheDragonCode/laravel-deploy-operations) to bootstrap dependencies for one-off post-deploy tasks (data backfills, fixes, third-party sync, etc.).
- Wire `php artisan deploy-operations:install` into the project bootstrap so the `deploy_operations` table is set up automatically.
- Run `php artisan deploy-operations --force` after `migrate --force` in `stubs/bin/deploy`.

#### Documentation

- New "Deploy Operations" section in `stubs/docs/04-deployment/01-deployment.md` — covers `make:deploy-operation`, run commands, migration-vs-operation-vs-seeder decision table, and gotchas (install on clone, idempotency).
- Updated `stubs/docs/04-deployment/README.md` automated/manual workflows to include deploy operations.

**Full Changelog**: https://github.com/cleaniquecoders/kickoff/compare/1.17.1...1.18.0

## 1.17.1 - 2026-04-20

### 🐛 Bug Fixes

- **Windows compatibility in `kickoff start`** — fixes `laravel new` failure where `getcwd().'/'.$projectName` produced mixed separators like `C:\Users\USER/myapp`, causing the Composer installer's `mkdir()` to fail.

#### What changed

- Added `normalizePath()` helper in `support/helpers.php` that collapses mixed `/` and `\` to the platform's `DIRECTORY_SEPARATOR`.
- `StartCommand::execute()` now normalizes `$projectPath` once on assignment.
- `StartCommand::createLaravelProject()` now:
  - Detects the `laravel` installer cross-platform (`where` on Windows, `command -v` elsewhere).
  - Passes the full normalized path directly to `laravel new` instead of chaining `cd && …`, removing shell-dialect (cmd vs bash) assumptions.
  

#### Tests

- Added `normalizePath()` mixed-separator tests.
- Added `isWindows()` sanity test matching `PHP_OS_FAMILY`.

**Full Changelog**: https://github.com/cleaniquecoders/kickoff/compare/1.17.0...1.17.1

## 1.17.0 - SOC 2 Compliance Controls - 2026-04-06

### SOC 2 Compliance Controls for Generated Projects

This release adds comprehensive security hardening to the stubs/ directory, implementing controls across all five SOC 2 Trust Service Criteria.

#### Security (CC1-CC9)

- **Fix:** Authorization gap in `Admin/Roles/Show` Livewire component — added `$this->authorize()` check
- **Fix:** SQL injection risk in `dumpSql()` helper — proper escaping with type-safe handling
- **Removed** default credentials from `.env.example` — all sensitive values now use `CHANGE_ME_BEFORE_DEPLOY`
- **New:** `SecurityHeaders` middleware — X-Content-Type-Options, X-Frame-Options, Referrer-Policy, Permissions-Policy, HSTS
- **New:** Rate limiting on auth, admin, and security routes via `throttle` middleware
- **New:** Password policy via `config/security.php` — min 12 chars, mixed case, numbers, symbols, uncompromised check
- **New:** Fortify 2FA configuration with password confirmation (`config/fortify.php`)

#### Availability (A1)

- **Improved** backup scripts — database dumps, GPG encryption, integrity verification, retention policy
- **New:** Dedicated database backup script (`bin/backup-db`)
- **Improved** deploy script — maintenance mode, pre-deploy backup, health check, auto-rollback on failure
- **Updated** Docker images — Meilisearch v1.12, MinIO latest, Redis with password authentication

#### Processing Integrity (PI1)

- **Enabled** console audit logging (`AUDIT_CONSOLE=true`)
- **New:** Architecture tests preventing `dumpSql` usage in app code

#### Confidentiality (C1)

- **New:** `EncryptsPii` trait for field-level PII encryption
- **New:** `RedactsPiiInAudit` trait for masking sensitive fields in audit records
- **Hardened** Telescope — hides passwords, secrets, authorization headers; disabled by default
- **Secured** defaults — `SESSION_ENCRYPT=true`, `MAIL_ENCRYPTION=tls`, `LOG_LEVEL=info`
- **New:** PHPStan level 5 config for generated projects

#### Privacy (P1-P8)

- **New:** `data:purge` artisan command for data retention (audits, telescope, soft-deleted users)
- **New:** Security CI workflow (`composer audit` + PHPStan on every push/PR)
- **New:** SOC 2 compliance documentation (`docs/05-security/soc2-compliance.md`)

#### New Files

- `stubs/app/Http/Middleware/SecurityHeaders.php`
- `stubs/config/fortify.php`
- `stubs/config/security.php`
- `stubs/bin/backup-db`
- `stubs/app/Concerns/EncryptsPii.php`
- `stubs/app/Concerns/RedactsPiiInAudit.php`
- `stubs/.phpstan/phpstan.neon.dist`
- `stubs/app/Console/Commands/PurgeExpiredDataCommand.php`
- `stubs/.github/workflows/security.yml`
- `stubs/docs/05-security/soc2-compliance.md`

## 1.16.0 - 2026-03-31

### What's Changed

#### Added

- Laravel 13 support (`illuminate/support: ^13.0`)
- PHPUnit 12 compatibility
- Pest 4 support

#### Changed

- Standardized CI workflow
- Updated dev dependencies

**Full Changelog**: https://github.com/cleaniquecoders/kickoff/compare/1.15.4...1.16.0

## 1.15.4 - 2026-03-31

### Chore: Refactor Claude Code settings

- Remove duplicate and redundant permission entries
- Consolidate patterns (`bin/*` covers `./bin/*`, `python3:*` covers inline scripts)
- Sort alphabetically for readability

**Full Changelog**: https://github.com/cleaniquecoders/kickoff/compare/1.15.3...1.15.4

## 1.15.3 - 2026-03-31

### Docs: Update CLAUDE.md with session lessons

- Fix testing framework reference (Pest, not PHPUnit)
- Add gotchas for Boost interactive setup, unstable package checks, sandbox testing
- Add Boost gotcha to stubs/CLAUDE.md for generated projects

**Full Changelog**: https://github.com/cleaniquecoders/kickoff/compare/1.15.2...1.15.3

## 1.15.2 - 2026-03-30

### Fix: Simplify Boost setup

- Make `boost:install` a non-critical step — if it fails, setup continues
- Remove `boost.json` config manipulation and `boost:update` workaround
- Remove `spatie/guidelines-skills` from dev deps (no stable release, adds complexity)
- Users can run `php artisan boost:install` manually after setup for full interactive config

Tested via `bin/sandbox run` — all steps pass cleanly.

**Full Changelog**: https://github.com/cleaniquecoders/kickoff/compare/1.15.1...1.15.2

## 1.15.1 - 2026-03-30

### Fix: Pin spatie/guidelines-skills to dev-main

- `spatie/guidelines-skills` has no stable release yet — pin to `dev-main` to avoid minimum-stability error during `composer require`

**Full Changelog**: https://github.com/cleaniquecoders/kickoff/compare/1.15.0...1.15.1

## 1.15.0 - 2026-03-30

### Auto-configure Boost Third-Party Packages

#### What's Changed

- **Add `spatie/guidelines-skills`** to dev dependencies — provides Spatie coding conventions (laravel-php, javascript, version-control, security)
- **Auto-register Boost third-party packages** — after `boost:install`, automatically writes the following packages to `boost.json` and runs `boost:update`:
  - `barryvdh/laravel-debugbar` (guideline)
  - `laravel/fortify` (skills)
  - `spatie/guidelines-skills` (guidelines, skills)
  - `spatie/laravel-medialibrary` (guidelines, skills)
  - `spatie/laravel-permission` (skills)
  

This eliminates the interactive third-party selection prompt that was blocking automated project setup.

**Full Changelog**: https://github.com/cleaniquecoders/kickoff/compare/1.14.2...1.15.0

## 1.14.2 - 2026-03-30

### Fix: Boost install fully non-interactive

- Add `--no-interaction` flag to `boost:install` to suppress the third-party package guidelines/skills prompt that was still blocking during automated setup

**Full Changelog**: https://github.com/cleaniquecoders/kickoff/compare/1.14.1...1.14.2

## 1.14.1 - 2026-03-30

### Fix: Boost install non-interactive

- Pass `--guidelines --skills --mcp` flags to `boost:install` so it skips the interactive prompt during project setup

**Full Changelog**: https://github.com/cleaniquecoders/kickoff/compare/1.14.0...1.14.1

## 1.14.0 - 2026-03-30

### Laravel 13 Upgrade with PHP 8.5 Support

#### What's Changed

- **Laravel 13 full support** — Rector config upgraded to Laravel 13 level set, PHP attribute-based model properties adopted
- **PHP 8.5 added to CI** — Test matrix now covers PHP 8.4 and 8.5
- **Model attribute syntax** — Base and User models converted to `#[Fillable]`, `#[Guarded]`, `#[Hidden]` PHP attributes (Laravel 13 convention)
- **Rector upgraded** — Stubs target `UP_TO_LARAVEL_120` + `LARAVEL_130` with PHP 8.5
- **Documentation updated** — CLAUDE.md, copilot-instructions, and docs updated to reflect Laravel 13+, Livewire 4, PHP 8.4+
- **Laravel 13 gotchas added** — CSRF middleware rename, cache serialization defaults, attribute syntax guidance
- **CI fix** — Test workflow now uses `pest` instead of `phpunit`

**Full Changelog**: https://github.com/cleaniquecoders/kickoff/compare/1.13.2...1.14.0

## 1.13.2 - 2026-03-30

### What's Changed

#### Bug Fix

- **Fix `composer dump-autoload` failing on copied stubs**: Added `--no-scripts` flag to prevent `post-autoload-dump` scripts from running during the `setupComposer` step. Stubs copy service providers (e.g. `HorizonServiceProvider`) that reference packages not yet installed — `package:discover` would fail on missing classes. Scripts run correctly later during `composer require`.

**Full Changelog**: https://github.com/cleaniquecoders/kickoff/compare/1.13.1...1.13.2

## 1.13.1 - 2026-03-30

### What's Changed

#### Bug Fix

- **Fix `composer dump-autoload` working directory**: The `setupComposer` step ran `composer dump-autoload` in the user's current directory instead of the target project directory, causing failure when `kickoff start` was invoked from a parent folder (e.g. `~/Projects/2026`). Added `--working-dir` flag to point to the correct project path.

**Full Changelog**: https://github.com/cleaniquecoders/kickoff/compare/1.13.0...1.13.1

## 1.13.0 - 2026-03-14

### What's Changed

- **Impersonation UI**: Added impersonate action button in user management list (3-dot dropdown) and user detail page
- **Impersonation banner**: Wired up the `<x-impersonating />` component in the sidebar layout — displays a red warning bar with "Leave Impersonation" link when active
- **Icon fixes**: Replaced Heroicons with Lucide icons (`circle-alert`, `log-out`) in the impersonation banner
- **Pre-bundled Flux icons**: Added `ellipsis`, `eye`, `user-check`, `circle-alert`, `arrow-left` icon files to stubs so generated projects don't need manual `php artisan flux:icon` imports
- **Convention compliance**: User list now uses 3-dot dropdown menu for actions (per project convention)

**Full Changelog**: https://github.com/cleaniquecoders/kickoff/compare/1.12.0...1.13.0

## 1.12.0 - 2026-03-13

### What's Changed

- Allow WebFetch for fluxui.dev domain in Claude Code local settings

**Full Changelog**: https://github.com/cleaniquecoders/kickoff/compare/1.11.0...1.12.0

## 1.11.0 - 2026-03-13

### What's New

- Added must-have UI requirements: responsive design, dark mode support, max 5 table columns (combine if more), 3-dot action menu pattern
- Added Lucide icon conventions via Flux (`php artisan flux:icon <name>`)
- Added `laravel/boost` to dev dependencies with `boost:install` step

**Full Changelog**: https://github.com/cleaniquecoders/kickoff/compare/1.10.0...1.11.0

## 1.10.0 - 2026-03-13

### What's New

- Added Claude operating principles to both package and stubs CLAUDE.md files — plan mode, subagent strategy, self-improvement loop, verification, elegance, and autonomous bug fixing guidelines

**Full Changelog**: https://github.com/cleaniquecoders/kickoff/compare/1.9.0...1.10.0

## 1.9.0 - 2026-03-13

### What's Changed

#### Bug Fixes

- Fix `.env.example` placeholder typo — `{OWNER}` missing `$` sign (#7)
- Remove dead Alert/Confirm components that depend on missing `<x-modal>` (#8)
- Replace `exit()` with proper `return Command::FAILURE` in `validateProject()` (#10)
- Re-throw exceptions in `step()` for critical steps (#11)
- Check exit codes in `runCommand()` and throw on failure (#12)
- Use `$path` parameter in `installPackages()` with `--working-dir` flag (#13)
- Update Docker Compose — MySQL 8.4, Meilisearch v1.12, modern defaults (#15)
- Fix `gitCommit()` to skip commit when no changes staged (#22)
- Preserve executable permissions in `copyRecursively()` for bin/ scripts (#23)
- Add `TwoFactorAuthenticatable` trait to stubs User model (#24)
- Fix Logout action return type for Livewire compatibility (#25)
- Add test overrides for starter kit compatibility (#26)

#### Enhancements

- Implement `toast()` helper with session flash (#16)
- Add `--dry-run`, `--skip-packages`, `--skip-npm` options to StartCommand (#18, #19)
- Improve package test coverage from 5 to 20 tests (#14)
- Consolidate `gitCommit()` to single final commit — ensures `.gitignore` files are in correct state
- Add `storage/debugbar/.gitignore` to stubs

#### Documentation

- Fix UUID documentation across 16 files to reflect dual int+UUID key pattern (#9)
- Document failure behavior, debugging with `-v` flag, and recovery steps (#20)
- Update CLAUDE.md with patterns from real projects, workflow conventions
- Consolidate Claude Code settings

**Full Changelog**: https://github.com/cleaniquecoders/kickoff/compare/1.8.0...1.9.0

## 1.8.0 - 2026-03-02

### What's Changed

#### Cleanup & Fixes

- **Remove unused team feature** — Removed `team_foreign_key`, `teams`, `team_resolver` config from Spatie Permission; removed team tables, relationships, and indexes from database instructions; replaced `Team::class` BelongsToMany example with `Tag::class`
- **Remove legacy/unused layout files** — Deleted `layouts/app/sidebar.blade.php` (legacy duplicate), `layouts/app/header.blade.php` and `components/layouts/app/header.blade.php` (unused header variants)
- **Fix layout namespace resolution** — Changed `layouts/app.blade.php` from `<x-layouts::app.sidebar>` to `<x-layouts.app.sidebar>` so it resolves to the custom component sidebar instead of the Laravel starter kit default. Fixes `Route [profile.edit] not defined` error.
- **Rewrite sidebar documentation** — Updated `docs/02-development/09-sidebar.md` to match actual menu builder classes (`Sidebar`, `UserManagement`, `MediaManagement`, `Settings`, `AuditMonitoring`) — old docs referenced non-existent classes

**Full Changelog**: https://github.com/cleaniquecoders/kickoff/compare/1.7.0...1.8.0

## 1.7.0 - 2026-02-28

### What's Changed

#### Migrate Settings UI from .env to Spatie Settings

Replaced runtime `.env` file writes with **Spatie Laravel Settings** for the admin Settings UI.

**Why:**

- `.env` should not be modified at runtime — it's a deployment-time config
- Eliminates race conditions from concurrent `.env` writes
- No more `config:clear` needed after saving settings
- Removes dangerous APP_ENV/APP_DEBUG toggles from the UI

**Changes:**

- Added `GeneralSettings`, `MailSettings`, `NotificationSettings` classes with settings migrations
  
- Settings UI now only exposes application-level settings:
  
  - **General**: Site Name
  - **Email**: From Address, From Name
  - **Notifications**: Enabled toggle, Channel selection
  
- Removed environment select, debug mode toggle, and SMTP credential fields from UI
  
- Deleted `env.php` helper (`update_env` / `update_env_multiple` no longer needed)
  
- Simplified notification config to static defaults (overridden at runtime by Spatie Settings)
  

**Full Changelog**: https://github.com/cleaniquecoders/kickoff/compare/1.6.0...1.7.0

## 1.6.0 - 2026-02-28

### What's Changed

#### New

- Add sandbox testing skill for local development workflow

#### Fixes

- Enforce PHP best practices across all stubs (87 files) — `declare(strict_types=1)`, policy fixes, security hardening, dead code removal
- Add return types to Livewire components, controllers, concerns, and model relations
- Remove redundant PHPDoc blocks from console commands
- Modernize Menu builder with native union types and fix variable shadowing bug
- Fix config defaults — Telescope disabled by default, MinIO region, seeder env defaults, notification config typo
- Delete broken stubs (TeamPolicy, Membership model)

## Added Self-Update CLAUDE.md Instruction - 2026-02-27

**Full Changelog**: https://github.com/cleaniquecoders/kickoff/compare/1.5.6...1.5.7

## Added git commit for each steps - 2026-02-21

- Added `gitCommit()` helper in support/helpers.php
- Git commit after each step + `restoreGitIgnoreFiles()` in `src/StartCommand.php`
- `stubs/todo.md` template (with `.gitignore` negation so it's tracked in the kickoff package)

## Remove Theme Toggle - 2026-02-20

**Full Changelog**: https://github.com/cleaniquecoders/kickoff/compare/1.5.4...1.5.5

## Remove Volt Related - 2026-01-22

**Full Changelog**: https://github.com/cleaniquecoders/kickoff/compare/1.5.3...1.5.4

## Simplify Docker Compose Setup - 2026-01-22

**Full Changelog**: https://github.com/cleaniquecoders/kickoff/compare/1.5.2...1.5.3

## Added Media Manager Integration - 2026-01-20

### Release Notes: v1.5.1

#### What's New

##### Media Manager Integration

This release adds full integration with the [`cleaniquecoders/media-manager`](https://github.com/cleaniquecoders/media-manager) package, providing a comprehensive media library management system with custom Flux UI components.

<img width="2048" height="646" alt="Media Manager Browser" src="https://github.com/user-attachments/assets/5f7d6a50-2bb5-43fc-b347-c32fbfd7c14e" />
#### Features
##### Media Management Module
- **Sidebar Navigation:** New "Media" section in sidebar with "Media Library" menu item
- **Custom Routes:** Dedicated `/media-manager` route with proper authentication and authorization
- **Access Control:** New permissions for media management (`media.access.management`, `media.upload.files`, `media.delete.files`)
##### Custom Flux-Styled Views
- **Media Browser:** Full-featured media browsing interface with grid and list views
- **Sidebar Filters:** Search, collection filter, type filter, and date range filters
- **Bulk Actions:** Select multiple items for bulk deletion
- **Preview Panel:** Flyout modal for previewing media details (images, videos, audio, PDFs, documents)
- **Empty States:** Polished empty state designs for both initial state and filtered results
##### UI Components Used
- Flux: `heading`, `button`, `button.group`, `input`, `select`, `modal`, `checkbox`, `badge`, `icon`, `callout`
- Tailwind CSS for card-like containers (replacing non-existent `flux:card`)
- Standard HTML tables for list view
#### Files Added
| File | Description |
|------|-------------|
| `stubs/app/Actions/Builder/Menu/MediaManagement.php` | Media menu builder |
| `stubs/routes/web/media.php` | Media manager routes |
| `stubs/resources/views/vendor/media-manager/browser.blade.php` | Main wrapper view |
| `stubs/resources/views/vendor/media-manager/livewire/media-browser.blade.php` | Livewire component view |
| `stubs/resources/views/vendor/media-manager/partials/grid-item.blade.php` | Grid item partial |
| `stubs/resources/views/vendor/media-manager/partials/list-item.blade.php` | List item partial |
| `stubs/resources/views/vendor/media-manager/partials/preview-panel.blade.php` | Preview modal partial |
#### Files Modified
| File | Changes |
|------|---------|
| `stubs/app/Actions/Builder/Menu.php` | Added MediaManagement import and match case |
| `stubs/resources/views/components/layouts/app/sidebar.blade.php` | Added media-management menu |
| `stubs/config/access-control.php` | Added media permissions and administrator role_scope |
| `stubs/app/Providers/AdminServiceProvider.php` | Added media management gates |
#### Dependencies
- Requires `cleaniquecoders/media-manager` ^1.0.1 (Livewire 4 compatible)
#### Upgrade Notes
After updating to v1.5.1, run:
```bash
php artisan reload:db
```
This will seed the new media permissions for your roles.
#### Verification
1. Log in as superadmin
2. Verify "Media" section appears in sidebar
3. Click "Media Library" to access `/media-manager`
4. Test grid/list view toggle, filters, and preview functionality
## 1.5.0 - 2026-01-19
### Release Notes - v1.5.0
**Release Date:** January 20, 2026
#### Overview
Version 1.5.0 is a major release that introduces a complete UI/UX overhaul with new dashboard, welcome page, security management features, notifications system, and improved navigation. This release also includes Livewire 4 upgrade and comprehensive documentation improvements.

---

#### New Features

##### Dashboard Redesign

- **Stats Overview Cards**: Display Total Users, Active Roles, Permissions, and Unread Notifications with visual icons
- **Quick Actions Panel**: One-click access to Edit Profile, Change Password, View Notifications, and Settings
- **Recent Activity Section**: Shows recent audit logs for quick monitoring
- **Personalized Welcome Message**: Greets user by name with contextual information

##### Landing Page (Welcome)

- **Modern Hero Section**: Clean design with "Powered by Kickoff" badge
- **Responsive Navigation**: Fixed navbar with authentication-aware links
- **Features Showcase**: Highlights Livewire 4, TailwindCSS v4, Laravel 12 stack
- **Call-to-Action**: "Get Started" button for new user registration

##### Notifications System

- **Bell Component** (`App\Livewire\Notifications\Bell`):
  
  - Header notification icon with unread count badge (99+ for large counts)
  - Dropdown showing 5 most recent unread notifications
  - Quick mark-as-read from dropdown
  - "View All Notifications" link
  
- **Notifications Management Page** (`App\Livewire\Notifications\Index`):
  
  - Full-page notification center at `/notifications`
  - Filter by: All, Unread, Read
  - Sortable by date (asc/desc)
  - Pagination (15 per page)
  - Bulk "Mark All as Read" action
  - Individual actions: Mark read/unread, Delete with confirmation
  - Visual highlighting for unread notifications
  - URL state persistence for filters and sorting
  
- **NotificationSeeder**: Seeds 5 sample notifications for superadmin user
  

##### Security & User Management

- **User Management** (`/security/users`):
  
  - User listing with roles display
  - Stats: Total Users, Active Today, With Roles, New This Month
  - User detail view with role assignment via `UserRoles` Livewire component
  
- **Role & Permission Management**:
  
  - `RolePermissions` Livewire component for toggling permissions per role
  - Module-based permission grouping with bulk toggle
  - Toast notifications for all actions
  - Authorization checks on all operations
  
- **Audit Trail** (`/security/audit-trail`):
  
  - Activity log viewer with pagination
  - Stats: Total Logs, Created, Updated, Deleted counts
  - Detailed audit view showing old/new values
  - User attribution for each action
  

##### Menu System Refactoring

- **New Structure**:
  
  - `Sidebar.php` - Dashboard and Notifications menu items
  - `UserManagement.php` - User management menu (renamed from Administration)
  - `AuditMonitoring.php` - Audit trail menu (renamed from Support)
  - `Settings.php` - Settings menu items
  
- **Removed**:
  
  - `Security.php` (AccessControl removed)
  - `SidebarFooter.php` (consolidated)
  

##### Layout Improvements

- **Sidebar**: Improved structure with dynamic user authentication elements
- **Header**: New desktop user menu component
- **Auth Layouts**: Added card, simple, and split layout variants
- **Partials**: New `head.blade.php` and `settings-heading.blade.php`

##### Logo & Branding

- **Kickoff Logo Component** (`x-kickoff-logo`): Rocket icon with gradient
- **App Logo Updates**: Uses Kickoff branding with project name
- **App Logo Icon**: Compact icon variant


---

#### Improvements

##### Livewire 4 Upgrade

- Updated all Livewire components to version 4 syntax
- Enhanced settings routes for Livewire 4 compatibility
- Updated profile settings component

##### Documentation

- **CLAUDE.md Added**: Comprehensive project guidance for Claude Code AI
  
  - Model conventions (extend `App\Models\Base`)
  - Database conventions (UUID primary keys)
  - Enum patterns with `InteractsWithEnum`
  - Authorization patterns with Spatie Permission
  - Helper functions reference
  - Testing patterns with Pest
  - Livewire patterns (alerts, confirmations)
  - DO's and DON'Ts checklist
  
- **Documentation Structure Updates**:
  
  - Added "Next Steps" navigation sections
  - Improved architecture overview
  - Enhanced development guides
  - Better contributing guidelines
  

##### Installation Process

- Added Livewire configuration during installation
- Added notifications table migration setup

##### Code Quality

- PHP Linting with Pint applied across codebase
- Improved code readability and maintainability


---

#### Bug Fixes

- Fixed URL for profile completion reminder in NotificationSeeder
- Fixed settings routes for profile and password in dashboard


---

#### Refactoring

- Renamed notification data key from `title` to `subject` for consistency
- Removed obsolete `VoltServiceProvider`
- Removed `AccessControlController` (replaced with Livewire components)
- Consolidated gate definitions for user management and auditing
- Refactored admin views structure (`administration` → `admin`)


---

#### Files Changed

##### New Files (Key)

- `stubs/CLAUDE.md`
- `stubs/app/Livewire/Actions/Logout.php`
- `stubs/app/Livewire/Admin/Roles/Index.php`
- `stubs/app/Livewire/Admin/Roles/Show.php`
- `stubs/app/Livewire/Admin/Settings/Show.php`
- `stubs/app/Livewire/Notifications/Bell.php`
- `stubs/app/Livewire/Notifications/Index.php`
- `stubs/app/Livewire/Security/RolePermissions.php`
- `stubs/app/Livewire/Security/UserRoles.php`
- `stubs/app/Actions/Builder/Menu/Settings.php`
- `stubs/app/Actions/Builder/Menu/Sidebar.php`
- `stubs/database/seeders/NotificationSeeder.php`
- `stubs/resources/views/welcome.blade.php`
- `stubs/resources/views/dashboard.blade.php`
- `stubs/resources/views/notifications/index.blade.php`
- `stubs/resources/views/security/users/index.blade.php`
- `stubs/resources/views/security/users/show.blade.php`
- `stubs/resources/views/security/audit-trail/index.blade.php`
- `stubs/resources/views/security/audit-trail/show.blade.php`
- `stubs/resources/views/components/kickoff-logo.blade.php`
- `stubs/resources/views/components/user-menu.blade.php`
- `stubs/resources/views/components/desktop-user-menu.blade.php`
- `stubs/routes/web/notifications.php`

##### Removed Files

- `stubs/app/Actions/Builder/Menu/Security.php`
- `stubs/app/Actions/Builder/Menu/SidebarFooter.php`
- `stubs/app/Http/Controllers/Security/AccessControlController.php`
- `stubs/app/Providers/VoltServiceProvider.php`
- `stubs/routes/web/security.php`

##### Assets Added

- `assets/dashboard.png`
- `assets/landing-features.png`
- `assets/landing-hero.png`
- `assets/role-permissions.png`
- `assets/settings-email.png`


---

#### Statistics

- **97 files changed**
- **+3,281 lines added**
- **-769 lines removed**


---

#### Upgrade Notes

Projects generated with Kickoff v1.5.0 will automatically include all new features. For existing projects upgrading from v1.4.x:

1. **Menu System**: Update menu builders to new structure
2. **Notifications**: Run `php artisan notifications:table && php artisan migrate`
3. **Livewire**: Update components to Livewire 4 syntax
4. **Routes**: Update security routes to new structure


---

**Full Changelog**: https://github.com/cleaniquecoders/kickoff/compare/1.4.2...1.5.0

## 1.4.2 - 2025-12-25

**Full Changelog**: https://github.com/cleaniquecoders/kickoff/compare/1.4.1...1.4.2

## Release Notes - Kickoff v1.4.1 - 2025-12-25

**Release Date:** December 25, 2024

### 🎉 What's New

#### Toast Notification System

- ✨ Added fully functional toast notification component with Alpine.js
- 🎨 Support for 4 notification types: success, error, warning, info
- 🌙 Dark mode support with proper color contrast
- ⚡ Auto-dismiss with configurable duration (default 3000ms)
- 🔄 Smooth animations and transitions

#### Settings Management

- 💾 Settings now persist to `.env` file (environment-based configuration)
  
- ✅ Full validation for all settings sections
  
- 🔐 Authorization with `manage.settings` gate
  
- 📧 **Enhanced Email Settings** with complete SMTP configuration:
  
  - Mail Driver (SMTP, Sendmail, Mailgun, SES, Log)
  - SMTP Host, Port, Username, Password
  - Encryption (TLS, SSL, None)
  - Sender information (From Address, From Name)
  - Organized in 2-column grid layout with helper text showing ENV keys
  
- 📋 Settings sections: General, Email, Notifications
  
- 🎯 Toast notifications for save/update feedback
  

#### Application Branding

- 🚀 New app logo component with Kickoff rocket icon
- 🎨 Dynamic project name display from `APP_NAME` env variable
- 🌓 Proper light/dark mode support with contrasting colors
- 💎 Clean design with white border and subtle shadow
- 📱 Responsive layout

#### Installation Improvements

- 🏷️ `.env.example` now uses project name placeholders
- ⚙️ Automatic replacement of `${PROJECT_NAME}` and `${OWNER}` during installation
- 🗄️ Database name automatically set to snake_case project name
- 📧 Superadmin email uses owner domain
- 🪣 MinIO bucket uses project name

### 🐛 Bug Fixes

#### Toast Notifications

- Fixed Livewire dispatch syntax (must use named parameters, not array)
- Fixed Alpine.js SVG icon rendering (`x-show` instead of `<template x-if>`)
- Added proper fallback classes for all toast states
- Fixed text visibility in both light and dark modes

#### Settings

- Replaced database-based settings with `.env` file approach
- Added proper `.env` file update helper functions
- Fixed settings not persisting after save

#### UI/UX

- Fixed app logo visibility in light mode
- Improved color contrast for better accessibility
- Changed from `brand-*` to `blue-*` colors for easier customization

### 🔧 Technical Changes

#### New Files

- `stubs/resources/views/components/toast.blade.php` - Toast notification component
- `stubs/resources/views/components/app-logo.blade.php` - Application logo component
- `stubs/support/env.php` - Environment file update helpers
- `stubs/docs/toast-notifications.md` - Toast documentation

#### Modified Files

- `stubs/.env.example` - Added project name placeholders
- `stubs/resources/views/livewire/admin/settings/show.blade.php` - Enhanced settings management
- `stubs/routes/web/administration.php` - Added settings authorization middleware
- `src/StartCommand.php` - Improved environment file setup
- `stubs/app/Providers/AdminServiceProvider.php` - Added administration gates

#### New Helper Functions

- `update_env(string $key, mixed $value)` - Update single env variable
- `update_env_multiple(array $data)` - Update multiple env variables

### 📝 Documentation

- 📝 Comprehensive documentation - reorganise the `docs/` based on context and priority.

#### Code Examples

All examples updated to use correct Livewire 3 named parameter syntax:

  ```php
  // ✅ Correct
$this->dispatch('toast',
type: 'success',
message: 'Success!',
duration: 3000
);

// ❌ Old (incorrect)
$this->dispatch('toast', [
'type' => 'success',
'message' => 'Success!'
]);








































  ```
### 💡 Migration Guide

From Previous Version

1. Toast Notifications: Update to use the new toast component: `$this->dispatch('toast', type: 'success', message: 'Saved!');`
2. Settings: Settings now persist to .env file automatically. No database table needed.
3. Branding: The app logo now uses `config('app.name')` automatically.

## Livewire Flux Integration & Development Tooling  - 2025-11-08

### 📋 Summary

The **version 1.4.0** introduces Livewire Flux package integration, refactors card components to use a new structured approach, and adds a comprehensive sandbox testing environment with Laravel Workbench. This represents a significant frontend modernization and a major improvement to the package development workflow.

### 📥 Installation

```bash
composer global require cleaniquecoders/kickoff









































```
### 🔗 Links

- **Full Changelog:** https://github.com/cleaniquecoders/kickoff/releases/tag/v1.4.0
- **Repository:** https://github.com/cleaniquecoders/kickoff

### 🎯 Type of Change

- ✨ Feature: Livewire Flux package integration
- 🔧 Refactor: Card component restructuring
- 🛠️ **Dev Tools: Sandbox testing environment**
- 📝 Documentation: Icon component additions

### 🔨 Technical Changes

#### 1. **Sandbox Testing Environment** ⭐ NEW

- **New Script**: sandbox - Automated testing utility for package development
- **Purpose**: Test `kickoff start` repeatedly without polluting git history
- **Git Integration**: Automatically manages `skip-worktree` flag for test-output
- **Test Directory**: sandbox - Isolated Laravel project for testing

**Sandbox Commands:**

```bash
bin/sandbox run          # Create fresh Laravel app + run kickoff start
bin/sandbox reset        # Delete sandbox and start clean









































```
**Key Features:**

- **Automated Workflow**: Creates Laravel app, applies kickoff, ready for inspection
- **Git-Safe**: Uses `skip-worktree` to prevent accidental commits of test files
- **Repeatable**: Fast iteration cycle for stub testing
- **Smart Cleanup**: Removes old sandbox before creating new one
- **Validation**: Checks for required Laravel installer and kickoff binary

**Developer Benefits:**

- Test package changes instantly without manual Laravel setup
- No more accidental commits of test-output changes
- Fast feedback loop during stub development
- Consistent testing environment for all contributors

#### 2. **Livewire Flux Integration**

- Integrated Livewire Flux package for enhanced UI components
- Provides modern, reactive component library for Laravel projects
- Improves developer experience with pre-built UI components

#### 3. **Card Component Refactoring**

- Introduced new `x-card` component structure
  
- Separated card into subcomponents:
  
  - `x-card` (main wrapper)
  - `x-card.header` (header section)
  - `x-card.body` (body content)
  - `x-card.footer` (footer actions)
  
- Updated views to use new component structure:
  
  - index.blade.php
  - show.blade.php
  - show.blade.php
  - show.blade.php
  

#### 4. **Icon Components Update**

- Added 13 custom Flux icon components in icon:
  
  - Lucide icon set (arrow-right-left, book-open-text, bug, chevrons-up-down, etc.)
  - System icons (settings, layout-dashboard, shield-check, log-out)
  - Navigation icons (layout-grid, folder-git-2, gauge)
  
- Replaced `@pure` directive with `@blaze` for better Flux compatibility
  
- All icons follow consistent Lucide design patterns
  

#### 5. **Development Tooling**

- **Laravel Workbench**: Added testbench.yaml configuration
- **Purpose**: Package development in isolation
- **Integration**: Enables testing kickoff as if it were installed globally
- Improved contributor onboarding with clear testing path

### 📁 Key Files Added/Modified

**New Files:**

- sandbox - **Sandbox testing script (183 lines)**
- testbench.yaml - Laravel Workbench configuration
- `stubs/resources/views/flux/icon/*.blade.php` - 13 new icon components
- `stubs/resources/views/components/card/*.blade.php` - 3 new subcomponents

**Modified Files:**

- card.blade.php - Main card component
- Multiple admin/livewire views updated with new card structure

### ✅ Benefits

1. **Massive DX Improvement**: Sandbox script reduces testing time from minutes to seconds
2. **Better Component Organization**: Structured card components improve maintainability
3. **Modern UI Framework**: Flux provides enterprise-grade components
4. **Git Hygiene**: Skip-worktree prevents test pollution in git history
5. **Contributor-Friendly**: New developers can test changes immediately with `bin/sandbox run`
6. **Consistency**: Standardized component patterns across the application
7. **Professional Workflow**: Matches industry best practices for package development

### 🧪 Testing Workflow

**Before (Manual):**

```bash
# 5-10 minutes per test cycle
cd ~/Projects
laravel new test-project
cd test-project
# manually edit composer.json to add local kickoff
composer install
kickoff start owner project
# inspect changes
# delete entire project
# repeat









































```
**After (Automated):**

```bash
# 30 seconds per test cycle
bin/sandbox run          # Creates Laravel + applies kickoff
# inspect test-output/sandbox
bin/sandbox reset        # Clean slate
# repeat instantly









































```
**Testing the Sandbox:**

```bash
# Create fresh Laravel app and apply kickoff
bin/sandbox run

# Inspect the generated project
cd test-output/sandbox
# create a database in mysql named `sandbox`









































```
Then create tables & seed data:

```bash
php artisan reload:db









































```
Run the sandbox app:

```bash
npm run build
php artisan serve









































```
To clean up sandbox, run:

```bash
bin/sandbox reset









































```
### 📦 Dependencies

**For Generated Projects:**

- Added: Livewire Flux package
- Updated: Related frontend dependencies

**For Package Development:**

- Laravel Workbench (dev)
- Laravel installer (global requirement documented)

### 🔄 Migration Path

**For Package Contributors:**

- Use `bin/sandbox run` instead of manual Laravel project creation
- Test-output directory automatically managed with skip-worktree

**For Generated Projects:**

1. Update card usage from single component to structured format
2. Replace `@pure` with `@blaze` in custom icon components
3. Leverage new Flux components for enhanced UI features

### 📚 Documentation

**Sandbox Documentation:**

- Inline comments in sandbox explain each command
- Usage instructions at script header
- Error messages guide missing dependencies

**Component Documentation:**

- Icon components follow Lucide design system
- Component structure documented in Blade files
- Copilot instructions remain up-to-date

### ⚠️ Breaking Changes

**For Package Users:**

- Card component structure changed (requires template updates)
- Directive change from `@pure` to `@blaze` (may affect custom implementations)

**For Contributors:**

- New testing workflow via sandbox script (old manual method still works)
- Test-output directory now ignored with skip-worktree

### 💡 Developer Experience Highlights

**Sandbox Script Innovation:**

- Solves the long-standing problem of testing package output
- Prevents "oops, committed test files" incidents
- Makes TDD possible for stub development
- Reduces barrier to contribution
- Professional package development workflow

**Impact on Development Speed:**

- **Before**: ~10 min per test iteration (manual setup/teardown)
- **After**: ~30 sec per test iteration (automated)
- **Time Saved**: ~95% reduction in testing overhead
- **Result**: More iterations = better quality stubs

## Laravel Sanctum - 2025-11-02

Added Laravel Sanctum

## 1.3.3 - 2025-11-02

### Release Notes - Kickoff v1.3.3

**Release Date:** November 2, 2025
**Package:** cleaniquecoders/kickoff

#### 📝 Overview

Version 1.3.3 is a documentation-focused release that enhances developer experience by providing comprehensive GitHub Copilot integration and improved project understanding.

#### ✨ What's New

##### 🤖 GitHub Copilot Integration

- **NEW:** Added comprehensive GitHub Copilot instructions file (`.github/copilot-instructions.md`)
  - Complete architecture documentation for AI-assisted development
  - Detailed command execution flow documentation
  - Helper function reference guide
  - Testing strategy and guidelines
  - Stub architecture explanation
  - Common development tasks with code examples
  - Important gotchas and best practices
  

##### 📚 Documentation Improvements

- Enhanced developer onboarding with AI-powered code assistance
- Comprehensive package architecture documentation
- Clear separation between package structure and generated project stubs
- Detailed explanation of placeholder replacement system
- Added examples for extending functionality

#### 🎯 Benefits

##### For Contributors

- Faster onboarding with AI-assisted code understanding
- Clear guidelines for adding new features
- Comprehensive testing patterns documented
- Better understanding of stub vs package structure

##### For Users

- Better understanding of what Kickoff generates
- Clear documentation of all helper functions
- Improved troubleshooting with detailed workflow docs

#### 📦 Package Information

- **Supported Laravel Versions:** 10.x, 11.x, 12.x
- **PHP Version:** ^8.2
- **Installation:** `composer global require cleaniquecoders/kickoff`
- **Usage:** `kickoff start <owner> <project-name> [<project-path>]`

#### 🔧 Technical Details

##### File Changes

- Added: `.github/copilot-instructions.md` (10,720 lines of comprehensive documentation)

##### No Breaking Changes

This release is purely additive and does not introduce any breaking changes.

#### 📖 Documentation Coverage

The new Copilot instructions document covers:

1. **Architecture Overview** - Package structure and execution flow
2. **Development Conventions** - Code style, formatting, and testing
3. **Helper Functions** - Complete API reference for CLI utilities
4. **Stubs Architecture** - Explanation of template system
5. **Composer Configuration** - Package distribution and scripts
6. **Command Execution Flow** - Step-by-step workflow breakdown
7. **Testing Guidelines** - PHPUnit patterns and best practices
8. **Common Development Tasks** - How-to guides for extending Kickoff
9. **Important Gotchas** - Common pitfalls and warnings

#### 🔗 Links

- **Repository:** [https://github.com/cleaniquecoders/kickoff](https://github.com/cleaniquecoders/kickoff)
- **Full Changelog:** [https://github.com/cleaniquecoders/kickoff/compare/1.3.2...1.3.3](https://github.com/cleaniquecoders/kickoff/compare/1.3.2...1.3.3)
- **Issues:** [https://github.com/cleaniquecoders/kickoff/issues](https://github.com/cleaniquecoders/kickoff/issues)

#### 🙏 Credits

**Maintained by:** CleaniqueCoders (Nasrul Hazim)
**Based on:** [Project Template](https://github.com/nasrulhazim/project-template)

#### 📦 Installation

##### Global Installation (Recommended)

```bash
composer global require cleaniquecoders/kickoff











































```
##### Update from Previous Version

```bash
composer global update cleaniquecoders/kickoff











































```
#### 🚀 Quick Start

After installation, create a new Laravel project and run:

```bash
cd your-laravel-project
kickoff start your-owner your-project-name











































```
For verbose output:

```bash
kickoff start your-owner your-project-name -vvv











































```
#### 🔮 What's Next?

See our [TODO list](https://github.com/cleaniquecoders/kickoff/blob/main/todo.md) for upcoming features:

- Rollback mechanism for failed setups
- Interactive package selection mode
- Custom stub directory support
- Laravel 12 compatibility testing
- Integration test suite

#### 📝 Upgrade Notes

No action required for upgrading from v1.3.2 to v1.3.3. Simply update the package globally.


---

**Previous Release:** [v1.3.2](https://github.com/cleaniquecoders/kickoff/releases/tag/1.3.2) - October 21, 2025

## 1.3.2 - 2025-10-21

**Full Changelog**: https://github.com/cleaniquecoders/kickoff/compare/1.3.1...1.3.2

## 1.3.1 - 2025-10-21

**Full Changelog**: https://github.com/cleaniquecoders/kickoff/compare/1.3.0...1.3.1

## Improvement on Menu, Sidebar and Components. - 2025-10-21

**Release Date:** October 21, 2025

### About Kickoff

Kickoff helps configure your new Laravel project with good practices, providing a comprehensive set of stubs, configurations, and utilities to jumpstart Laravel application development.

### What's New in v1.3.0

#### 🔄 Updates

- Updated project stubs and access control configurations
- Update Access Control naming conventions.
- Improve documentations
- Added Default GitHub Copilot Instructions
- Improve Menu builder with header label & icon, and also authorization.

## 1.2.7 - 2025-08-14

**Full Changelog**: https://github.com/cleaniquecoders/kickoff/compare/1.2.6...1.2.7

## Update stubs - 2025-08-14

**Full Changelog**: https://github.com/cleaniquecoders/kickoff/compare/1.2.5...1.2.6

## 1.2.5 - 2025-08-06

**Full Changelog**: https://github.com/cleaniquecoders/kickoff/compare/1.2.4...1.2.5

## 1.2.4 - 2025-08-06

**Full Changelog**: https://github.com/cleaniquecoders/kickoff/compare/1.2.3...1.2.4

## 1.2.3 - 2025-08-06

**Full Changelog**: https://github.com/cleaniquecoders/kickoff/compare/1.2.2...1.2.3

## 1.2.2 - 2025-08-06

**Full Changelog**: https://github.com/cleaniquecoders/kickoff/compare/1.2.1...1.2.2

## 1.2.1 - 2025-08-03

**Full Changelog**: https://github.com/cleaniquecoders/kickoff/compare/1.2.0...1.2.1
