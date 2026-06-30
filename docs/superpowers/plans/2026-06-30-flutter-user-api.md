# Flutter User API Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Build a token-based (Sanctum) REST API so a user-only Flutter app can log in, browse available items, submit loan applications, and view their own application status.

**Architecture:** Versioned `/api/v1` routes guarded by `auth:sanctum`. Eloquent API Resources shape the JSON. A shared `App\Actions\CreateLoanApplication` holds the stock-check + creation logic used by both the new API controller and the refactored web controller (single source of truth). Validation errors, auth failures, and stock failures return JSON (already enabled for `api/*` in `bootstrap/app.php`).

**Tech Stack:** Laravel v13, PHP 8.4, Laravel Sanctum, Eloquent API Resources, PHPUnit (sqlite `:memory:`), Pint.

**Spec:** `docs/superpowers/specs/2026-06-30-flutter-user-api-design.md`

**Conventions observed:** Tests live in `tests/Feature`, use `Tests\TestCase` + `RefreshDatabase`, sqlite in-memory. Factories use `fake()`. The only existing factory is `UserFactory`; this plan adds the rest. API endpoints use `auth:sanctum` only (no Spatie permission middleware), so tests do **not** need to seed roles.

---

## File Structure

**Create:**
- `routes/api.php` (created by `install:api`, then replaced with our v1 group)
- `app/Http/Controllers/Api/AuthController.php` — login, logout, me
- `app/Http/Controllers/Api/ItemController.php` — item catalogue index/show
- `app/Http/Controllers/Api/LoanApplicationController.php` — application index/store/show
- `app/Http/Requests/Api/StoreLoanApplicationRequest.php` — API submission validation
- `app/Http/Resources/UserResource.php`
- `app/Http/Resources/ItemResource.php`
- `app/Http/Resources/LoanApplicationResource.php`
- `app/Http/Resources/LoanApplicationItemResource.php`
- `app/Actions/CreateLoanApplication.php`
- `app/Exceptions/InsufficientStockException.php`
- `database/factories/DistrictFactory.php`
- `database/factories/CategoryFactory.php`
- `database/factories/StorageLocationFactory.php`
- `database/factories/ItemFactory.php`
- `database/factories/LoanApplicationFactory.php`
- `docs/flutter-integration-guide.md`
- Test files under `tests/Feature/Api/` and `tests/Feature/`

**Modify:**
- `app/Models/User.php` — add `HasApiTokens`
- `app/Models/Item.php`, `Category.php`, `District.php`, `StorageLocation.php`, `LoanApplication.php` — add `HasFactory`
- `app/Http/Controllers/User/UserLoanApplicationController.php` — use the shared action
- `composer.json` / `bootstrap/app.php` — via `install:api`

---

## Task 1: Install Sanctum and wire the API

**Files:**
- Run: `php artisan install:api`
- Modify: `app/Models/User.php`
- Create: `routes/api.php` (replace generated content)
- Test: `tests/Feature/Api/AuthTest.php`

- [ ] **Step 1: Install the API scaffolding + Sanctum**

Run:
```bash
php artisan install:api --no-interaction
php artisan migrate
```
Expected: `laravel/sanctum` added to `composer.json`; `routes/api.php` created; `bootstrap/app.php` `withRouting(...)` gains `api: __DIR__.'/../routes/api.php'`; `personal_access_tokens` table migrated.

- [ ] **Step 2: Add the `HasApiTokens` trait to the User model**

Modify `app/Models/User.php` — add the import and the trait:
```php
use Laravel\Sanctum\HasApiTokens;
```
```php
use HasFactory, Notifiable, HasRoles, SoftDeletes, HasApiTokens;
```

- [ ] **Step 3: Replace `routes/api.php` with the v1 skeleton**

```php
<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('user', fn (Request $request) => $request->user());
    });
});
```

- [ ] **Step 4: Write the failing test**

Create `tests/Feature/Api/AuthTest.php`:
```php
<?php

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_protected_route_requires_token(): void
    {
        $this->getJson('/api/v1/user')->assertStatus(401);
    }
}
```

- [ ] **Step 5: Run the test**

Run: `php artisan test --compact --filter=AuthTest`
Expected: PASS (unauthenticated request to a `auth:sanctum` route returns 401 JSON).

- [ ] **Step 6: Commit**

```bash
git add composer.json composer.lock config/sanctum.php bootstrap/app.php routes/api.php app/Models/User.php database/migrations tests/Feature/Api/AuthTest.php
git commit -m "feat(api): install Sanctum and scaffold v1 API routing"
```

---

## Task 2: Login endpoint

**Files:**
- Create: `app/Http/Resources/UserResource.php`
- Create: `app/Http/Controllers/Api/AuthController.php`
- Modify: `routes/api.php`
- Test: `tests/Feature/Api/AuthTest.php`

- [ ] **Step 1: Write the failing tests**

