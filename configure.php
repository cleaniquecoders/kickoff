<?php

define('BASE_PATH', __DIR__);
define('PROJECT_NAME', basename(BASE_PATH));

/**
 * Elegant Project Setup Script
 * 
 * This script automates the following for a Laravel project:
 * - Installs required and development Composer packages
 * - Updates composer.json with scripts and helpers autoload
 * - Creates configuration files: rector.php, phpstan.neon.dist, pint.json
 * - Sets up directories: support/, routes/, tinker/, docs/
 * - Generates support/helpers.php with auto-loader
 * - Organizes route files into subdirectories
 * - Creates todo.md and updates .gitignore
 * - Sets up tinker/ and docs/README.md with TOC
 * - Creates executable bin/ scripts for project automation
 * - Sets up .config directory with Nginx and Supervisor templates
 * - Generates tests/Feature/ArchitectureTest.php with Pest Arch rules
 * - Creates documentation templates (CHANGELOG, CONTRIBUTING, etc.)
 * - Publishes package configs and migrations via artisan
 * - Creates GitHub Actions workflow YAMLs for CI/CD
 * - Runs artisan tasks: config:clear, migrate, storage:link
 * - Outputs clean progress and completion messages
 */
function step(string $message, callable $callback)
{
    echo "⏳ $message...";
    try {
        $callback();
        echo " ✅\n";
    } catch (Exception $e) {
        echo " ❌\n";
    }
}

/**
 * Run script silently.
 */
function runSilent(string $cmd)
{
    shell_exec("$cmd > /dev/null 2>&1");
}

/**
 * Install Composer packages.
 */
function installPackages(array $require, array $requireDev): void
{
    if ($require) {
        runSilent('composer require '.implode(' ', $require));
    }
    if ($requireDev) {
        runSilent('composer require --dev '.implode(' ', $requireDev));
    }
}

/**
 * Create a directory if it does not exist.
 */
function ensureDir(string $path, int $mode = 0755): void
{
    if (! is_dir($path)) {
        @mkdir($path, $mode, true);
    }
}

/**
 * Create a file with content if it does not exist.
 */
function ensureFile(string $path, string $content): void
{
    if (! file_exists($path)) {
        file_put_contents($path, $content);
    }
}

/**
 * Create or update a file with content.
 */
function putFile(string $path, string $content): void
{
    file_put_contents($path, $content);
}

/**
 * Setup a directory and file.
 */
function setupDirAndFile(string $dir, string $file, string $content): void
{
    ensureDir($dir);
    ensureFile($file, $content);
}

/**
 * Create bash scripts
 */
function createBinScript(string $name, string $content): void
{
    $binDir = BASE_PATH.'/bin';
    ensureDir($binDir);
    $path = "$binDir/$name";
    ensureFile($path, $content);
    @chmod($path, 0755);
}

/**
 * Replace all ${PROJECT_NAME} in a string with the PROJECT_NAME constant.
 */
function replaceProjectName(string $content): string
{
    return str_replace('${PROJECT_NAME}', PROJECT_NAME, $content);
}

// ------------------------------------------------------------
// 1. Install packages
// ------------------------------------------------------------
step('Installing required packages', function () {
    $require = [
        'spatie/laravel-permission',
        'spatie/laravel-medialibrary',
        'cleaniquecoders/traitify',
        'cleaniquecoders/laravel-media-secure',
        'owen-it/laravel-auditing',
    ];
    $requireDev = [
        'barryvdh/laravel-debugbar',
        'larastan/larastan',
        'driftingly/rector-laravel',
        'pestphp/pest-plugin-arch',
    ];
    installPackages($require, $requireDev);
});

