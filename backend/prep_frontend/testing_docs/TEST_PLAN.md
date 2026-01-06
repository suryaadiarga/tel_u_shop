# Test Plan — Student Demo Test Pack (Backend)

## Objective
Demonstrate backend test execution output (unit + feature) in terminal without UI usage.

## Scope
In scope (feature slices in this test pack):
- Auth (login)
- Customer Products (index/show)
- Wallet (topup)
- Merchant Products (create)
- Admin Users (index)

Out of scope:
- Frontend UI and end-to-end UI flows
- Non-selected API endpoints (cart, checkout, wishlist, reviews, loyalty, notifications, merchant analytics, admin orders)
- Performance, load, and security testing

## Environment & Tools
- Laravel backend (WORKDIR: D:\DATAKU\App\TubesImpal\backend)
- `php artisan test`
- SQLite in-memory (configured in `phpunit.xml`)
- Sanctum for API authentication in tests

## Pass/Fail Criteria
- PASS: All tests in selected slices complete with green output (no failures).
- FAIL: Any test failure, error, or unexpected non-deterministic output.
