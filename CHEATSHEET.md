# Laravel + PHP Cheatsheet — BookXchange Project

---

## 1. PHP Basics Worth Knowing First

```php
// Variables — always prefixed with $
$title = 'Dune';
$price = 9.99;
$available = true;
$nothing = null;

// Arrays
$conditions = ['new', 'good', 'fair', 'poor'];  // indexed
$book = ['title' => 'Dune', 'author' => 'Herbert'];  // associative (like a dict)

// String interpolation
echo "Book: $title";
echo "Book: {$book['title']}";  // use {} for array/object access

// Null coalescing (very common in Laravel views)
$q = $_GET['q'] ?? '';              // use '' if 'q' is not set
$name = $user->username ?? 'Guest'; // use 'Guest' if null

// Arrow functions (used in your routes/web.php)
$double = fn($n) => $n * 2;

// Type checking
is_null($var);
is_array($var);
gettype($var);  // "string", "integer", "array", "object", ...
```

---

## 2. Project Structure

```
app/
  Http/
    Controllers/     ← AuthController, CatalogController, ProfileController (add more here)
  Models/            ← Book, User, Exchange, Category, Dispute
  Providers/         ← service registration (rarely touched)

bootstrap/           ← Laravel boot files (don't touch)

config/              ← app, auth, database, mail, session settings
  app.php            ← APP_NAME, locale, timezone
  auth.php           ← auth guards and providers
  database.php       ← DB connections (reads from .env)

database/
  factories/         ← fake data generators for testing
  migrations/        ← DB schema version control
  seeders/           ← scripts to populate the DB

public/              ← web server root (index.php, css, images)
  css/style.css

resources/
  views/             ← Blade templates
    layouts/app.blade.php   ← main layout
    books/
    catalog/
    auth/
    admin/
    profile/

routes/
  web.php            ← all HTTP routes for the browser

.env                 ← environment-specific secrets (never commit!)
artisan              ← CLI entry point
composer.json        ← PHP dependencies (like package.json for Node)
```

---

## 3. MVC Request Flow

```
Browser → routes/web.php → Controller method
                                  ↓
                         Model (Eloquent / DB query)
                                  ↓
                         View (Blade template) → HTML → Browser
```

Your current routes skip the Controller step (closures `fn() => view(...)`).
That works for static pages; you'll need Controllers for anything that touches the DB.

---

## 4. Routes (`routes/web.php`)

```php
// Static (current approach — fine for mockups)
Route::get('/contact', fn() => view('contact'))->name('contact');

// With a Controller
Route::get('/books',        [BookController::class, 'index'])->name('books.index');
Route::get('/books/create', [BookController::class, 'create'])->name('books.create');
Route::post('/books',       [BookController::class, 'store'])->name('books.store');
Route::get('/books/{id}',   [BookController::class, 'show'])->name('books.show');
Route::put('/books/{id}',   [BookController::class, 'update'])->name('books.update');
Route::delete('/books/{id}',[BookController::class, 'destroy'])->name('books.destroy');

// Shortcut: all 7 CRUD routes in one line
Route::resource('books', BookController::class);

// Protected routes (redirect to /login if not authenticated)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::post('/books',  [BookController::class, 'store'])->name('books.store');
});

// Admin-only routes (add a custom middleware or check inside controller)
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/categories', [Admin\CategoryController::class, 'index']);
    Route::get('/disputes',   [Admin\DisputeController::class, 'index']);
});
```

**Generate URLs in Blade — always use named routes, never hardcode:**
```blade
{{ route('books.show', ['id' => $book->id]) }}  {{-- /books/42 --}}
{{ route('catalog.index', ['category' => 'fiction']) }}  {{-- /catalog?category=fiction --}}
```

---

## 5. Controllers

```bash
# Generate a controller
php artisan make:controller BookController

# Generate a resource controller (has index/create/store/show/edit/update/destroy)
php artisan make:controller BookController --resource
```