Append to `tests/Feature/Api/AuthTest.php` (add `use App\Models\User;` at the top):
```php
    public function test_user_can_login_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'ali@example.com',
            'password' => 'password',
        ]);

        $response = $this->postJson('/api/v1/login', [
            'email' => 'ali@example.com',
            'password' => 'password',
            'device_name' => 'Pixel 8',
        ]);

        $response->assertOk()
            ->assertJsonStructure(['token', 'user' => ['id', 'name', 'email']]);

        $this->assertDatabaseHas('personal_access_tokens', [
            'tokenable_id' => $user->id,
            'name' => 'Pixel 8',
        ]);
    }

    public function test_login_fails_with_wrong_password(): void
    {
        User::factory()->create([
            'email' => 'ali@example.com',
            'password' => 'password',
        ]);

        $this->postJson('/api/v1/login', [
            'email' => 'ali@example.com',
            'password' => 'wrong-password',
        ])->assertStatus(422)->assertJsonValidationErrors('email');
    }

    public function test_login_is_throttled_after_too_many_attempts(): void
    {
        User::factory()->create(['email' => 'ali@example.com', 'password' => 'password']);

        for ($i = 0; $i < 5; $i++) {
            $this->postJson('/api/v1/login', ['email' => 'ali@example.com', 'password' => 'wrong']);
        }

        $this->postJson('/api/v1/login', ['email' => 'ali@example.com', 'password' => 'password'])
            ->assertStatus(429);
    }
```

- [ ] **Step 2: Run to verify failure**

Run: `php artisan test --compact --filter=AuthTest`
Expected: FAIL (route `/api/v1/login` not defined → 404).

- [ ] **Step 3: Create `UserResource`**

`app/Http/Resources/UserResource.php`:
```php
<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'district' => $this->whenLoaded('district', fn () => $this->district ? [
                'id' => $this->district->id,
                'name' => $this->district->name,
            ] : null),
        ];
    }
}
```

- [ ] **Step 4: Create `AuthController` with `login`**

`app/Http/Controllers/Api/AuthController.php`:
```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
            'device_name' => 'nullable|string|max:255',
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (! $user || ! Hash::check($validated['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        $user->forceFill(['last_login_at' => now()])->save();

        $deviceName = $validated['device_name'] ?? 'flutter';
        $token = $user->createToken($deviceName)->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => new UserResource($user->load('district')),
        ]);
    }
}
```

- [ ] **Step 5: Add the login route (throttled)**

Replace `routes/api.php` with:
```php
<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::post('login', [AuthController::class, 'login'])->middleware('throttle:5,15');

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('user', fn (Request $request) => $request->user());
    });
});
```

- [ ] **Step 6: Run to verify pass**

Run: `php artisan test --compact --filter=AuthTest`
Expected: PASS (all four tests).

- [ ] **Step 7: Commit**

```bash
git add app/Http/Resources/UserResource.php app/Http/Controllers/Api/AuthController.php routes/api.php tests/Feature/Api/AuthTest.php
git commit -m "feat(api): add token login endpoint with throttling"
```

---

## Task 3: Logout and current-user (`me`) endpoints

**Files:**
- Modify: `app/Http/Controllers/Api/AuthController.php`
- Modify: `routes/api.php`
- Test: `tests/Feature/Api/AuthTest.php`

- [ ] **Step 1: Write the failing tests**

Append to `tests/Feature/Api/AuthTest.php`:
```php
    public function test_me_returns_authenticated_user(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;

        $this->withToken($token)->getJson('/api/v1/user')
            ->assertOk()
            ->assertJsonPath('user.id', $user->id);
    }

    public function test_logout_revokes_current_token(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;

        $this->withToken($token)->postJson('/api/v1/logout')->assertOk();
        $this->withToken($token)->getJson('/api/v1/user')->assertStatus(401);
    }
```

- [ ] **Step 2: Run to verify failure**

Run: `php artisan test --compact --filter=AuthTest`
Expected: FAIL (`me` returns the raw user not `{user: {...}}`; `/logout` route missing → 404).

- [ ] **Step 3: Add `logout` and `me` to `AuthController`**

Append these methods inside `app/Http/Controllers/Api/AuthController.php`:
```php
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Berjaya log keluar.']);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'user' => new UserResource($request->user()->load('district')),
        ]);
    }
```

- [ ] **Step 4: Point routes at the controller**

Replace the `auth:sanctum` group in `routes/api.php` so it reads:
```php
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('user', [AuthController::class, 'me']);
    });
```

- [ ] **Step 5: Run to verify pass**

Run: `php artisan test --compact --filter=AuthTest`
Expected: PASS (all six tests).

- [ ] **Step 6: Commit**

```bash
git add app/Http/Controllers/Api/AuthController.php routes/api.php tests/Feature/Api/AuthTest.php
git commit -m "feat(api): add logout and current-user endpoints"
```

---

## Task 4: Item catalogue (factories + resource + controller)

**Files:**
- Modify: `app/Models/Item.php`, `Category.php`, `District.php`, `StorageLocation.php` (add `HasFactory`)
- Create: `database/factories/DistrictFactory.php`, `CategoryFactory.php`, `StorageLocationFactory.php`, `ItemFactory.php`
- Create: `app/Http/Resources/ItemResource.php`
- Create: `app/Http/Controllers/Api/ItemController.php`
- Modify: `routes/api.php`
- Test: `tests/Feature/Api/ItemApiTest.php`

- [ ] **Step 1: Add `HasFactory` to the four models**

