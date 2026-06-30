# Flutter User API — Design Spec

**Date:** 2026-06-30
**Status:** Approved (design) — pending implementation plan
**Scope:** REST API for a **user-only** Flutter app: login → browse item catalogue → submit loan application (permohonan) → view own application status.

---

## 1. Goal & Context

The existing web app (`jpp-makmal`) serves users and admins via **session-based** Blade routes. A new **Flutter mobile app for users only** needs a **token-based JSON API** to:

1. Authenticate users (email + password → token).
2. Browse available items.
3. Submit loan applications.
4. View their own applications and status.

Admins are **out of scope** — they continue using the existing web panel. No admin endpoints are built here.

### Current state (from exploration)
- No `routes/api.php`, no Sanctum/Passport installed.
- `bootstrap/app.php` already renders JSON for `api/*` (`shouldRenderJsonWhen`).
- Web login: `email` + `password` (min 8), rate-limited 5 attempts/IP, role-based redirect.
- Loan application flow (`UserLoanApplicationController::store`): validate → re-check stock → create `LoanApplication` (status `menunggu`) + `LoanApplicationItem` rows. `district_id` auto-filled from the user.
- Item catalogue filter: `is_active = true`, `status = 'tersedia'`, `available_quantity > 0`.

---

## 2. Architecture Decisions

| Decision | Choice | Rationale |
|----------|--------|-----------|
| Auth | **Laravel Sanctum** (per-user/per-device token) | Official, lightweight, first-party mobile. Approved to add `laravel/sanctum`. |
| Versioning | **`/api/v1/…`** | No existing API routes → default to versioning per project guidelines. |
| Serialization | **Eloquent API Resources** | Project guideline default. Stable, explicit JSON contract. |
| Shared business logic | **`App\Actions\CreateLoanApplication`** | Stock re-check + creation currently inline in the web controller. Extract so web **and** API share one source of truth (DRY). |
| Admin access | **Not restricted** | App is user-facing; admins logging in only see their own (empty) data. No gate added in v1. |

---

## 3. Endpoints

Base prefix: `/api/v1`. Protected routes use middleware `auth:sanctum`. Users only ever access their **own** data (`user_id = Auth::id()`); accessing another user's application → **403**.

| Method | Path | Auth | Purpose |
|--------|------|:----:|---------|
| POST | `/login` | — | Email+password → `{ token, user }` |
| POST | `/logout` | ✅ | Revoke the current token (logout this device only) |
| GET | `/user` | ✅ | Current authenticated user |
| GET | `/items` | ✅ | Available item catalogue (search, category filter, paginated) |
| GET | `/items/{id}` | ✅ | Single item detail |
| GET | `/loan-applications` | ✅ | Own applications + status (paginated, latest first) |
| POST | `/loan-applications` | ✅ | Submit a new application |
| GET | `/loan-applications/{id}` | ✅ | Own application detail |

---

## 4. Authentication Flow

1. **`POST /api/v1/login`** — validates `email`, `password`; throttled (mirror web: 5 attempts/IP, 900s lockout) via the `throttle` middleware / `RateLimiter`. On success: update `last_login_at`, create a Sanctum token named after `device_name`, return `{ token, user }`. On failure: **422** with `auth.failed` message. Throttled: **429**.
2. The Flutter app stores the token in **secure storage** and attaches `Authorization: Bearer <token>` to every subsequent request (no user interaction; never shown/typed by the user).
3. **`POST /api/v1/logout`** — deletes the **current** access token only (`$request->user()->currentAccessToken()->delete()`), leaving other devices logged in.

`User` model gains the `Laravel\Sanctum\HasApiTokens` trait. Tokens live in the `personal_access_tokens` table (one row per user/device); each token resolves to exactly one user.

---

## 5. Request / Response Contracts

### POST /api/v1/login
```json
// request
{ "email": "ali@jpp.gov.my", "password": "secret123", "device_name": "Pixel 8" }

// 200
{
  "token": "12|aBcD...",
  "user": { "id": 5, "name": "Ali", "email": "ali@jpp.gov.my",
            "district": { "id": 3, "name": "Hulu Langat" } }
}
```

### GET /api/v1/items?search=&category_id=&page=
Returns a **paginated** list of available items (filter identical to web: `is_active`, `status='tersedia'`, `available_quantity > 0`). Optional `search` (name) and `category_id` filters.

### POST /api/v1/loan-applications
```json
// request — clean array shape (differs from the web's keyed-map input)
{
  "items": [ { "item_id": 12, "quantity": 2 }, { "item_id": 7, "quantity": 1 } ],
  "start_date": "2026-07-05",
  "end_date": "2026-07-10",
  "purpose": "Untuk program makmal sekolah"
}

// 201 → LoanApplicationResource
{
  "data": {
    "id": 31, "application_no": "LA-20260630-031", "status": "menunggu",
    "start_date": "2026-07-05", "end_date": "2026-07-10",
    "purpose": "Untuk program makmal sekolah",
    "district": { "id": 3, "name": "Hulu Langat" },
    "items": [ { "id": 88, "item": { "id": 12, "name": "Mikroskop" }, "quantity_requested": 2 } ],
    "items_count": 2, "created_at": "2026-06-30T08:00:00Z"
  }
}
```

