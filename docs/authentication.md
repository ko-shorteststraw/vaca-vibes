# Authentication

## Overview

Vaca Vibes uses session-based authentication with PHP's native session handling. Users register with a username and password, and sessions are maintained via cookies.

## User Registration

**Route:** `GET/POST /register`

Validation rules:

- Username must be at least 3 characters
- Password must be at least 6 characters
- Password and confirmation must match
- Username must be unique

Passwords are hashed with `password_hash()` using `PASSWORD_DEFAULT` (currently bcrypt).

## Login

**Route:** `GET/POST /login`

Credentials are verified with `password_verify()`. On success, `$_SESSION['user_id']` is set and the user is redirected to `/`.

## Logout

**Route:** `POST /logout`

Destroys the session and clears the session cookie, then redirects to `/login`.

## Middleware

### SessionMiddleware

Runs on every request. Starts the PHP session and loads the user record from the database:

```php
$userId = $_SESSION['user_id'] ?? null;
if ($userId !== null) {
    $user = $this->userRepo->findById((int) $userId);
    $request = $request->withAttribute('user', $user);
}
```

If the stored user ID no longer exists in the database, the session is cleaned up.

### AuthMiddleware

Runs after SessionMiddleware. Checks if `$request->getAttribute('user')` is set. If not, redirects to `/login`.

Public paths that bypass authentication:

- `/login`
- `/register`

### AdminMiddleware

Applied per-route (not globally). Returns a 403 response if the user is not an admin:

```php
if (! $user || ! $user['is_admin']) {
    return new HtmlResponse('Forbidden', 403);
}
```

## Default Admin Account

On first run, `DatabaseService` seeds a default admin user:

| Username | Password | Role |
|----------|----------|------|
| `kendall`  | `admin`  | Admin |

The seed only runs when the `users` table is empty. Any existing vacations with no owner are assigned to the admin.

## Accessing the User in Handlers

All handlers can access the authenticated user via the request attribute:

```php
$user = $request->getAttribute('user');
// $user = ['id' => 1, 'username' => 'admin', 'is_admin' => 1, 'created_at' => '...']
```