In each of `app/Models/Item.php`, `Category.php`, `District.php`, `StorageLocation.php` add:
```php
use Illuminate\Database\Eloquent\Factories\HasFactory;
```
and add the trait to the class, e.g. for `Item`:
```php
class Item extends Model
{
    use HasFactory;
```
(For `District` and `StorageLocation` which already have a `casts()` method, just add `use HasFactory;` as the first line in the class body.)

- [ ] **Step 2: Create the factories**

`database/factories/DistrictFactory.php`:
```php
<?php

namespace Database\Factories;

use App\Models\District;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<District> */
class DistrictFactory extends Factory
{
    protected $model = District::class;

    public function definition(): array
    {
        return [
            'name' => fake()->city(),
            'code' => strtoupper(fake()->unique()->bothify('D###')),
            'address' => fake()->address(),
            'phone' => fake()->numerify('01#-#######'),
            'is_active' => true,
        ];
    }
}
```

`database/factories/CategoryFactory.php`:
```php
<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Category> */
class CategoryFactory extends Factory
{
    protected $model = Category::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->words(2, true),
            'description' => fake()->sentence(),
            'status' => 'dipinjam',
        ];
    }
}
```

`database/factories/StorageLocationFactory.php`:
```php
<?php

namespace Database\Factories;

use App\Models\StorageLocation;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<StorageLocation> */
class StorageLocationFactory extends Factory
{
    protected $model = StorageLocation::class;

    public function definition(): array
    {
        return [
            'name' => fake()->words(2, true).' Store',
            'code' => strtoupper(fake()->unique()->bothify('SL###')),
            'description' => fake()->sentence(),
            'is_active' => true,
        ];
    }
}
```

`database/factories/ItemFactory.php`:
```php
<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Item;
use App\Models\StorageLocation;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Item> */
class ItemFactory extends Factory
{
    protected $model = Item::class;

    public function definition(): array
    {
        return [
            'name' => fake()->words(3, true),
            'description' => fake()->sentence(),
            'quantity' => 10,
            'available_quantity' => 10,
            'condition' => 'baik',
            'status' => 'tersedia',
            'category_id' => Category::factory(),
            'storage_location_id' => StorageLocation::factory(),
            'expiry_date' => null,
            'image' => null,
            'qr_code' => null,
            'is_active' => true,
        ];
    }
}
```

- [ ] **Step 3: Write the failing tests**

`tests/Feature/Api/ItemApiTest.php`:
```php
<?php

namespace Tests\Feature\Api;

use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ItemApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_lists_only_available_items(): void
    {
        Sanctum::actingAs(User::factory()->create());

        Item::factory()->create(['name' => 'Mikroskop', 'status' => 'tersedia', 'available_quantity' => 5, 'is_active' => true]);
        Item::factory()->create(['status' => 'dipinjam', 'available_quantity' => 0]);
        Item::factory()->create(['status' => 'tersedia', 'available_quantity' => 0]);
        Item::factory()->create(['status' => 'tersedia', 'available_quantity' => 3, 'is_active' => false]);

        $this->getJson('/api/v1/items')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'Mikroskop');
    }

    public function test_can_filter_items_by_search(): void
    {
        Sanctum::actingAs(User::factory()->create());
        Item::factory()->create(['name' => 'Mikroskop Digital']);
        Item::factory()->create(['name' => 'Beaker Kaca']);

        $this->getJson('/api/v1/items?search=Mikro')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'Mikroskop Digital');
    }

    public function test_can_show_single_item(): void
    {
        Sanctum::actingAs(User::factory()->create());
        $item = Item::factory()->create(['name' => 'Mikroskop']);

        $this->getJson("/api/v1/items/{$item->id}")
            ->assertOk()
            ->assertJsonPath('data.name', 'Mikroskop');
    }

    public function test_items_require_authentication(): void
    {
        $this->getJson('/api/v1/items')->assertStatus(401);
    }
}
```

- [ ] **Step 4: Run to verify failure**

Run: `php artisan test --compact --filter=ItemApiTest`
Expected: FAIL (route `/api/v1/items` missing → 404).

- [ ] **Step 5: Create `ItemResource`**

`app/Http/Resources/ItemResource.php`:
```php
<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'category' => $this->whenLoaded('category', fn () => [
                'id' => $this->category->id,
                'name' => $this->category->name,
            ]),
            'available_quantity' => $this->available_quantity,
            'condition' => $this->condition,
            'image_url' => $this->image ? asset('storage/'.$this->image) : null,
        ];
    }
}
```

- [ ] **Step 6: Create `ItemController`**

`app/Http/Controllers/Api/ItemController.php`:
```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ItemResource;
use App\Models\Item;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $items = Item::query()
            ->with('category')
            ->where('is_active', true)
            ->where('status', 'tersedia')
            ->where('available_quantity', '>', 0)
            ->when($request->filled('search'), fn ($q) => $q->where('name', 'like', '%'.$request->string('search').'%'))
            ->when($request->filled('category_id'), fn ($q) => $q->where('category_id', $request->integer('category_id')))
            ->latest()
            ->paginate(15);

        return ItemResource::collection($items);
    }

    public function show(Item $item)
    {
        return new ItemResource($item->load('category'));
    }
}
```

- [ ] **Step 7: Add item routes**

