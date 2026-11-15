# Dependency Audit

How this project keeps its Composer and npm dependencies free of known vulnerabilities.

## Audit the lock, not a fresh resolve

Production installs exactly what `composer.lock` pins. Audit that file:

```bash
composer audit --locked
```

**Never** run `composer update` before auditing. It re-resolves to the newest allowed versions,
so the audit checks a dependency set nobody deploys. It passes while the committed lock
is still vulnerable. `.github/workflows/security.yml` follows this rule: it audits the lock
first, then runs `composer install` (not `update`) for PHPStan.

## What runs automatically

| Mechanism | When | What it does |
|---|---|---|
| `security.yml` | Every push and pull request | `composer audit --locked` + PHPStan |
| `security.yml` schedule | Weekly (Monday 01:00 UTC) | Catches advisories published after the last commit, even in a repo with no pushes |
| Dependabot (`.github/dependabot.yml`) | Weekly | Opens PRs for Composer and npm patch/minor updates (grouped), monthly for GitHub Actions |

## Fixing an advisory

1. Find the affected range and fixed version: `composer audit --locked --format=json`.
2. Update only what is needed:
   ```bash
   composer update vendor/package --with-dependencies
   ```
   Transitive packages (e.g. `mtdowling/jmespath.php` via `aws/aws-sdk-php`) are updated the same way.
3. If a `composer.json` constraint blocks the fix, check `composer why-not vendor/package x.y.z`.
   Raise the constraint within the same major. A major bump is an upgrade, so plan it as one.
4. Run the test suite. No new failures compared with before the update.
5. `composer audit --locked` again, then commit `composer.lock` (and `composer.json` if changed).

## Triage: is the vulnerable code reachable?

An advisory in the lock is always fixed. But reachability decides **how fast**:

- **Reachable** (the vulnerable class/feature is used): patch now, ship a release.
- **Not reachable**: patch in the next routine release.

Example: CVE-2026-114509 (`laravel/framework` `TokenGuard` auth bypass) only affects
apps with a guard using `'driver' => 'token'` in `config/auth.php`. Apps on `session` +
Sanctum still get flagged and still update, but are not exposed.

Severity that the audit leaves blank is **unknown**. Treat it as unknown, not as low.
