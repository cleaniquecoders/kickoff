# 🚀 Laravel Starter

This script bootstraps a **new Laravel project** with all the essential tools, configs, and workflows you need for a modern and maintainable setup.

## ✨ Features

- 📦 Installs required Laravel packages:
  - [spatie/laravel-permission](https://spatie.be/docs/laravel-permission)
  - [spatie/laravel-medialibrary](https://spatie.be/docs/laravel-medialibrary)
  - [cleaniquecoders/traitify](https://github.com/cleaniquecoders/traitify)
  - [cleaniquecoders/laravel-media-secure](https://github.com/cleaniquecoders/laravel-media-secure)
  - [owen-it/laravel-auditing](https://laravel-auditing.com)

- 🛠 Dev tools:
  - [barryvdh/laravel-debugbar](https://github.com/barryvdh/laravel-debugbar)
  - [larastan/larastan](https://github.com/nunomaduro/larastan) for static analysis
  - [driftingly/rector-laravel](https://github.com/rectorphp/rector-laravel)
  - [pestphp/pest-plugin-arch](https://pestphp.com) for architecture testing

- ⚙️ Configuration:
  - Adds QA scripts to `composer.json`
  - Autoloads `support/helpers.php`
  - Creates `rector.php`, `phpstan.neon.dist`, and `pint.json`

- 📂 Project Structure:
  - `support/` for helpers
  - Refactored `routes/` into `web/`, `api/`, and `console/` subfolders (backups included)
  - `tinker/` with `.gitignore`
  - `docs/README.md` with a placeholder TOC

- 🛡️ Automation Scripts:
  - Creates executable scripts in `bin/` for tasks like backup, deployment, PHPStan reporting, dependency updates, and project install
  - All scripts use the current directory name as the project name

- ✅ Testing:
  - Generates `tests/Feature/ArchitectureTest.php` using Pest Arch

- 📝 Documentation:
  - Creates `CHANGELOG.md`, `CONTRIBUTING.md`, `CODE_OF_CONDUCT.md`, `SECURITY.md`, `SUPPORT.md`, and `LICENSE.md`

- ⚡ GitHub Actions Workflows:
  - Pint (Laravel Pint)
  - PHPStan
  - Rector
  - Tests (Pest)
  - Changelog updater

- 🔧 Artisan & Project Tasks:
  - Clears config and view caches
  - Runs migrations
  - Creates storage symlink
  - Publishes package configs and migrations

- 🛠️ Infrastructure Templates:
  - Sets up `.config/` with Nginx and Supervisor templates

## 📥 Usage

> ⚠️ This script is for **new Laravel projects only**. Do not run on an existing project unless you know what you’re doing.

Run the script in your fresh Laravel project root:

```bash
curl -s https://raw.githubusercontent.com/cleaniquecoders/laravel-starter/main/configure.php | php
```