In `routes/api.php`, inside the `auth:sanctum` group, add:
```php
        Route::get('items', [\App\Http\Controllers\Api\ItemController::class, 'index']);
        Route::get('items/{item}', [\App\Http\Controllers\Api\ItemController::class, 'show']);
```

- [ ] **Step 8: Run to verify pass**

Run: `php artisan test --compact --filter=ItemApiTest`
Expected: PASS (all four tests).

- [ ] **Step 9: Commit**

```bash
git add app/Models database/factories app/Http/Resources/ItemResource.php app/Http/Controllers/Api/ItemController.php routes/api.php tests/Feature/Api/ItemApiTest.php
git commit -m "feat(api): add item catalogue endpoints with factories"
```

---

## Task 5: Shared action, exception, and loan resources

**Files:**
- Create: `app/Exceptions/InsufficientStockException.php`
- Create: `app/Actions/CreateLoanApplication.php`
- Create: `app/Http/Resources/LoanApplicationItemResource.php`, `LoanApplicationResource.php`
- Modify: `app/Models/LoanApplication.php` (add `HasFactory`)
- Create: `database/factories/LoanApplicationFactory.php`
- Test: `tests/Feature/CreateLoanApplicationTest.php`

- [ ] **Step 1: Add `HasFactory` to `LoanApplication`**

In `app/Models/LoanApplication.php` add `use Illuminate\Database\Eloquent\Factories\HasFactory;` and `use HasFactory;` in the class body.

- [ ] **Step 2: Create `LoanApplicationFactory`**

`database/factories/LoanApplicationFactory.php`:
```php
<?php

namespace Database\Factories;

use App\Models\District;
use App\Models\LoanApplication;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<LoanApplication> */
class LoanApplicationFactory extends Factory
{
    protected $model = LoanApplication::class;

    public function definition(): array
    {
        return [
            'application_no' => 'LA-'.now()->format('Ymd').'-'.fake()->unique()->numerify('###'),
            'user_id' => User::factory(),
            'district_id' => District::factory(),
            'start_date' => now()->addDay()->toDateString(),
            'end_date' => now()->addDays(5)->toDateString(),
            'purpose' => fake()->sentence(),
            'status' => 'menunggu',
        ];
    }
}
```

- [ ] **Step 3: Write the failing tests for the action**

`tests/Feature/CreateLoanApplicationTest.php`:
```php
<?php

namespace Tests\Feature;

use App\Actions\CreateLoanApplication;
use App\Exceptions\InsufficientStockException;
use App\Models\District;
use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateLoanApplicationTest extends TestCase
{
    use RefreshDatabase;

    public function test_creates_application_with_items(): void
    {
        $district = District::factory()->create();
        $user = User::factory()->create(['district_id' => $district->id]);
        $item = Item::factory()->create(['available_quantity' => 5]);

        $application = app(CreateLoanApplication::class)->handle(
            $user,
            [['id' => $item->id, 'quantity' => 2]],
            now()->addDay()->toDateString(),
            now()->addDays(3)->toDateString(),
            'Untuk program makmal sekolah',
        );

        $this->assertSame('menunggu', $application->status);
        $this->assertSame($user->district_id, $application->district_id);
        $this->assertDatabaseHas('loan_application_items', [
            'loan_application_id' => $application->id,
            'item_id' => $item->id,
            'quantity_requested' => 2,
        ]);
    }

    public function test_throws_when_stock_insufficient(): void
    {
        $district = District::factory()->create();
        $user = User::factory()->create(['district_id' => $district->id]);
        $item = Item::factory()->create(['available_quantity' => 1]);

        $this->expectException(InsufficientStockException::class);

        app(CreateLoanApplication::class)->handle(
            $user,
            [['id' => $item->id, 'quantity' => 3]],
            now()->addDay()->toDateString(),
            now()->addDays(3)->toDateString(),
            'Untuk program makmal sekolah',
        );
    }
}
```

- [ ] **Step 4: Run to verify failure**

Run: `php artisan test --compact --filter=CreateLoanApplicationTest`
Expected: FAIL (`App\Actions\CreateLoanApplication` does not exist).

- [ ] **Step 5: Create the exception**

`app/Exceptions/InsufficientStockException.php`:
```php
<?php

namespace App\Exceptions;

use Exception;

class InsufficientStockException extends Exception
{
}
```

- [ ] **Step 6: Create the action**

`app/Actions/CreateLoanApplication.php`:
```php
<?php

namespace App\Actions;

use App\Exceptions\InsufficientStockException;
use App\Models\Item;
use App\Models\LoanApplication;
use App\Models\LoanApplicationItem;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CreateLoanApplication
{
    /**
     * @param  array<int, array{id: int, quantity: int}>  $items
     */
    public function handle(User $user, array $items, string $startDate, string $endDate, string $purpose): LoanApplication
    {
        return DB::transaction(function () use ($user, $items, $startDate, $endDate, $purpose) {
            foreach ($items as $row) {
                $item = Item::findOrFail($row['id']);

                if ($item->available_quantity < $row['quantity']) {
                    throw new InsufficientStockException(
                        "Stok {$item->name} tidak mencukupi. Tersedia: {$item->available_quantity}"
                    );
                }
            }

            $application = LoanApplication::create([
                'application_no' => 'LA-'.now()->format('Ymd').'-'.str_pad((string) (LoanApplication::max('id') + 1), 3, '0', STR_PAD_LEFT),
                'user_id' => $user->id,
                'district_id' => $user->district_id,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'purpose' => $purpose,
                'status' => 'menunggu',
            ]);

            foreach ($items as $row) {
                LoanApplicationItem::create([
                    'loan_application_id' => $application->id,
                    'item_id' => $row['id'],
                    'quantity_requested' => $row['quantity'],
                ]);
            }

            return $application->load('items.item', 'district');
        });
    }
}
```

