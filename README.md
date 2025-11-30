# GetPayInTask

A robust, high-performance Laravel API for product management with inventory reservation.

## 🚀 How to Run

1.  **Setup Database:**
    ```bash
    php artisan migrate
   
    ```

2.  **Start Services:**
    ```bash
    # Terminal 1: Start API Server
    php artisan serve

    # Terminal 2: Start Queue Worker (Required for Holds & Webhooks)
    php artisan queue:work

    # Terminal 3: Start Scheduler (Required for Hold Cleanup)
    php artisan schedule:work
    ```

3.  **Test API:**
    ```bash
    curl http://localhost:8000/api/products/1
    curl http://localhost:8000/api/holds
    curl http://localhost:8000/api/orders
    curl http://localhost:8000/api/payments/webhook
    ```

---

## 🛠️ What I Built (Key Features)

This project demonstrates clean architecture and high-concurrency handling using **Laravel 12**, **MySQL**, and **Redis**.

### 1. Product API (Clean Architecture)
*   **Repository Pattern:** Decoupled database logic from controllers (`ProductRepository`).
*   **API Resources:** Consistent JSON response formatting.
*   **Smart Caching:** Implemented **Cache-Aside** strategy with Redis. Product data is cached for speed and automatically invalidated/cleared when stock changes.

### 2. Hold System (Inventory Reservation)
*   **Endpoint:** `POST /api/holds`
*   **Concurrency Control:** Uses **Atomic Transactions** and `lockForUpdate()` to prevent race conditions when reserving stock.
*   **Auto-Cleanup:** A scheduled task runs every 5 seconds to release expired holds.
*   **Scalability:** Uses **Redis Queues** with chunking to process expired holds in parallel without memory leaks.

### 3. Order System (Atomic Creation)
*   **Endpoint:** `POST /api/orders`
*   **Validation:** Custom rules ensure holds are valid and not double-spent.
*   **Data Integrity:** Converts a hold to an order within a database transaction, ensuring stock is permanently deducted only once.

### 4. Webhook System (Reliable Payments)
*   **Endpoint:** `POST /api/payments/webhook`
*   **Idempotency:** Custom middleware uses **Redis Locking** and **Caching** to handle duplicate webhook events. Ensures each payment update is processed exactly once, even if the gateway retries.

## 💻 Tech Stack
*   **Framework:** Laravel 12, PHP 8.2+
*   **Database:** MySQL
*   **Cache & Queue:** Redis
*   **Tools:** Xdebug, Laravel Telescope, Larastan
