[![PHP Linting (Pint)](https://github.com/cleaniquecoders/laravel-starter/actions/workflows/lint.yml/badge.svg)](https://github.com/cleaniquecoders/laravel-starter/actions/workflows/lint.yml) [![Test](https://github.com/cleaniquecoders/laravel-starter/actions/workflows/run-tests.yml/badge.svg)](https://github.com/cleaniquecoders/laravel-starter/actions/workflows/run-tests.yml)

# 🚀 Kickoff

This package bootstraps a **new Laravel project** with all the essential tools, configs, and workflows you need for a modern and maintainable setup.

The setup is based on this repository - [Project Template](https://github.com/nasrulhazim/project-template).

## 📥 Usage

> ⚠️ This script is for **new Laravel projects only**. Do not run on an existing project unless you know what you’re doing.

Install this package at global:

```bash
composer global require cleaniquecoders/kickoff
```

Then run:

```bash
kickoff start <owner> <project-name> <project-path>
```

> By default it will use current directory `<project-path>` is optional. It will use current directory path. `<project-path>` can accept relative or absolute path to the project.

Example:

```bash
kickoff start nasrulhazim project-template

🎉 Let's kickoff your nasrulhazim/project-template now!

⏳ Copy application stubs... ✅
⏳ Update composer.json for helper, config plugins and scripts... ✅
⏳ Update project name in bin/ directory... ✅
⏳ Update README... ✅
⏳ Update .env.example... ✅
⏳ Changing to project directory...... ✅
⏳ Installing required packages... ✅
⏳ Publishing package configs & migrations... ✅
⏳ Install tippy.js... ✅
⏳ Building application... ✅

🎉 Project setup completed successfully!
```

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

## 🛡️ Automation Scripts

- Creates executable scripts in `bin/` for common project tasks:
  - **backup-app**: Backup your application files
  - **backup-media**: Backup only media files changed in the last 24 hours
  - **build-fe-assets**: Build and commit frontend assets
  - **deploy**: Deploy code to your server with branch/tag support
  - **update-dependencies**: Update Composer and npm dependencies, audit and build assets
  - **reinstall-npm**: Remove and reinstall npm modules and lock file
  - **install**: Project initialization, database setup, and environment configuration
  - **phpstan**: Run PHPStan and generate readable reports per identifier

- All scripts use the current directory name as the project name for dynamic configuration.
- Scripts are made executable and can be run directly from the `bin/` directory.


## Security Vulnerabilities

If you discover a security vulnerability within AirBox, please send an e-mail to Nasrul Hazim via [nasrulhazim.m@gmail.com](mailto:nasrulhazim.m@gmail.com). All security vulnerabilities will be promptly addressed.

## Contributors

<a href="https://github.com/cleaniquecoders/kickoff/graphs/contributors">
  <img src="https://contrib.rocks/image?repo=cleaniquecoders/kickoff"  alt="project-template Contributors"/>
</a>

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