// ------------------------------------------------------------
// 2. Update composer.json with scripts & helpers autoload
// ------------------------------------------------------------
step('Updating composer.json', function () {
    $composerFile = BASE_PATH.'/composer.json';
    $composer = json_decode(file_get_contents($composerFile), true);

    $composer['autoload']['files'] = ['support/helpers.php'];
    $composer['scripts'] = [
        'analyse' => '@php vendor/bin/phpstan analyse',
        'test' => '@php vendor/bin/pest',
        'test-arch' => '@php vendor/bin/pest tests/Feature/ArchitectureTest.php',
        'test-coverage' => 'vendor/bin/pest --coverage',
        'format' => '@php vendor/bin/pint',
        'lint' => '@php vendor/bin/phplint',
        'rector' => 'vendor/bin/rector process',
    ];

    putFile($composerFile, json_encode($composer, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    runSilent('composer dump-autoload');
});

// ------------------------------------------------------------
// 3. Create configs
// ------------------------------------------------------------
step('Creating rector.php', function () {
    ensureFile(BASE_PATH.'/rector.php', "<?php\n\n// Rector config\n");
});
step('Creating phpstan.neon.dist', function () {
    ensureFile(BASE_PATH.'/phpstan.neon.dist', "parameters:\n  level: 6\n  paths:\n    - app/\n");
});
step('Creating pint.json', function () {
    ensureFile(BASE_PATH.'/pint.json', "{\n    \"preset\": \"laravel\"\n}\n");
});

// ------------------------------------------------------------
// 4a. Setup directories: support/, routes/, tinker/, docs/
// ------------------------------------------------------------
step('Setting up support/helpers.php', function () {
    $helpersDir = BASE_PATH.'/support';
    $helpersFile = $helpersDir.'/helpers.php';
    $content = <<<'PHP'
<?php

if (! function_exists('require_all_in')) {
    /**
     * Require all files in the given path.
     *
     * @param string $path File path pattern. eg. routes/web/*.php
     * @return void
     */
    function require_all_in(string $path): void
    {
        collect(glob($path))
            ->each(function ($path) {
                if (basename($path) !== basename(__FILE__)) {
                    require $path;
                }
            });
    }
}

// Auto-load all helpers in support/
require_all_in(__DIR__.'/*.php');
PHP;
    setupDirAndFile($helpersDir, $helpersFile, $content);
});

step('Organising routes', function () {
    $routesDir = BASE_PATH.'/routes';
    foreach (['web', 'api', 'console'] as $dir) {
        $subDir = "$routesDir/$dir";
        ensureDir($subDir);
        $mainFile = "$routesDir/$dir.php";
        if (file_exists($mainFile)) {
            copy($mainFile, "$subDir/_.php");
        }
        putFile($mainFile, "<?php\n\nrequire_all_in(base_path('routes/$dir/*.php'));\n");
    }

    $authFile = "$routesDir/auth.php";
    if (file_exists($authFile)) {
        copy($authFile, "$routesDir/web/auth.php");
        unlink($authFile);
    }
});

step('Creating todo.md', function () {
    ensureFile(BASE_PATH.'/todo.md', "# TODO\n");
    file_put_contents(BASE_PATH.'/.gitignore', "\ntodo.md\n", FILE_APPEND);
});

step('Creating tinker/ with .gitignore', function () {
    $tinkerDir = BASE_PATH.'/tinker';
    ensureDir($tinkerDir);
    ensureFile("$tinkerDir/.gitignore", "*\n!.gitignore\n");
});

step('Creating docs/README.md with TOC', function () {
    $docsDir = BASE_PATH.'/docs';
    ensureDir($docsDir);
    ensureFile("$docsDir/README.md", "# Project Documentation\n\n- Getting Started\n- TOC goes here\n");
});

// ------------------------------------------------------------
// 4b. Setup bin/ script
// ------------------------------------------------------------
$scripts = [
    'backup-app' => <<<'BASH'
#!/bin/bash

# Set source and destination directories
source_dir="/var/www/${PROJECT_NAME}"
backup_dir="/home/${PROJECT_NAME}/backup"

# Create backup directory if it doesn't exist
mkdir -p "$backup_dir"

# Create a unique timestamp for the backup file
timestamp=$(date +"%Y%m%d")

# Initialize incremental value
incremental=0

# Function to generate the zip file name
generate_zip_name() {
  if [ "$incremental" -eq 0 ]; then
    echo "${timestamp}-${PROJECT_NAME}-app.zip"
  else
    echo "${timestamp}-${PROJECT_NAME}-app-${incremental}.zip"
  fi
}

# Check if the zip file already exists, and increment the value if necessary
while [ -e "$backup_dir/$(generate_zip_name)" ]; do
  ((incremental++))
done

# Create the zip file
zip_file="$backup_dir/$(generate_zip_name)"
zip -r "$zip_file" "$source_dir"

chown -R ${PROJECT_NAME}:${PROJECT_NAME} "$backup_dir"

echo "Backup completed: $zip_file"
BASH,
    'backup-media' => <<<'BASH'
#!/bin/bash

# Set source and destination directories
source_dir="/var/www/${PROJECT_NAME}/storage/media"
backup_dir="/home/${PROJECT_NAME}/backup/media"

# Create backup directory if it doesn't exist
mkdir -p "$backup_dir"

# Create a unique timestamp for the backup file
timestamp=$(date +"%Y%m%d")

# Initialize incremental value
incremental=0

# Function to generate the zip file name
generate_zip_name() {
  if [ "$incremental" -eq 0 ]; then
    echo "${timestamp}-${PROJECT_NAME}-media.zip"
  else
    echo "${timestamp}-${PROJECT_NAME}-media-${incremental}.zip"
  fi
}

# Check if the zip file already exists, and increment the value if necessary
while [ -e "$backup_dir/$(generate_zip_name)" ]; do
  ((incremental++))
done

# Find files modified in the last 24 hours and create the zip file
find "$source_dir" -type f -mtime -1 -exec zip -q -r "$backup_dir/$(generate_zip_name)" {} +

echo "Media backup completed: $backup_dir/$(generate_zip_name)"
BASH,
    'build-fe-assets' => <<<'BASH'
#!/usr/bin/env bash

npm run build

# Check if there are changes in the public/ directory
if [ -n "$(git status --porcelain public/)" ]; then
  git add public/
  git commit -m "Update FE Assets"
  echo "Successfully compiled and committed FE Assets"
else
  echo "No changes in public/ directory. Nothing to commit."
fi

echo ""
BASH,
    'deploy' => <<<'BASH'
#!/usr/bin/env bash

BRANCH=""
REMOTE="origin"
PROJECT_PATH="/var/www/${PROJECT_NAME}"
COMPOSER_ALLOW_SUPERUSER=1

# Function to display usage
usage() {
    echo "Usage: $0 [-b branch] [-r remote] [-p project_path]"
    exit 1
}

# Function to get the latest tag
get_latest_tag() {
    git fetch --tags
    LATEST_TAG=$(git describe --tags $(git rev-list --tags --max-count=1))
    echo $LATEST_TAG
}

# Parse command line arguments for branch, remote, and project path
while getopts "b:r:p:" flag
do
    case "${flag}" in
        b) BRANCH=${OPTARG};;
        r) REMOTE=${OPTARG};;
        p) PROJECT_PATH=${OPTARG};;
        *) usage;;  # Handle invalid flag
    esac