- [ ] **Step 7: Create the loan resources**

`app/Http/Resources/LoanApplicationItemResource.php`:
```php
<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LoanApplicationItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'item' => $this->whenLoaded('item', fn () => [
                'id' => $this->item->id,
                'name' => $this->item->name,
            ]),
            'quantity_requested' => $this->quantity_requested,
        ];
    }
}
```

`app/Http/Resources/LoanApplicationResource.php`:
```php
<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LoanApplicationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'application_no' => $this->application_no,
            'status' => $this->status,
            'start_date' => $this->start_date?->toDateString(),
            'end_date' => $this->end_date?->toDateString(),
            'purpose' => $this->purpose,
            'district' => $this->whenLoaded('district', fn () => $this->district ? [
                'id' => $this->district->id,
                'name' => $this->district->name,
            ] : null),
            'items' => LoanApplicationItemResource::collection($this->whenLoaded('items')),
            'items_count' => $this->whenLoaded('items',
                fn () => $this->items->count(),
                $this->items_count ?? null
            ),
            'rejection_reason' => $this->rejection_reason,
            'created_at' => $this->created_at,
        ];
    }
}
```

- [ ] **Step 8: Run to verify pass**

Run: `php artisan test --compact --filter=CreateLoanApplicationTest`
Expected: PASS (both tests).

- [ ] **Step 9: Commit**

```bash
git add app/Exceptions/InsufficientStockException.php app/Actions/CreateLoanApplication.php app/Http/Resources/LoanApplicationItemResource.php app/Http/Resources/LoanApplicationResource.php app/Models/LoanApplication.php database/factories/LoanApplicationFactory.php tests/Feature/CreateLoanApplicationTest.php
git commit -m "feat(api): add CreateLoanApplication action and loan resources"
```

---

## Task 6: Submit loan application endpoint

**Files:**
- Create: `app/Http/Requests/Api/StoreLoanApplicationRequest.php`
- Create: `app/Http/Controllers/Api/LoanApplicationController.php`
- Modify: `routes/api.php`
- Test: `tests/Feature/Api/LoanApplicationApiTest.php`

- [ ] **Step 1: Write the failing tests**

`tests/Feature/Api/LoanApplicationApiTest.php`:
```php
<?php

namespace Tests\Feature\Api;

use App\Models\District;
use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class LoanApplicationApiTest extends TestCase
{
    use RefreshDatabase;

    private function userWithDistrict(): User
    {
        $district = District::factory()->create();

        return User::factory()->create(['district_id' => $district->id]);
    }

    public function test_user_can_submit_loan_application(): void
    {
        $user = $this->userWithDistrict();
        Sanctum::actingAs($user);
        $item = Item::factory()->create(['available_quantity' => 5]);

        $this->postJson('/api/v1/loan-applications', [
            'items' => [['item_id' => $item->id, 'quantity' => 2]],
            'start_date' => now()->addDay()->toDateString(),
            'end_date' => now()->addDays(3)->toDateString(),
            'purpose' => 'Untuk program makmal sekolah',
        ])
            ->assertStatus(201)
            ->assertJsonPath('data.status', 'menunggu')
            ->assertJsonPath('data.items_count', 1);

        $this->assertDatabaseHas('loan_applications', [
            'user_id' => $user->id,
            'district_id' => $user->district_id,
            'status' => 'menunggu',
        ]);
        $this->assertDatabaseHas('loan_application_items', [
            'item_id' => $item->id,
            'quantity_requested' => 2,
        ]);
    }

    public function test_submission_fails_validation_with_short_purpose(): void
    {
        $user = $this->userWithDistrict();
        Sanctum::actingAs($user);
        $item = Item::factory()->create(['available_quantity' => 5]);

        $this->postJson('/api/v1/loan-applications', [
            'items' => [['item_id' => $item->id, 'quantity' => 1]],
            'start_date' => now()->addDay()->toDateString(),
            'end_date' => now()->addDays(3)->toDateString(),
            'purpose' => 'pendek',
        ])->assertStatus(422)->assertJsonValidationErrors('purpose');
    }

    public function test_submission_fails_when_stock_insufficient(): void
    {
        $user = $this->userWithDistrict();
        Sanctum::actingAs($user);
        $item = Item::factory()->create(['available_quantity' => 1]);

        $this->postJson('/api/v1/loan-applications', [
            'items' => [['item_id' => $item->id, 'quantity' => 5]],
            'start_date' => now()->addDay()->toDateString(),
            'end_date' => now()->addDays(3)->toDateString(),
            'purpose' => 'Untuk program makmal sekolah',
        ])->assertStatus(422);

        $this->assertDatabaseCount('loan_applications', 0);
    }

    public function test_submission_requires_authentication(): void
    {
        $this->postJson('/api/v1/loan-applications', [])->assertStatus(401);
    }
}
```

