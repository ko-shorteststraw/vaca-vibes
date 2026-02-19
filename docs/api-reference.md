# API Reference

All API endpoints (prefixed with `/api/`) return Server-Sent Events via Datastar. They are not traditional JSON APIs.

## Authentication Routes

These are standard HTML form endpoints, not SSE.

| Method | Path | Handler | Description |
|--------|------|---------|-------------|
| GET/POST | `/login` | `Auth\LoginHandler` | Login form and processing |
| GET/POST | `/register` | `Auth\RegisterHandler` | Registration form and processing |
| POST | `/logout` | `Auth\LogoutHandler` | Destroy session, redirect to login |

## Page Routes

Standard HTML responses.

| Method | Path | Handler | Description |
|--------|------|---------|-------------|
| GET | `/` | `HomeHandler` | Home page with user's vacations |
| GET | `/vacation/{id}` | `VacationDetailHandler` | Vacation detail with itinerary and expenses |

## Admin Routes

Require admin privileges (AdminMiddleware).

| Method | Path | Handler | Description |
|--------|------|---------|-------------|
| GET | `/admin` | `Admin\DashboardHandler` | Admin dashboard with stats |
| GET | `/admin/users` | `Admin\UsersHandler` | User management table |
| DELETE | `/api/admin/user/{id}` | `Admin\DeleteUserHandler` | Delete a user (SSE) |

## Vacation API (SSE)

All vacation API endpoints require authentication. Create/update/delete verify ownership.

| Method | Path | Handler | Description |
|--------|------|---------|-------------|
| GET | `/api/vacation/form` | `Vacation\FormHandler` | Render create form (SSE patch) |
| GET | `/api/vacation/{id}/edit` | `Vacation\FormHandler` | Render edit form (SSE patch) |
| POST | `/api/vacation` | `Vacation\CreateHandler` | Create vacation, redirect to home |
| PUT | `/api/vacation/{id}` | `Vacation\UpdateHandler` | Update vacation, redirect to detail |
| DELETE | `/api/vacation/{id}` | `Vacation\DeleteHandler` | Delete vacation, redirect to home |

### Vacation Signals

The create/update handlers read these Datastar signals:

| Signal | Field |
|--------|-------|
| `destination` | Destination name |
| `startDate` | Start date |
| `endDate` | End date |
| `budget` | Budget amount |
| `notes` | Notes text |
| `imageUrl` | Image URL |

## Itinerary API (SSE)

| Method | Path | Handler | Description |
|--------|------|---------|-------------|
| POST | `/api/vacation/{id}/itinerary` | `Itinerary\CreateHandler` | Add itinerary item |
| GET | `/api/itinerary/{id}/edit` | `Itinerary\EditFormHandler` | Render inline edit form (SSE patch) |
| PUT | `/api/itinerary/{id}` | `Itinerary\UpdateHandler` | Update itinerary item |
| GET | `/api/itinerary/{id}/cancel` | `Itinerary\CancelEditHandler` | Cancel edit, restore read-only view |
| GET | `/api/vacation/{id}/itinerary/suggestions` | `Itinerary\SuggestHandler` | Get suggested activities for destination |
| POST | `/api/vacation/{id}/itinerary/suggested` | `Itinerary\AddSuggestedHandler` | Add a suggested activity to itinerary |
| DELETE | `/api/itinerary/{id}` | `Itinerary\DeleteHandler` | Remove itinerary item |

### Itinerary Signals (Create)

| Signal | Field |
|--------|-------|
| `itinDay` | Day number |
| `itinTitle` | Activity title |
| `itinDescription` | Description |
| `itinTime` | Time |
| `itinCost` | Cost |

### Itinerary Signals (Edit)

| Signal | Field |
|--------|-------|
| `editDay` | Day number |
| `editTitle` | Activity title |
| `editDescription` | Description |
| `editTime` | Time |
| `editCost` | Cost |

### Itinerary Signals (Add Suggested)

| Signal | Field |
|--------|-------|
| `suggestedTitle` | Activity title |
| `suggestedDescription` | Description |
| `suggestedCost` | Estimated cost |

## Expense API (SSE)

| Method | Path | Handler | Description |
|--------|------|---------|-------------|
| POST | `/api/vacation/{id}/expense` | `Expense\CreateHandler` | Add expense |
| DELETE | `/api/expense/{id}` | `Expense\DeleteHandler` | Remove expense |

### Expense Signals

| Signal | Field |
|--------|-------|
| `expDescription` | Expense description |
| `expAmount` | Amount |
| `expCategory` | Category |