done

if [ ! -d "$PROJECT_PATH" ]; then
    echo "$PROJECT_PATH does not exist. Nothing to do."
    exit 1
fi

clear

if [ -z "$BRANCH" ]; then
    echo "No branch provided, using the latest tag."
    BRANCH=$(get_latest_tag)
fi

echo "🚀 Deploying codes for branch/tag $BRANCH from remote $REMOTE at path $PROJECT_PATH"

echo "Navigate to $PROJECT_PATH"
cd "$PROJECT_PATH" || exit

echo "Pulling codes"
git checkout "$BRANCH"
git pull "$REMOTE" "$BRANCH"

echo "Install dependencies"
y | composer install

echo "Clear config caches"
php artisan config:clear

echo "Clear view caches"
php artisan view:clear

echo "Running migration"
php artisan migrate --force

echo "Restart Horizon"
php artisan horizon:terminate

echo "Change project ownership to web server"
chown nginx:nginx "$PROJECT_PATH" -R

clear
echo ""
echo "🚀 Deployed codes for $BRANCH from remote $REMOTE at path $PROJECT_PATH"
BASH,
    'update-dependencies' => <<<'BASH'
#!/usr/bin/env bash

# Exit script if any command fails
set -e

# Exit script if an unset variable is used
set -u

# Update Composer dependencies
composer update

