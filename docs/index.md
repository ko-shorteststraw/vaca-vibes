# Vacay Vibes

A vacation planning web application built with PHP/Mezzio and real-time UI updates via Datastar SSE.

## Features

- **Vacation Management** -- Create, edit, and delete vacation plans with destinations, dates, budgets, and notes
- **Itinerary Planning** -- Add daily activities with times, descriptions, and costs
- **Expense Tracking** -- Log expenses by category with automatic budget summaries
- **User Authentication** -- Registration, login/logout with session-based auth
- **Admin Dashboard** -- User management and site-wide statistics
- **Real-time UI** -- Server-Sent Events via Datastar for instant updates without page reloads

## Tech Stack

| Layer | Technology |
|-------|-----------|
| Backend | PHP 8.3, [Mezzio](https://docs.mezzio.dev/) |
| Frontend | [Datastar](https://data-star.dev/) v1, [Bulma](https://bulma.io/) CSS |
| Database | SQLite |
| Templates | [Plates](https://platesphp.com/) |
| Infrastructure | Docker Compose (nginx + PHP-FPM) |

## Quick Start

```bash
docker compose up -d
```

The app is available at **http://localhost:8080**.

### Admin Account

The default admin is `admin` / `admin`. Customize via environment variables in `.env`:

```
ADMIN_USERNAME=yourusername
ADMIN_PASSWORD=yourpassword
```