```php
namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;

class BookController extends Controller
{
    // GET /books
    public function index()
    {
        $books = Book::with(['owner', 'category'])
                     ->where('status', 'available')
                     ->latest()
                     ->paginate(12);

        return view('catalog.index', compact('books'));
        // compact('books') is shorthand for ['books' => $books]
    }

    // GET /books/{id}
    public function show(int $id)
    {
        $book = Book::with(['owner', 'category', 'exchanges'])->findOrFail($id);
        return view('books.show', compact('book'));
    }

    // POST /books
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => 'required|max:200',
            'author'      => 'required|max:150',
            'isbn'        => 'nullable|max:20',
            'condition'   => 'required|in:new,good,fair,poor',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
        ]);

        $validated['user_id'] = auth()->id();
        $validated['status']  = 'available';

        Book::create($validated);

        return redirect()->route('catalog.index')
                         ->with('success', 'Book published!');
    }

    // DELETE /books/{id}
    public function destroy(Book $book)  // route-model binding: auto-fetches by id
    {
        $this->authorize('delete', $book);  // check user owns the book
        $book->delete();
        return redirect()->route('profile.index')->with('success', 'Book removed.');
    }
}
```

---

## 6. Route–Model Binding

Instead of fetching manually with `findOrFail`, Laravel can inject the model directly:

```php
// routes/web.php
Route::get('/books/{book}', [BookController::class, 'show']);

// Controller — Laravel auto-fetches Book where id = {book}; 404 if not found
public function show(Book $book)
{
    return view('books.show', compact('book'));
}
```

---

## 7. Eloquent Models & Relationships

```php
// ── Fetching ──────────────────────────────────────────────────────
Book::all();                                    // every row
Book::find(5);                                  // by PK, null if missing
Book::findOrFail(5);                            // by PK, 404 if missing
Book::where('status', 'available')->get();      // filtered collection
Book::where('status', 'available')->first();    // single row or null
Book::where('status', 'available')->firstOrFail(); // single row or 404

// ── Chaining conditions ───────────────────────────────────────────
Book::where('status', 'available')
    ->where('category_id', 3)
    ->orderBy('created_at', 'desc')
    ->limit(10)
    ->get();

// ── Search (LIKE) ─────────────────────────────────────────────────
Book::where('title', 'LIKE', "%{$query}%")
    ->orWhere('author', 'LIKE', "%{$query}%")
    ->get();

// ── Creating ──────────────────────────────────────────────────────
Book::create(['title' => 'Dune', 'author' => 'Herbert', 'user_id' => 1, ...]);

// Via relationship (automatically sets user_id)
auth()->user()->books()->create(['title' => 'Dune', ...]);

// ── Updating ──────────────────────────────────────────────────────
$book->update(['status' => 'exchanged']);
Book::where('status', 'pending')->update(['status' => 'available']); // bulk

// ── Deleting ──────────────────────────────────────────────────────
$book->delete();
Book::destroy(5);      // by PK
Book::destroy([1,2,3]); // multiple PKs

// ── Eager loading (avoids N+1 queries — always use for lists) ────
$books = Book::with(['owner', 'category'])->get();
// Without this, accessing $book->owner in a loop fires one DB query per book

// ── Relationships already in your models ─────────────────────────
$book->owner;          // User
$book->category;       // Category
$book->exchanges;      // Collection<Exchange>
$exchange->requester;  // User
$exchange->owner;      // User
$exchange->book;       // Book
$exchange->dispute;    // Dispute|null
$category->books;      // Collection<Book>
```

**Relationship types:**
| Declaration | Meaning | Example |
|---|---|---|
| `belongsTo(User::class)` | This model has a FK column | Book belongs to User |
| `hasMany(Exchange::class)` | Other model has a FK to this | Book has many Exchanges |
| `hasOne(Dispute::class)` | Other model has a FK, only one | Exchange has one Dispute |
| `belongsToMany(...)` | Join table between two models | (not used yet, but useful for wishlists) |

---

## 8. Migrations