# Upgrade npm packages and fix any vulnerabilities
npm upgrade
npm audit fix --force

# Build the assets
npm run build

# Check if there are any changes to commit
if [[ -n $(git status --porcelain) ]]; then
    # Stage the updated files
    git add composer.json composer.lock package.json public/build

    # Commit the changes
    git commit -m "Update dependencies"

    # Inform the user of success
    echo "You have successfully updated the dependencies! 🎉🎉🎉"
else
    # Inform the user that there is nothing to commit
    echo "Nothing to do here."
fi
BASH,
    'reinstall-npm' => <<<'BASH'
#!/usr/bin/env bash

LOCK_FILE=package-lock.json
MODULE_DIR=node_modules/

rm $LOCK_FILE
rm -fr $MODULE_DIR

npm upgrade
npm audit fix --force
npm install
npm run build
BASH,
    'install' => <<<'BASH'
#!/usr/bin/env bash

# Get the current directory name
CURRENT_DIR_NAME=$(basename "$PWD")

# Convert the directory name to snake_case (e.g., loop-tag -> loop_tag)
DB_DATABASE=$(echo "$CURRENT_DIR_NAME" | tr '-' '_' | sed -r 's/([A-Z])/_\L\1/g' | sed 's/^_//')

# Convert the directory name to kebab-case (e.g., loop_tag -> loop-tag)
KEBAB_NAME=$(echo "$DB_DATABASE" | tr '_' '-')

# Convert the directory name to Title Case (e.g., loop-tag -> Loop Tag)
APP_NAME=$(echo "$CURRENT_DIR_NAME" | tr '-' ' ' | awk '{for(i=1;i<=NF;i++) $i=toupper(substr($i,1,1)) tolower(substr($i,2))}1')

# Database credentials (use environment variables if available)
DB_USER=${DB_USERNAME:-root}
DB_PASS=${DB_PASSWORD}
DB_HOST=${DB_HOST:-localhost}

# Create the database if it does not exist
echo "Checking if database '$DB_DATABASE' exists..."
MYSQL_CMD="mysql -u$DB_USER -h$DB_HOST"
if [ -n "$DB_PASS" ]; then
    MYSQL_CMD+=" -p$DB_PASS"
fi

if ! $MYSQL_CMD -e "USE $DB_DATABASE;" 2>/dev/null; then
    echo "Database '$DB_DATABASE' does not exist. Creating..."
    $MYSQL_CMD -e "CREATE DATABASE $DB_DATABASE;"
else
    echo "Database '$DB_DATABASE' already exists."
fi

# Update .env.example with the current directory name
sed -i.bak "s/^APP_NAME=.*/APP_NAME=\"$APP_NAME\"/" .env.example
sed -i.bak "s/^DB_DATABASE=.*/DB_DATABASE=$DB_DATABASE/" .env.example
rm -f .env.example.bak  # Remove backup

# Commit the changes to .env.example
git add .env.example
git commit -m "Update .env.example with APP_NAME=\"$APP_NAME\" and DB_DATABASE=$DB_DATABASE"

# Install composer dependencies if not already installed
if [ ! -d vendor/ ]; then
    composer install
    if [ -f ./update-dependencies ]; then
        bash ./update-dependencies
    fi