- [ ] **Step 2: Run to verify failure**

Run: `php artisan test --compact --filter=LoanApplicationApiTest`
Expected: FAIL (route `/api/v1/loan-applications` missing → 404, auth case may 404 too).

- [ ] **Step 3: Create the API form request**

`app/Http/Requests/Api/StoreLoanApplicationRequest.php`:
```php
<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class StoreLoanApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|integer|exists:items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'purpose' => 'required|string|min:10',
        ];
    }

    /**
     * @return array<int, array{id: int, quantity: int}>
     */
    public function normalizedItems(): array
    {
        return collect($this->input('items'))
            ->map(fn ($row) => ['id' => (int) $row['item_id'], 'quantity' => (int) $row['quantity']])
            ->all();
    }
}
```

- [ ] **Step 4: Create `LoanApplicationController` with `store`**

`app/Http/Controllers/Api/LoanApplicationController.php`:
```php
<?php

namespace App\Http\Controllers\Api;

use App\Actions\CreateLoanApplication;
use App\Exceptions\InsufficientStockException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreLoanApplicationRequest;
use App\Http\Resources\LoanApplicationResource;
use Illuminate\Http\JsonResponse;

class LoanApplicationController extends Controller
{
    public function store(StoreLoanApplicationRequest $request, CreateLoanApplication $action): JsonResponse
    {
        try {
            $application = $action->handle(
                $request->user(),
                $request->normalizedItems(),
                $request->validated('start_date'),
                $request->validated('end_date'),
                $request->validated('purpose'),
            );
        } catch (InsufficientStockException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return (new LoanApplicationResource($application))
            ->response()
            ->setStatusCode(201);
    }
}
```

- [ ] **Step 5: Add the route**

In `routes/api.php`, inside the `auth:sanctum` group, add:
```php
        Route::post('loan-applications', [\App\Http\Controllers\Api\LoanApplicationController::class, 'store']);
```

- [ ] **Step 6: Run to verify pass**

Run: `php artisan test --compact --filter=LoanApplicationApiTest`
Expected: PASS (all four tests).

- [ ] **Step 7: Commit**

```bash
git add app/Http/Requests/Api/StoreLoanApplicationRequest.php app/Http/Controllers/Api/LoanApplicationController.php routes/api.php tests/Feature/Api/LoanApplicationApiTest.php
git commit -m "feat(api): add submit loan application endpoint"
```

---

## Task 7: List and show own applications

**Files:**
- Modify: `app/Http/Controllers/Api/LoanApplicationController.php`
- Modify: `routes/api.php`
- Test: `tests/Feature/Api/LoanApplicationApiTest.php`

- [ ] **Step 1: Write the failing tests**

Append to `tests/Feature/Api/LoanApplicationApiTest.php` (add `use App\Models\LoanApplication;` to the imports):
```php
    public function test_user_sees_only_their_own_applications(): void
    {
        $user = $this->userWithDistrict();
        $other = $this->userWithDistrict();
        LoanApplication::factory()->create(['user_id' => $user->id, 'district_id' => $user->district_id]);
        LoanApplication::factory()->create(['user_id' => $other->id, 'district_id' => $other->district_id]);

        Sanctum::actingAs($user);

        $this->getJson('/api/v1/loan-applications')
            ->assertOk()
            ->assertJsonCount(1, 'data');
    }

    public function test_user_can_view_their_own_application(): void
    {
        $user = $this->userWithDistrict();
        $app = LoanApplication::factory()->create(['user_id' => $user->id, 'district_id' => $user->district_id]);

        Sanctum::actingAs($user);

        $this->getJson("/api/v1/loan-applications/{$app->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $app->id);
    }

    public function test_user_cannot_view_another_users_application(): void
    {
        $user = $this->userWithDistrict();
        $other = $this->userWithDistrict();
        $app = LoanApplication::factory()->create(['user_id' => $other->id, 'district_id' => $other->district_id]);

        Sanctum::actingAs($user);

        $this->getJson("/api/v1/loan-applications/{$app->id}")->assertStatus(403);
    }
```

- [ ] **Step 2: Run to verify failure**

Run: `php artisan test --compact --filter=LoanApplicationApiTest`
Expected: FAIL (index/show routes missing → 404).

- [ ] **Step 3: Add `index` and `show` to the controller**

Add these methods to `app/Http/Controllers/Api/LoanApplicationController.php`, and add the imports `use App\Models\LoanApplication;` and `use Illuminate\Http\Request;`:
```php
    public function index(Request $request)
    {
        $applications = LoanApplication::query()
            ->withCount('items')
            ->where('user_id', $request->user()->id)
            ->latest()
            ->paginate(10);

        return LoanApplicationResource::collection($applications);
    }

    public function show(Request $request, LoanApplication $loanApplication)
    {
        abort_if($loanApplication->user_id !== $request->user()->id, 403);

        return new LoanApplicationResource(
            $loanApplication->load('items.item', 'district')
        );
    }
```

- [ ] **Step 4: Add the routes**

