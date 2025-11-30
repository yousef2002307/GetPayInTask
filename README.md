# GetPayInTask

## ✨ Quick Technical Summary

This project implements a robust product management system with a high-performance inventory reservation mechanism. Key technical achievements include:

- **Product API**: Built using **Repository Pattern** and **API Resources** for clean separation of concerns and consistent responses.
- **Hold System**: Implemented a temporary stock reservation system (`POST /api/holds`) that immediately locks inventory.  uses transcation when updating the stock to avoid race conditions
- **Automated Cleanup**: A scheduled task runs every **5 seconds** to identify expired holds.
- **High-Performance Queueing**: Uses **Redis queues** with a **chunking strategy** (processing 100 records at a time) to dispatch individual deletion jobs. This ensures scalable, parallel processing of expired holds without memory leaks.

## What This Project Is About

Hey there! 👋 This is a Laravel-based API project that I built to demonstrate clean coding practices and proper software architecture. The main goal was to create a simple yet professional product management system that follows industry best practices.

## The Challenge

The task was straightforward: build an API endpoint to fetch product information. But instead of just throwing together a quick solution with database queries directly in the controller (which, let's be honest, we've all done at some point 😅), I wanted to do it the *right way* - using proper design patterns that make the code maintainable, testable, and scalable.

## What I Built

### The Product API

I created a RESTful API endpoint that allows you to retrieve product details by their ID. When you hit the endpoint, you get back nicely formatted JSON with all the product information you need.

**Example Request:**
```
GET /api/products/1
```

**Example Response:**
```json
{
  "data": {
    "id": 1,
    "stock": 31,
    "price": "810.90",
    "created_at": "2025-11-29T05:40:23+00:00",
    "updated_at": "2025-11-29T05:40:23+00:00"
  }
}
```

If the product doesn't exist, you'll get a friendly 404 error message instead of a cryptic database error.

## How It's Structured (The Cool Part!)

### 1. **Repository Pattern** 📚

Instead of writing database queries directly in the controller, I created a `ProductRepository` class. Think of it as a dedicated librarian who knows exactly where to find the books (data) you need.

- **Location:** `app/Repositories/ProductRepository.php`
- **What it does:** Handles all the database operations for products
- **Why it's awesome:** If I ever need to change how we fetch products (maybe switch databases, add caching, or whatever), I only need to update this one file. The controller doesn't even know or care!

### 2. **API Resources** ✨

I used Laravel's API Resources (`ProductResource`) to transform the raw database data into a clean, consistent JSON format.

- **Location:** `app/Http/Resources/ProductResource.php`
- **What it does:** Takes the messy database model and turns it into beautiful, formatted JSON
- **Why it's awesome:** The price gets properly formatted, dates are in ISO format, and if I ever need to add or remove fields from the API response, I just update this one file. No hunting through controllers!

### 3. **Clean Controller** 🎯

The `ProductController` is super clean and focused. It doesn't worry about database queries or JSON formatting - it just coordinates between the repository and the resource.

- **Location:** `app/Http/Controllers/Api/ProductController/ProductController.php`
- **What it does:** 
  - Receives the request
  - Asks the repository for the product
  - Wraps it in a resource
  - Returns the response
- **Bonus:** It even logs errors when products aren't found, which is super helpful for debugging!

### 4. **Database Setup** 🗄️

The products table is simple but effective:
- `id` - Unique identifier
- `stock` - How many items we have (can't be negative!)
- `price` - Stored as a decimal for accuracy
- `created_at` & `updated_at` - Automatic timestamps

I also created a factory and seeder so you can quickly populate the database with test data.

### 5. **The Hold System (Advanced Feature)** ⏳

This is where things get really interesting! I implemented a temporary reservation system (like when you're buying tickets and they are "held" for 5 minutes).

#### How It Works:

1.  **Creating a Hold:**
    *   Endpoint: `POST /api/holds`
    *   When you request a hold, the system checks if enough stock is available.
    *   If yes, it immediately **decrements the product stock** and creates a `Hold` record with an expiration time (2 minutes).
    * uses transcation when updating the stock to avoid race conditions
    * uses lockForUpdate() to avoid race conditions
    * uses DB::beginTransaction() and DB::commit() to avoid race conditions
    * check stock quantity before decrementing
    * if stock is not available, return error message with available stock

2.  **Automatic Cleanup (The "Magic" Part):**
    *   I didn't want expired holds to sit there forever blocking stock.
    *   I set up a **Scheduled Task** (`routes/console.php`) that runs every **5 seconds**.
    *   This triggers the `CleanupExpiredHoldsJob`.

3.  **Scalable Background Processing:**
    *   Instead of trying to delete thousands of expired holds in one go (which would crash the server), I used a **Chunking Strategy**.
    *   The `CleanupExpiredHoldsJob` finds expired holds in batches of 100.
    *   For *each* expired hold, it dispatches a separate `ExpiredHoldJob` to the queue.
    *   **Why?** This allows multiple queue workers to process expiring holds in parallel and prevents memory overflows.

4.  **Stock Restoration:**
    *   When the `ExpiredHoldJob` runs, it adds the quantity back to the product's stock and expires the hold record.

### 6. The Order System 📦

Once a user decides to purchase the held product, the order system takes over. This isn't just a simple insert; it's a critical state transition.

#### How It Works:

1.  **Endpoint:** `POST /api/orders`
2.  **Strict Validation:**
    *   The system validates that the `hold_id` exists.
    *   **Custom Rule (`IsHoldExpiredOrUsed`)**: Verifies that the hold hasn't expired AND hasn't been used yet. This prevents double-spending of reservations.
3.  **Atomic Processing:**
    *   We use a **Database Transaction** to ensure data integrity.
    *   **Pessimistic Locking**: We use `lockForUpdate()` on the hold record. This effectively "locks" the row, preventing any other process from modifying it until our transaction completes.
    *   The hold is marked as `is_used = true`.
    *   The order is created with a default `payment_status` of `pending`.

## Why This Approach Matters

You might be thinking, "Wow, that's a lot of files for just fetching a product!" And you're right - it is more files than just slapping a query in the controller. But here's why it's worth it:

1. **Testability:** I can easily mock the repository in tests without touching a real database
2. **Maintainability:** Each piece has one job and does it well
3. **Scalability:** When the app grows (and it will!), this structure won't become a tangled mess
4. **Team-Friendly:** Other developers can jump in and immediately understand what each part does
5. **Flexibility:** Want to add caching? Just update the repository. Need to change the JSON format? Just update the resource. Easy peasy!

## How to Use It

1. **Set up the database:**
   ```bash
   php artisan migrate
   ```

2. **Add some test products:**
   ```bash
   php artisan db:seed --class=ProductSeeder
   ```

3. **Start the server:**
   ```bash
   php artisan serve
   ```

4. **Test the API:**
   ```bash
   curl http://localhost:8000/api/products/1
   ```

## What I Learned

This project reinforced some important lessons:
- Taking a bit more time upfront to structure things properly saves *tons* of time later
- Separation of concerns isn't just a fancy term - it genuinely makes code better
- Good architecture isn't about being fancy; it's about making future changes easier

## Tech Stack

- **Laravel 12** - The PHP framework that makes web development enjoyable
- **MySQL** - For data storage
- **PHP 8.2+** - With all those nice type hints and modern features
- **Redis** - For queue processing and caching
- **Xdebug** - For debugging
- **log-viewer** - For viewing logs
-**phpstan/larastan** - For static analysis
- **Laravel Telescope** - For debugging

---

Built with ☕ and a commitment to clean code!