### Validation rules (POST /loan-applications)
Mirrors the web `StoreLoanApplicationRequest`, adapted to the clean array shape:

| Field | Rule |
|-------|------|
| `items` | required, array, min:1 |
| `items.*.item_id` | required, exists:items,id |
| `items.*.quantity` | required, integer, min:1 |
| `start_date` | required, date, after_or_equal:today |
| `end_date` | required, date, after_or_equal:start_date |
| `purpose` | required, string, min:10 |

Stock is **re-checked at submit time** inside the shared action; insufficient stock → **422** with a descriptive message (e.g. `Stok Mikroskop tidak mencukupi. Tersedia: 1`).

---

## 6. Eloquent API Resources

- **`UserResource`** — `id`, `name`, `email`, `district { id, name }`
- **`ItemResource`** — `id`, `name`, `description`, `category { id, name }`, `available_quantity`, `condition`, `image_url`
- **`LoanApplicationResource`** — `id`, `application_no`, `status`, `start_date`, `end_date`, `purpose`, `district`, `items[]`, `items_count`, `rejection_reason`, `created_at`
- **`LoanApplicationItemResource`** — `id`, `item { id, name }`, `quantity_requested`

`image_url` derives from `Item.image` (full URL via storage). Internal fields (`qr_code`, `storage_location_id`) are **not** exposed.

---

## 7. Shared Action — `App\Actions\CreateLoanApplication`

Single source of truth for creating an application, used by both the API controller and (refactored) web controller.

- **Input:** authenticated `User`, normalized `items` array (`[{ id, quantity }]`), `start_date`, `end_date`, `purpose`.
- **Behaviour:** wrap in a `DB::transaction`; re-check stock for each item; generate `application_no`; create the `LoanApplication` (status `menunggu`, `district_id` from user) + `LoanApplicationItem` rows; return the created application.
- **Stock failure:** throws `App\Exceptions\InsufficientStockException` (carries the user-facing message). The **API controller** catches it → 422 JSON; the **web controller** catches it → `back()->with('error', ...)`. Web user-facing behaviour is unchanged.

The web controller's `StoreLoanApplicationRequest::getSelectedItems()` already yields `[{ id, quantity }]`, so it maps cleanly onto the action input. The API FormRequest maps `item_id → id`.

---

## 8. Error Handling

`bootstrap/app.php` already JSON-renders `api/*`. Standard responses:

| Status | When |
|--------|------|
| 422 | Validation errors / insufficient stock |
| 401 | Missing or invalid token (`auth:sanctum`) |
| 403 | Accessing another user's application |
| 404 | Unknown item / application id |
| 429 | Login throttle exceeded |

Validation errors use Laravel's default `{ "message": ..., "errors": { field: [...] } }` shape.

---

## 9. Testing (PHPUnit feature tests)

- **Auth:** login success returns token; wrong password → 422; throttle after 5 attempts → 429; `logout` revokes current token; protected route without token → 401.
- **Items:** list returns only available items; `search` / `category_id` filters work; pagination shape.
- **Loan applications:** submit happy path → 201 + DB rows + status `menunggu`; validation failures → 422; insufficient stock → 422 (no rows created); list returns only own applications; show own → 200; **show another user's application → 403**.

Use model factories (create any missing factories: `Item`, `Category`, `District`, `LoanApplication`).

---

## 10. Deliverables

1. Install + configure `laravel/sanctum` (via `php artisan install:api`); add `HasApiTokens` to `User`.
2. `routes/api.php` (v1 group) registered in `bootstrap/app.php`.
3. Controllers: `App\Http\Controllers\Api\AuthController`, `Api\ItemController`, `Api\LoanApplicationController`.
4. `App\Actions\CreateLoanApplication` + `App\Exceptions\InsufficientStockException`; refactor web controller to use the action.
5. `App\Http\Requests\Api\StoreLoanApplicationRequest` + 4 API Resources.
6. Feature tests (section 9) + any missing factories.
7. **Flutter integration guide** (markdown in `docs/`): base URL config, login → store token via `flutter_secure_storage`, `Dio` interceptor attaching the bearer token, fetch item catalogue, submit permohonan, view status — with copy-paste Dart examples.

---

## 11. Out of Scope (v1)

- Admin/approval endpoints, push notifications, file/image upload from mobile.
- Active loans (`loans`) and return flow — deferred to a later phase.
- Password reset, registration (users are provisioned via the existing web/admin flow).