fi

# Install and build npm dependencies if not already installed
if [ ! -d node_modules/ ]; then
    npm upgrade
    npm audit fix --force
    npm run build

    # Commit changes to public/ if any
    if [ -n "$(git status public/ --porcelain)" ]; then
        git add public/
        git commit -m "Update public/ directory after build"
    fi
fi

# Copy .env.example to .env if not already present
if [ ! -f .env ]; then
    cp .env.example .env
    php artisan key:generate
fi
BASH,
    'phpstan' => <<<'BASH'
#!/usr/bin/env bash

clear

echo "Running PHPStan..."
vendor/bin/phpstan --error-format=json > .phpstan/scan-result.json

jq . .phpstan/scan-result.json > .phpstan/scan-result.pretty.json && mv .phpstan/scan-result.pretty.json .phpstan/scan-result.json

input_file=".phpstan/scan-result.json"
output_dir=".phpstan"

if [[ ! -f "\$input_file" ]]; then
  echo "❌ File \$input_file not found."
  exit 1
fi

find "\$output_dir" -type f -name '*.txt' -delete

# Validate if the JSON has a "files" key and it's not null
if ! jq -e '.files != null and (.files | length > 0)' "\$input_file" >/dev/null; then
  echo "ℹ️ No issues found or invalid PHPStan JSON output."
  exit 0
fi

mkdir -p "\$output_dir"

echo "📂 Output directory ready: \$output_dir"
echo "📄 Reading from: \$input_file"

jq -r '
  .files as \$files |
  \$files | to_entries[] |
  .key as \$file |
  .value.messages[] |
  [
    .identifier,
    \$file,
    (.line | tostring),
    .message,
    (if (.tip != null and (.tip | type) == "string") then .tip else "" end),
    (if (.ignorable == true) then "Yes" else "No" end)
  ] | @tsv
' "\$input_file" |
while IFS=\$'\t' read -r identifier file line message tip ignorable; do
  output_file="\${output_dir}/\${identifier}.txt"
  {
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
    echo "📂 File       : \$file"
    echo "🔢 Line       : \$line"
    echo "💬 Message    : \$message"
    [[ -n "\$tip" ]] && echo "💡 Tip        : \$tip"
    echo "✅ Ignorable  : \$ignorable"
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
    echo ""
  } >> "\$output_file"
done

echo "✅ PHPStan scan identifiers outputted into individual files."

# Generate summary
summary_file="\${output_dir}/summary.txt"

# Define label width (adjust if needed)
label_width=42

total_issues=0
total_identifiers=0

# Temp file to collect identifier and count lines
temp_summary_data=\$(mktemp)