```bash
php artisan make:migration create_reviews_table           # new table
php artisan make:migration add_rating_to_books_table      # add column
php artisan migrate                  # run pending migrations
php artisan migrate:rollback         # undo last batch
php artisan migrate:status           # see which have run
php artisan migrate:fresh            # drop all + re-migrate (dev only!)
php artisan migrate:fresh --seed     # drop all + re-migrate + seed data
```

**Common column types:**
```php
$table->id();                                         // BIGINT unsigned auto-increment PK
$table->string('title', 200);                         // VARCHAR(200)
$table->text('description')->nullable();              // TEXT, optional
$table->integer('page_count')->unsigned();            // INT unsigned
$table->decimal('price', 8, 2);                      // DECIMAL(8,2)
$table->boolean('is_verified')->default(false);       // TINYINT 0/1
$table->enum('status', ['available','pending']);       // fixed value set
$table->timestamp('verified_at')->nullable();         // TIMESTAMP
$table->timestamps();                                 // created_at + updated_at
$table->softDeletes();                               // deleted_at (see below)

// Foreign keys
$table->foreignId('user_id')->constrained();          // FK → users.id
$table->foreignId('user_id')->constrained()
      ->cascadeOnDelete();                            // delete child when parent deleted
$table->foreignId('admin_id')->nullable()
      ->constrained('users')->nullOnDelete();         // set null when parent deleted
```

**Add a column to an existing table:**
```php
public function up(): void
{
    Schema::table('books', function (Blueprint $table) {
        $table->string('language', 10)->default('en')->after('isbn');
    });
}

public function down(): void
{
    Schema::table('books', function (Blueprint $table) {
        $table->dropColumn('language');
    });
}
```

---

## 9. Validation

```php
// In a Controller method
$validated = $request->validate([
    'title'       => 'required|string|max:200',
    'author'      => 'required|string|max:150',
    'isbn'        => 'nullable|digits:13',
    'condition'   => 'required|in:new,good,fair,poor',
    'category_id' => 'required|exists:categories,id',
    'cover_image' => 'nullable|image|mimes:jpeg,png,webp|max:2048', // 2 MB
    'email'       => 'required|email|unique:users,email',
    'password'    => 'required|min:8|confirmed',  // needs password_confirmation field
]);
// If validation fails, Laravel redirects back with errors automatically.
```

**Show errors in Blade:**
```blade
@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

{{-- Per-field error --}}
<input name="title" value="{{ old('title') }}" class="form-control @error('title') is-invalid @enderror">
@error('title')
    <div class="invalid-feedback">{{ $message }}</div>
@enderror
```

`old('title')` re-fills the input with the previously submitted value after a failed validation.

---

## 10. Blade Templates

**Layout (`layouts/app.blade.php`) defines slots:**
```blade
@yield('title')       {{-- simple string slot --}}
@yield('content')     {{-- main content slot --}}
@stack('styles')      {{-- stackable — multiple pushes merged --}}
@stack('scripts')
```

**Child views fill those slots:**
```blade
@extends('layouts.app')

@section('title', 'Book Catalog')

@section('content')
    <h1>Books</h1>
    @foreach ($books as $book)
        <p>{{ $book->title }} — {{ $book->author }}</p>
    @endforeach
@endsection

@push('scripts')
    <script>console.log('loaded')</script>
@endpush
```

**All common directives:**
```blade
{{ $var }}                   {{-- echo (HTML-escaped, safe) --}}
{!! $html !!}                {{-- echo raw HTML (only use for trusted content!) --}}

@if ($condition) ... @elseif (...) ... @else ... @endif
@unless ($condition) ... @endunless   {{-- opposite of @if --}}

@foreach ($books as $book) ... @endforeach
@forelse ($books as $book)
    ...
@empty
    <p>No books found.</p>   {{-- shown when collection is empty --}}
@endforelse

@for ($i = 0; $i < 10; $i++) ... @endfor

@auth   ...  @endauth        {{-- shown only to logged-in users --}}
@guest  ...  @endguest       {{-- shown only to guests --}}

@if (auth()->user()?->isAdmin())  ...  @endif   {{-- admin check --}}

{{-- This is a Blade comment — not sent to the browser --}}
```

