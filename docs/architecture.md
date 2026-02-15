# Architecture

## Overview

Vaca Vibes follows the Mezzio middleware pipeline pattern with Datastar for real-time frontend updates via Server-Sent Events (SSE).

## Middleware Pipeline

Every request flows through the following pipeline:

```
Request → ServerUrl → Route → ImplicitHead → ImplicitOptions
        → MethodNotAllowed → UrlHelper → Session → Auth
        → Dispatch → Handler → Response
```

Key middleware:

- **SessionMiddleware** -- Starts PHP sessions, loads the authenticated user from `$_SESSION['user_id']` into `$request->getAttribute('user')`
- **AuthMiddleware** -- Redirects unauthenticated users to `/login`. Public paths (`/login`, `/register`) are excluded
- **AdminMiddleware** -- Applied per-route (not globally) to restrict admin pages. Returns 403 for non-admin users

## Dependency Injection

The app uses Laminas ServiceManager with `ReflectionBasedAbstractFactory` for auto-wiring. Only `DatabaseService` has an explicit factory (to inject the database path). All handlers and repositories are auto-wired from their constructor type hints.

```php
// config/autoload/dependencies.global.php
'abstract_factories' => [
    ReflectionBasedAbstractFactory::class,
],
'factories' => [
    DatabaseService::class => DatabaseServiceFactory::class,
],
```

## Database Layer

SQLite is used via PDO with the repository pattern:

| Repository | Table | Purpose |
|-----------|-------|---------|
| `UserRepository` | `users` | User accounts and authentication |
| `VacationRepository` | `vacations` | Vacation plans (scoped by user) |
| `ItineraryRepository` | `itinerary_items` | Daily activities per vacation |
| `ExpenseRepository` | `expenses` | Expense tracking per vacation |

Schema initialization and migrations are handled in `DatabaseService::initSchema()`, which runs on every request. The schema uses `CREATE TABLE IF NOT EXISTS` and `PRAGMA table_info` checks for idempotent migrations.

## SSE / Datastar Pattern

All API handlers (vacation, itinerary, expense CRUD) use Datastar's `ServerSentEventGenerator` to push DOM updates back to the browser:

```php
$sse = new ServerSentEventGenerator();
$sse->sendHeaders();
$sse->patchElements($html, ['selector' => '#target']);
exit;
```

The `exit` call is necessary to prevent Mezzio's response emitter from conflicting with the already-sent SSE headers.

Frontend elements use Datastar attributes to trigger requests:

```html
<button data-on-click="@get('/api/vacation/form')">
    + Add Vacation
</button>
```

## Templates

Plates (native PHP templates) are used with three namespaces:

- `app::` -- Full page templates (home, vacation detail, login, etc.)
- `layout::` -- The base layout with navbar and footer
- `partial::` -- Reusable components (forms, cards, list items)

!!! note "Layout Data"
    Plates 3.x layouts do **not** automatically receive data from the child template's `render()` call. Data must be passed explicitly via `$this->layout('layout::default', ['user' => $user ?? null])`.

## Ownership Model

All vacations are scoped to the user who created them. Every handler that operates on a vacation (or its itinerary/expenses) verifies ownership:

```php
$vacation = $this->vacationRepo->findById($id);
if (! $vacation || (int) $vacation['user_id'] !== $user['id']) {
    // reject
}
```

Admin users access admin-specific routes but do not bypass ownership checks on regular vacation operations.