In `routes/api.php`, inside the `auth:sanctum` group, add (keep the existing `store` route):
```php
        Route::get('loan-applications', [\App\Http\Controllers\Api\LoanApplicationController::class, 'index']);
        Route::get('loan-applications/{loanApplication}', [\App\Http\Controllers\Api\LoanApplicationController::class, 'show']);
```

- [ ] **Step 5: Run to verify pass**

Run: `php artisan test --compact --filter=LoanApplicationApiTest`
Expected: PASS (all seven tests).

- [ ] **Step 6: Commit**

```bash
git add app/Http/Controllers/Api/LoanApplicationController.php routes/api.php tests/Feature/Api/LoanApplicationApiTest.php
git commit -m "feat(api): add list and show own loan applications"
```

---

## Task 8: Refactor the web controller to use the shared action

**Files:**
- Modify: `app/Http/Controllers/User/UserLoanApplicationController.php`
- Test: `tests/Feature/WebLoanApplicationTest.php`

- [ ] **Step 1: Write a regression test for the web submission**

`tests/Feature/WebLoanApplicationTest.php`:
```php
<?php

namespace Tests\Feature;

use App\Models\District;
use App\Models\Item;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WebLoanApplicationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    public function test_user_can_submit_loan_application_via_web(): void
    {
        $district = District::factory()->create();
        $user = User::factory()->create(['district_id' => $district->id, 'is_active' => true]);
        $user->assignRole('user');
        $item = Item::factory()->create(['available_quantity' => 5]);

        $response = $this->actingAs($user)->post('/user/loan-applications', [
            'items' => [$item->id => 2],
            'start_date' => now()->addDay()->toDateString(),
            'end_date' => now()->addDays(3)->toDateString(),
            'purpose' => 'Untuk program makmal sekolah',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('loan_applications', [
            'user_id' => $user->id,
            'status' => 'menunggu',
        ]);
        $this->assertDatabaseHas('loan_application_items', [
            'item_id' => $item->id,
            'quantity_requested' => 2,
        ]);
    }
}
```
> Note: the web form posts `items` as a `{id: quantity}` map (see `StoreLoanApplicationRequest::getSelectedItems()`), which differs from the API's `[{item_id, quantity}]` shape. `RolePermissionSeeder` defines the `user` role with the `create-loan-application` and `view-own-applications` permissions (confirmed), which the web route `permission:create-loan-application` middleware requires.

- [ ] **Step 2: Run to verify it passes against current code**

Run: `php artisan test --compact --filter=WebLoanApplicationTest`
Expected: PASS (locks in current behaviour before the refactor).

- [ ] **Step 3: Refactor the controller to use the action**

Replace the body of `store()` in `app/Http/Controllers/User/UserLoanApplicationController.php` and update imports. New imports block (top of file):
```php
use App\Actions\CreateLoanApplication;
use App\Exceptions\InsufficientStockException;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLoanApplicationRequest;
use App\Models\Item;
use App\Models\LoanApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
```
New `store()` method:
```php
    public function store(StoreLoanApplicationRequest $request, CreateLoanApplication $action)
    {
        try {
            $application = $action->handle(
                Auth::user(),
                $request->getSelectedItems(),
                $request->validated('start_date'),
                $request->validated('end_date'),
                $request->validated('purpose'),
            );
        } catch (InsufficientStockException $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }

        return redirect()->route('user.loan-applications.show', $application->id)
            ->with('success', 'Permohonan pinjaman berjaya dihantar.');
    }
```

- [ ] **Step 4: Run to verify the test still passes**

Run: `php artisan test --compact --filter=WebLoanApplicationTest`
Expected: PASS (behaviour unchanged; logic now lives in the shared action).

- [ ] **Step 5: Commit**

```bash
git add app/Http/Controllers/User/UserLoanApplicationController.php tests/Feature/WebLoanApplicationTest.php
git commit -m "refactor: web loan submission uses shared CreateLoanApplication action"
```

---

## Task 9: Flutter integration guide

**Files:**
- Create: `docs/flutter-integration-guide.md`

- [ ] **Step 1: Write the guide**

Create `docs/flutter-integration-guide.md` with the following content:

````markdown
# Flutter Integration Guide — JPP Makmal User API

This guide shows how to connect a Flutter app to the user API: log in,
store the token, attach it to every request, browse items, and submit a
loan application (permohonan).

## 1. Packages

```yaml
# pubspec.yaml
dependencies:
  dio: ^5.4.0
  flutter_secure_storage: ^9.0.0
```

## 2. Base URL

All endpoints are prefixed with `/api/v1`. Example base URL during local
development with Laragon: `http://jpp-makmal.test/api/v1`.

## 3. API client with token interceptor

```dart
import 'package:dio/dio.dart';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';

class ApiClient {
  ApiClient() {
    dio = Dio(BaseOptions(
      baseUrl: 'http://jpp-makmal.test/api/v1',
      headers: {'Accept': 'application/json'},
    ));

    dio.interceptors.add(InterceptorsWrapper(
      onRequest: (options, handler) async {
        final token = await _storage.read(key: 'token');
        if (token != null) {
          options.headers['Authorization'] = 'Bearer $token';
        }
        handler.next(options);
      },
    ));
  }

  late final Dio dio;
  final _storage = const FlutterSecureStorage();
}
```

## 4. Login → store the token

The user only types email + password. The app stores the returned token
and never shows it.