**Partial views (reusable components):**
```blade
{{-- Include another view file --}}
@include('partials.book-card', ['book' => $book])
```

**Helpers used in your views:**
```blade
{{ route('catalog.index') }}                    {{-- named route URL --}}
{{ route('books.show', ['id' => $book->id]) }}  {{-- route with param --}}
{{ asset('css/style.css') }}                    {{-- URL to public/ file --}}
{{ request('q') }}                              {{-- current query param --}}
{{ request()->routeIs('profile.index') }}       {{-- true if current route --}}
{{ date('Y') }}                                 {{-- current year --}}
```

---

## 11. Forms, CSRF & Method Spoofing

```blade
{{-- POST form — @csrf required or Laravel returns 419 --}}
<form method="POST" action="{{ route('books.store') }}">
    @csrf
    <input name="title" type="text" class="form-control" value="{{ old('title') }}">
    <button type="submit" class="btn btn-primary">Save</button>
</form>

{{-- PUT/PATCH/DELETE — HTML only supports GET/POST, use @method to spoof --}}
<form method="POST" action="{{ route('books.update', $book) }}">
    @csrf
    @method('PUT')
    ...
</form>

<form method="POST" action="{{ route('books.destroy', $book) }}">
    @csrf
    @method('DELETE')
    <button type="submit">Delete</button>
</form>
```

---

## 12. Authentication

```blade
{{-- In Blade --}}
@auth
    <span>Hello, {{ auth()->user()->username }}</span>
@endauth

@guest
    <a href="{{ route('login') }}">Log in</a>
@endguest

@if (auth()->user()?->isAdmin())
    <a href="{{ route('admin.categories') }}">Admin Panel</a>
@endif
```

```php
// In Controllers
auth()->check();          // true if logged in
auth()->guest();          // true if not logged in
auth()->user();           // the logged-in User model (or null)
auth()->id();             // the logged-in user's ID (or null)
auth()->user()->isAdmin() // your custom method on the User model

// Log in programmatically
auth()->login($user);
auth()->logout();

// Log in and remember
Auth::attempt(['email' => $email, 'password' => $password], $remember);
```

**Protect routes with middleware:**
```php
// In routes/web.php
Route::middleware('auth')->group(function () { ... });

// In a Controller constructor (protects all methods)
public function __construct()
{
    $this->middleware('auth');
    $this->middleware('auth')->except(['index', 'show']); // allow guests to browse
}
```

---

## 13. Session & Flash Messages

```php
// In a Controller — store a flash message after a redirect
return redirect()->route('catalog.index')->with('success', 'Book published!');
return redirect()->back()->with('error', 'Something went wrong.');

// Read session values manually
session()->put('key', 'value');
session()->get('key', 'default');
session()->forget('key');
session()->flush(); // clear everything
```

```blade
{{-- In your layout — show flash messages --}}
@if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

@if (session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif
```

---

## 14. File Uploads

Relevant for `cover_image` on your `books` table.

```blade
{{-- Form must have enctype --}}
<form method="POST" action="{{ route('books.store') }}" enctype="multipart/form-data">
    @csrf
    <input type="file" name="cover_image" accept="image/*">
    <button type="submit">Upload</button>
</form>
```

```php
// In controller
public function store(Request $request)
{
    $request->validate([
        'cover_image' => 'nullable|image|mimes:jpeg,png,webp|max:2048',
    ]);

    $path = null;
    if ($request->hasFile('cover_image')) {
        // Stores in storage/app/public/covers/ and returns the path
        $path = $request->file('cover_image')->store('covers', 'public');
    }

    Book::create([...$validated, 'cover_image' => $path]);
}
```

```blade
{{-- Display the image --}}
@if ($book->cover_image)
    <img src="{{ Storage::url($book->cover_image) }}" alt="Cover">
@else
    <img src="{{ asset('css/no-cover.png') }}" alt="No cover">
@endif
```

