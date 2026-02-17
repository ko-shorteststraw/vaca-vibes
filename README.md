# Vacay Vibes

A vacation planning web application built with PHP/Mezzio and real-time UI updates via Datastar SSE.

## Features

- **Vacation Management** -- Create, edit, and delete vacation plans with destinations, dates, budgets, and notes
- **Itinerary Planning** -- Add daily activities with times, descriptions, and costs
- **Expense Tracking** -- Log expenses by category with automatic budget summaries
- **User Authentication** -- Registration, login/logout with session-based auth
- **Admin Dashboard** -- User management and site-wide statistics (admin-only)
- **Real-time UI** -- Server-Sent Events via Datastar for instant updates without page reloads

## Tech Stack

- **Backend:** PHP 8.3, [Mezzio](https://docs.mezzio.dev/) framework
- **Frontend:** [Datastar](https://data-star.dev/) v1 (SSE-driven hypermedia), [Bulma](https://bulma.io/) CSS
- **Database:** SQLite
- **Templates:** [Plates](https://platesphp.com/) (native PHP templates)
- **Infrastructure:** Docker Compose (nginx + PHP-FPM)

## Quick Start

### Prerequisites

- Docker and Docker Compose

### Run

```bash
docker compose up -d
```

The app is available at **http://localhost:8080**.

### Admin Account

The default admin credentials are `admin` / `admin`. To customize, copy `.env.example` to `.env` and set your values:

```bash
cp .env.example .env
```

Then edit `.env`:

```
ADMIN_USERNAME=yourusername
ADMIN_PASSWORD=yourpassword
```

These are only used when seeding the database on first run (empty users table). Restart the PHP container after changes: `docker compose restart php`.

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

## Architecture

The app follows the Mezzio middleware pipeline pattern:

```
Request → Route → Session → Auth → Dispatch → Handler → Response
```

- **SessionMiddleware** starts PHP sessions and loads the authenticated user
- **AuthMiddleware** redirects unauthenticated users to `/login` (public paths excluded)
- **AdminMiddleware** applied per-route to restrict admin pages

All API handlers use Datastar's `ServerSentEventGenerator` to push real-time DOM patches back to the browser via SSE, eliminating the need for JavaScript frameworks or full page reloads.

## Documentation

Full documentation is available in the `docs/` directory, built with [Zensical](https://zensical.org/).

To serve docs locally:

```bash
pip install zensical
zensical serve
```

## License

MIT
