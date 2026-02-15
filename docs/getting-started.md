# Getting Started

## Prerequisites

- Docker and Docker Compose

## Installation

Clone the repository and start the containers:

```bash
git clone <repo-url> vaca-vibes
cd vaca-vibes
docker compose up -d
```

This starts two services:

- **nginx** -- Reverse proxy on port 8080
- **php** -- PHP 8.3 FPM with SQLite

## First Run

On first startup, the application automatically:

1. Creates the SQLite database at `data/database.sqlite`
2. Initializes all tables (users, vacations, itinerary_items, expenses)
3. Seeds a default admin account (`admin` / `admin`)

Visit **http://localhost:8080** to access the app. You'll be redirected to the login page.

## Project Structure

```
vaca-vibes/
├── config/
│   ├── autoload/           # Dependency & template config
│   ├── config.php          # Container bootstrap
│   ├── pipeline.php        # Middleware pipeline
│   └── routes.php          # Route definitions
├── data/
│   └── database.sqlite     # SQLite database (auto-created)
├── public/
│   └── index.php           # Entry point
├── src/App/
│   ├── Handler/            # Request handlers
│   │   ├── Admin/          # Admin dashboard & user management
│   │   ├── Auth/           # Login, register, logout
│   │   ├── Expense/        # Expense CRUD (SSE)
│   │   ├── Itinerary/      # Itinerary CRUD (SSE)
│   │   └── Vacation/       # Vacation CRUD (SSE)
│   ├── Middleware/          # Session, Auth, Admin middleware
│   └── Service/            # Database, repositories
├── templates/
│   ├── app/                # Page templates
│   ├── layout/             # Layout template
│   └── partial/            # Reusable partials
├── docker-compose.yml
└── composer.json
```

## Docker Services

The `docker-compose.yml` defines:

| Service | Image | Purpose |
|---------|-------|---------|
| `nginx` | `nginx:alpine` | Serves static files, proxies PHP requests via FastCGI |
| `php` | Custom (PHP 8.3-FPM Alpine) | Runs the application with SQLite PDO extension |

The nginx config sets `fastcgi_buffering off` which is required for Server-Sent Events to stream properly through the proxy.

## Development

PHP dependencies are managed via Composer. The `vendor/` directory is mounted from a Docker volume for performance. To install or update dependencies:

```bash
docker compose exec php composer install
```