# Loop through all identifier files
for file in "\$output_dir"/*.txt; do
  [[ "\$file" == "\$summary_file" ]] && continue

  identifier=\$(basename "\$file" .txt)
  count=\$(grep -c "📂 File" "\$file")

  printf -- "- %-\${label_width}s : %4d\n" "\$identifier" "\$count" >> "\$temp_summary_data"

  total_issues=\$((total_issues + count))
  total_identifiers=\$((total_identifiers + 1))
done

# Write summary file using grouped commands
{
  echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
  echo "🔎 PHPStan Scan Summary"
  echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
  printf -- "- %-\${label_width}s  : %4d\n" "Unique Identifiers" "\$total_identifiers"
  printf -- "- %-\${label_width}s : %4d\n" "Issues Found" "\$total_issues"
  echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
  echo ""
  echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
  echo "📋 Issues by Identifier:"
  echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
  sort "\$temp_summary_data"
  echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
} > "\$summary_file"

echo "📊 Summary written to \$summary_file"

# Clean up
rm -f "\$temp_summary_data"
BASH,
];
foreach ($scripts as $name => $content) {
    step("Setting up bin/$name script", function () use ($name, $content) {
        createBinScript($name, replaceProjectName($content));
    });
}

// ------------------------------------------------------------
// 4c. Setup .config
// ------------------------------------------------------------
step('Setting up .config', function () {
    $configurations = [
        'minio.nginx.conf' => <<<'BASH'
user  nginx;
worker_processes  auto;

error_log  /var/log/nginx/error.log warn;
pid        /var/run/nginx.pid;

events {
    worker_connections  4096;
}

http {
    include       /etc/nginx/mime.types;
    default_type  application/octet-stream;

    log_format  main  '$remote_addr - $remote_user [$time_local] "$request" '
                      '$status $body_bytes_sent "$http_referer" '
                      '"$http_user_agent" "$http_x_forwarded_for"';

    access_log  /var/log/nginx/access.log  main;
    sendfile        on;
    keepalive_timeout  65;

    # include /etc/nginx/conf.d/*.conf;

    upstream minio {
        server minio1:9000;
        server minio2:9000;
        server minio3:9000;
        server minio4:9000;
    }

    upstream console {
        ip_hash;
        server minio1:9001;
        server minio2:9001;
        server minio3:9001;
        server minio4:9001;
    }

    server {
        listen       9000;
        listen  [::]:9000;
        server_name  localhost;

        # To allow special characters in headers
        ignore_invalid_headers off;
        # Allow any size file to be uploaded.
        # Set to a value such as 1000m; to restrict file size to a specific value
        client_max_body_size 0;
        # To disable buffering
        proxy_buffering off;
        proxy_request_buffering off;

        location / {
            proxy_set_header Host $http_host;
            proxy_set_header X-Real-IP $remote_addr;
            proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
            proxy_set_header X-Forwarded-Proto $scheme;

            proxy_connect_timeout 300;
            # Default is HTTP/1, keepalive is only enabled in HTTP/1.1
            proxy_http_version 1.1;
            proxy_set_header Connection "";
            chunked_transfer_encoding off;

            proxy_pass http://minio;
        }
    }

    server {
        listen       9001;
        listen  [::]:9001;
        server_name  localhost;

        # To allow special characters in headers
        ignore_invalid_headers off;
        # Allow any size file to be uploaded.
        # Set to a value such as 1000m; to restrict file size to a specific value
        client_max_body_size 0;
        # To disable buffering
        proxy_buffering off;
        proxy_request_buffering off;

        location / {
            proxy_set_header Host $http_host;
            proxy_set_header X-Real-IP $remote_addr;
            proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
            proxy_set_header X-Forwarded-Proto $scheme;
            proxy_set_header X-NginX-Proxy true;

            # This is necessary to pass the correct IP to be hashed
            real_ip_header X-Real-IP;

            proxy_connect_timeout 300;

            # To support websocket
            proxy_http_version 1.1;
            proxy_set_header Upgrade $http_upgrade;
            proxy_set_header Connection "upgrade";

            chunked_transfer_encoding off;

            proxy_pass http://console;
        }
    }
}
BASH,
        'supervisord.ini' => <<<'BASH'
[program:horizon]
process_name=%(program_name)s
command=php /var/www/${PROJECT_NAME}/artisan horizon
autostart=true
autorestart=true
user=nginx
redirect_stderr=true
stdout_logfile=/var/log/supervisor/${PROJECT_NAME}-horizon.log
stopwaitsecs=3600
BASH,
    ];
    $path = BASE_PATH.'/.config';
    ensureDir($path);

    foreach ($configurations as $filename => $content) {
        $file_path = $path.DIRECTORY_SEPARATOR.$filename;
        ensureFile($file_path, replaceProjectName($content));
    }

});

// ------------------------------------------------------------
// 5. Create ArchitectureTest.php
// ------------------------------------------------------------
step('Creating ArchitectureTest.php', function () {
    $testDir = BASE_PATH.'/tests/Feature';
    $archTestFile = "$testDir/ArchitectureTest.php";
    $content = <<<PHP
<?php

it('runs on PHP 8.4 or above')
    ->expect(phpversion())
    ->toBeGreaterThanOrEqual('8.4.0');

arch()
    ->expect(['dd', 'dump', 'ray'])
    ->not
    ->toBeUsedIn([
        'app',
        'config',
        'database',
        'routes',
        'support',
    ]);

it('does not using url method')
    ->expect(['url'])
    ->not
    ->toBeUsed();

arch()
    ->expect('App\Http\Controllers')
    ->toHaveSuffix('Controller');

arch()
    ->expect('App\Policies')
    ->toBeClasses();

arch()
    ->expect('App\Policies')
    ->toHaveSuffix('Policy');

arch()
    ->expect('App\Mail')
    ->toBeClasses();

arch()
    ->expect('App\Mail')
    ->toExtend('Illuminate\Mail\Mailable');

arch()
    ->expect('env')
    ->toOnlyBeUsedIn([
        'config',
    ]);

arch()
    ->expect('App')
    ->not
    ->toUse([
        'DB::raw',
        'DB::select',
        'DB::statement',
        'DB::table',
        'DB::insert',
        'DB::update',
        'DB::delete',
    ]);

arch()
    ->expect('App\Concerns')
    ->toBeTraits();

arch()
    ->expect('App\Enums')
    ->toBeEnums();

arch()
    ->expect('App\Contracts')
    ->toBeInterfaces();
PHP;
    setupDirAndFile($testDir, $archTestFile, $content);
});

// ------------------------------------------------------------
// 6. Create documentation templates
// ------------------------------------------------------------
$templates = [
    'CHANGELOG.md' => "# Changelog\n\n## [Unreleased]\n- Initial setup\n",
    'CONTRIBUTING.md' => "# Contributing\n\nThanks for contributing! Submit PRs to `main`.\n",
    'CODE_OF_CONDUCT.md' => "# Code of Conduct\n\nBe respectful and inclusive.\n",
    'SECURITY.md' => "# Security Policy\n\nReport issues to security@example.com\n",
    'SUPPORT.md' => "# Support\n\nFor help, open an issue.\n",
    'LICENSE.md' => "MIT License\n\n(c) ".date('Y')." Your Name\n",
];
foreach ($templates as $file => $content) {
    step("Creating $file", function () use ($file, $content) {
        ensureFile(BASE_PATH."/$file", $content);
    });
}

// ------------------------------------------------------------
// 7. Publish package configs/migrations
// ------------------------------------------------------------
step('Publishing package configs & migrations', function () {
    $tags = [
        '--tag=permission-migrations', '--tag=permission-config',
        '--tag=medialibrary-migrations', '--tag=medialibrary-config',
        '--tag=media-secure-config', '--tag=laravel-errors',
    ];
    foreach ($tags as $tag) {
        runSilent("php artisan vendor:publish {$tag}");
    }
});

// ------------------------------------------------------------
// 8. GitHub Actions Workflows (minimal placeholders here)
// ------------------------------------------------------------
step('Creating GitHub Actions workflows', function () {
    $workflowDir = BASE_PATH.'/.github/workflows';
    ensureDir($workflowDir);
    ensureFile("$workflowDir/pint.yml", "name: PHP Linting (Pint)\n");
    ensureFile("$workflowDir/phpstan.yml", "name: PHPStan\n");
    ensureFile("$workflowDir/rector.yml", "name: Rector CI\n");
    ensureFile("$workflowDir/tests.yml", "name: Test\n");
    ensureFile("$workflowDir/changelog.yml", "name: Update Changelog\n");
});

// ------------------------------------------------------------
// 9. Artisan tasks
// ------------------------------------------------------------
step('Running artisan tasks', function () {
    runSilent('php artisan config:clear');
    runSilent('php artisan migrate');
    runSilent('php artisan storage:link');
});

// ------------------------------------------------------------
// Done
// ------------------------------------------------------------
echo "\n🎉 Project setup completed successfully!\n";