```dart
class AuthService {
  AuthService(this._client);
  final ApiClient _client;
  final _storage = const FlutterSecureStorage();

  Future<void> login(String email, String password) async {
    final res = await _client.dio.post('/login', data: {
      'email': email,
      'password': password,
      'device_name': 'flutter-app',
    });
    await _storage.write(key: 'token', value: res.data['token']);
  }

  Future<void> logout() async {
    await _client.dio.post('/logout');
    await _storage.delete(key: 'token');
  }
}
```

Response shape from `POST /login`:

```json
{
  "token": "12|aBcD...",
  "user": { "id": 5, "name": "Ali", "email": "ali@jpp.gov.my",
            "district": { "id": 3, "name": "Hulu Langat" } }
}
```

On `422` the credentials are wrong (`errors.email`); on `429` the user
hit the login throttle (5 attempts / 15 minutes).

## 5. Browse available items

```dart
Future<List<dynamic>> fetchItems({String? search}) async {
  final res = await _client.dio.get('/items', queryParameters: {
    if (search != null && search.isNotEmpty) 'search': search,
  });
  return res.data['data']; // paginated; meta in res.data['meta']
}
```

Each item: `{ id, name, description, category, available_quantity, condition, image_url }`.

## 6. Submit a loan application (permohonan)

```dart
Future<Map<String, dynamic>> submitApplication({
  required List<Map<String, int>> items, // [{ 'item_id': 12, 'quantity': 2 }]
  required String startDate,             // 'YYYY-MM-DD'
  required String endDate,
  required String purpose,               // min 10 characters
}) async {
  final res = await _client.dio.post('/loan-applications', data: {
    'items': items,
    'start_date': startDate,
    'end_date': endDate,
    'purpose': purpose,
  });
  return res.data['data']; // 201 Created → the new application
}
```

Validation/stock errors return `422`:
- Validation: `{ "message": ..., "errors": { "purpose": ["..."] } }`
- Insufficient stock: `{ "message": "Stok Mikroskop tidak mencukupi. Tersedia: 1" }`

## 7. View own applications & status

```dart
Future<List<dynamic>> fetchMyApplications() async {
  final res = await _client.dio.get('/loan-applications');
  return res.data['data'];
}

Future<Map<String, dynamic>> fetchApplication(int id) async {
  final res = await _client.dio.get('/loan-applications/$id');
  return res.data['data'];
}
```

`status` values: `menunggu`, `diluluskan`, `ditolak`, `dibatalkan`,
`dipinjam`, `dikembalikan`.

## 8. End-to-end flow

1. `AuthService.login(email, password)` → token stored.
2. `fetchItems()` → user picks items + quantities.
3. `submitApplication(...)` → `201`, status `menunggu`.
4. `fetchMyApplications()` / `fetchApplication(id)` → track status.
5. `AuthService.logout()` → token revoked + cleared.

## 9. Endpoint reference

| Method | Path | Auth | Purpose |
|--------|------|:----:|---------|
| POST | `/api/v1/login` | — | email+password → token + user |
| POST | `/api/v1/logout` | ✅ | revoke current token |
| GET | `/api/v1/user` | ✅ | current user |
| GET | `/api/v1/items` | ✅ | available items (`search`, `category_id`, `page`) |
| GET | `/api/v1/items/{id}` | ✅ | item detail |
| GET | `/api/v1/loan-applications` | ✅ | own applications |
| POST | `/api/v1/loan-applications` | ✅ | submit application |
| GET | `/api/v1/loan-applications/{id}` | ✅ | own application detail |
````

- [ ] **Step 2: Commit**

```bash
git add docs/flutter-integration-guide.md
git commit -m "docs: add Flutter integration guide for the user API"
```

---

## Task 10: Final verification

- [ ] **Step 1: Run the full test suite**

Run: `php artisan test --compact`
Expected: PASS (all tests, including the pre-existing suite).

- [ ] **Step 2: Format**

Run: `vendor/bin/pint --dirty --format agent`
Expected: clean / files formatted.

- [ ] **Step 3: Commit any formatting**

```bash
git add -A
git commit -m "style: apply Pint formatting"
```

---

## Self-Review Notes

- **Spec coverage:** every endpoint in spec §3 maps to a task (login T2; logout/me T3; items T4; submit T6; index/show T7). Sanctum auth (spec §4) → T1–T3. Resources (spec §6) → T2/T4/T5. Shared action (spec §7) → T5, consumed in T6 and T8. Error handling (spec §8) verified by 401/403/422/429 assertions across T2–T7. Tests (spec §9) → each feature task. Flutter guide (spec §10 deliverable 7) → T9.
- **Stock semantics:** applications do **not** decrement `available_quantity` (matching existing web behaviour — decrement happens later at approval/loan stage, out of scope). The action only re-checks stock.
- **Type consistency:** action signature `handle(User, array<{id,quantity}>, string, string, string)` is used identically in T6 (API, via `normalizedItems()`) and T8 (web, via `getSelectedItems()`). Resource property names match model attributes verified against migrations.
- **Role/permission verified:** `RolePermissionSeeder` defines the `user` role with `create-loan-application` + `view-own-applications`, satisfying the web route's `permission:create-loan-application` middleware used in the T8 regression test.