```bash
# Create the symbolic link so public disk files are accessible via browser
php artisan storage:link
```

---

## 15. Pagination

```php
// Controller — replace ->get() with ->paginate()
$books = Book::with(['owner', 'category'])
             ->where('status', 'available')
             ->paginate(12);  // 12 per page
```

```blade
{{-- Blade — render page links (Bootstrap-styled) --}}
{{ $books->links() }}

{{-- With search params preserved in page links --}}
{{ $books->appends(request()->query())->links() }}
```

Configure Bootstrap 5 pagination style in `App\Providers\AppServiceProvider` (already done):
```php
use Illuminate\Pagination\Paginator;

public function boot(): void
{
    Paginator::useBootstrapFive();
}
```

---

## 16. Collections

Eloquent always returns a `Collection` for multi-row queries (not a plain array). Collections have many useful methods:

```php
$books = Book::all();

$books->count();
$books->first();
$books->last();
$books->isEmpty();
$books->isNotEmpty();

// Filter in PHP (after fetching from DB)
$available = $books->where('status', 'available');
$byAuthor  = $books->where('author', 'Herbert');

// Transform
$titles = $books->pluck('title');              // Collection of titles
$titles = $books->pluck('title', 'id');        // ['id' => 'title'] map
$groups = $books->groupBy('condition');        // grouped by field

// Sorting
$sorted = $books->sortBy('title');
$sorted = $books->sortByDesc('created_at');

// Map & each
$books->each(fn($book) => logger($book->title));
$upper = $books->map(fn($book) => strtoupper($book->title));
```

> **Tip:** Filter in the DB query (`.where(...)`) when possible — it's faster than fetching everything and filtering in PHP.

---

## 17. Seeders & Factories

Seeders populate the database with test data.

```bash
php artisan make:seeder BookSeeder
php artisan make:factory BookFactory --model=Book
php artisan db:seed                   # run DatabaseSeeder
php artisan db:seed --class=BookSeeder  # run one seeder
php artisan migrate:fresh --seed      # fresh DB + seed
```

**Factory example:**
```php
// database/factories/BookFactory.php
class BookFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title'       => fake()->sentence(3),
            'author'      => fake()->name(),
            'isbn'        => fake()->isbn13(),
            'description' => fake()->paragraph(),
            'condition'   => fake()->randomElement(['new', 'good', 'fair', 'poor']),
            'status'      => 'available',
            'user_id'     => User::factory(),       // creates a User too
            'category_id' => Category::factory(),
        ];
    }
}
```

**Seeder example:**
```php
// database/seeders/DatabaseSeeder.php
public function run(): void
{
    User::factory()->create(['email' => 'admin@example.com', 'role' => 'admin']);
    User::factory(10)->create();
    Book::factory(50)->create();
}
```

**Use in Tinker for quick tests:**
```bash
php artisan tinker
>>> Book::factory(5)->create()
```

---

## 18. Soft Deletes

Instead of physically removing rows, soft deletes set a `deleted_at` timestamp.
Useful for disputes or exchanges where you want to keep a record.

```php
// In migration
$table->softDeletes();

// In model
use Illuminate\Database\Eloquent\SoftDeletes;

class Exchange extends Model
{
    use SoftDeletes;
    ...
}

// Usage
$exchange->delete();          // sets deleted_at, not actually deleted
$exchange->restore();         // clears deleted_at
$exchange->forceDelete();     // actually removes the row

Exchange::withTrashed()->get();        // includes soft-deleted
Exchange::onlyTrashed()->get();        // only soft-deleted
```

---

## 19. Authorization (Gates & Policies)

Gates and Policies let you check if a user is allowed to do something, separate from your controller logic.

```bash
php artisan make:policy BookPolicy --model=Book
```

```php
// app/Policies/BookPolicy.php
class BookPolicy
{
    public function update(User $user, Book $book): bool
    {
        return $user->id === $book->user_id;  // only owner can edit
    }

    public function delete(User $user, Book $book): bool
    {
        return $user->id === $book->user_id || $user->isAdmin();
    }
}
```

