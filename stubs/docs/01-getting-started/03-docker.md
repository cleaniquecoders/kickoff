# Docker

The project includes a `docker-compose.yml` with services for local development.

## Available Services

| Service     | Port(s)        | Description              |
| ----------- | -------------- | ------------------------ |
| MySQL       | 3306           | Database server          |
| Redis       | 6379           | Cache & session store    |
| Mailpit     | 1025, 8025     | Mail testing (SMTP + UI) |
| Meilisearch | 7700           | Full-text search engine  |
| MinIO       | 9000, 9001     | S3-compatible storage    |

## Running Docker

```bash
# Start all services
docker compose up -d

# Stop all services
docker compose down

# View logs
docker compose logs -f

# View logs for a specific service
docker compose logs -f mysql
```

## Access Points

- **MySQL**: `localhost:3306` (root/root or configured user)
- **Redis**: `localhost:6379`
- **Mailpit UI**: http://localhost:8025
- **MinIO Console**: http://localhost:9001 (minioadmin/minioadmin)
- **Meilisearch**: http://localhost:7700

## Environment Variables

Configure Docker services via `.env`:

```env
# MySQL
DB_PORT=3306
DB_ROOT_PASSWORD=root
DB_DATABASE=your_database
DB_USERNAME=your_user
DB_PASSWORD=secret

# Redis
REDIS_PORT=6379

# Mailpit
MAIL_PORT=1025
MAILPIT_UI_PORT=8025

# Meilisearch
MEILI_PORT=7700
MEILI_MASTER_KEY=masterKey

# MinIO
MINIO_ROOT_USER=minioadmin
MINIO_ROOT_PASSWORD=minioadmin
```

## Connecting Laravel to Docker Services

Update your `.env` to connect to Docker services:

```env
# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_user
DB_PASSWORD=secret

# Cache & Session
CACHE_DRIVER=redis
SESSION_DRIVER=redis
REDIS_HOST=127.0.0.1
REDIS_PORT=6379

# Mail
MAIL_MAILER=smtp
MAIL_HOST=127.0.0.1
MAIL_PORT=1025
```