```php
// In a Controller
public function destroy(Book $book)
{
    $this->authorize('delete', $book);  // throws 403 if not allowed
    $book->delete();
    return redirect()->route('profile.index');
}
```

```blade
{{-- In Blade --}}
@can('delete', $book)
    <button>Delete</button>
@endcan

@cannot('update', $book)
    <p>You cannot edit this book.</p>
@endcannot
```

---

## 20. Environment Variables (`.env`)

Never hardcode secrets — use `.env` and never commit it.

```ini
APP_NAME="Book Exchange"
APP_ENV=local          # local | production
APP_DEBUG=true         # false in production!
APP_URL=http://localhost

DB_CONNECTION=mariadb
DB_HOST=mariadb
DB_DATABASE=bookexchange
DB_USERNAME=sail
DB_PASSWORD=password
```

**Read in code:**
```php
env('APP_NAME')           // 'Book Exchange'
config('app.name')        // preferred — reads from config/app.php
config('database.connections.mariadb.host')
```

> After editing `.env`, run `php artisan config:clear` to clear the cached config.

---

## 21. Artisan — Commands Reference

> **This project uses Laravel Sail.** Prefix every `artisan` command with `./vendor/bin/sail`:
> ```bash
> ./vendor/bin/sail artisan migrate
> ./vendor/bin/sail artisan tinker
> # etc.
> ```
> The examples below use the short form for readability.

```bash
# Development
php artisan tinker                        # interactive REPL (test Eloquent live)

# Code generation
php artisan make:controller BookController --resource
php artisan make:model Review -m          # model + migration
php artisan make:migration add_col_to_table
php artisan make:seeder BookSeeder
php artisan make:factory BookFactory --model=Book
php artisan make:policy BookPolicy --model=Book
php artisan make:middleware AdminMiddleware

# Database
php artisan migrate
php artisan migrate:rollback
php artisan migrate:status
php artisan migrate:fresh --seed          # ⚠ destroys all data

# Routes
php artisan route:list                    # see all routes with names and methods

# Cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan optimize:clear                # clears all caches at once

# Storage
php artisan storage:link                  # link public disk for file uploads
```

---

## 22. Debugging

```php
// Dump and die — stops execution and prints variable
dd($book);
dd($books->toArray());

// Dump without stopping
dump($book);

// Log to storage/logs/laravel.log
logger('Something happened');
logger()->info('Book created', ['id' => $book->id]);
logger()->error('Failed', ['exception' => $e->getMessage()]);

// View the log
tail -f storage/logs/laravel.log
```

Enable **Laravel Debugbar** (dev only) for a visual toolbar showing queries, routes, and timing:
```bash
composer require barryvdh/laravel-debugbar --dev
```

---

## 23. Common Gotchas

| Problem | Cause | Fix |
|---|---|---|
| **419 Page Expired** | Missing `@csrf` in form | Add `@csrf` inside every POST form |
| **MassAssignmentException** | Field not in `#[Fillable]` | Add the field to the `#[Fillable]` attribute on the model |
| **N+1 queries** | Accessing relation in a loop without eager loading | Use `->with(['owner', 'category'])` on the query |
| **404 on route** | Route not defined or wrong name | Run `php artisan route:list` to check |
| **403 Forbidden** | Failed a Policy/Gate check | Check the policy logic or wrap in `@can` |
| **Blade shows raw `{{ }}`** | File not saved as `.blade.php` | Rename the file to `name.blade.php` |
| **Old form values lost** | Not using `old()` in input | Use `value="{{ old('title') }}"` on inputs |
| **Changes to `.env` ignored** | Config cached | Run `php artisan config:clear` |
| **Images not displaying** | Public storage link missing | Run `php artisan storage:link` |
| **Sidebar categories hardcoded** | Static HTML | Replace with `Category::all()` passed from controller |
| **`auth()` returns null** | Accessing outside of request lifecycle | Only call `auth()` inside controllers, middleware, or Blade |
