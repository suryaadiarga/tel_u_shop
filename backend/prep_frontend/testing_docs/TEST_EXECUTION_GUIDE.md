# Test Execution Guide

## Run all tests
- `php artisan test`

## Run per student slice
- Auth: `php artisan test --filter=Auth`
- CustomerProducts: `php artisan test --filter=CustomerProducts`
- Wallet: `php artisan test --filter=Wallet`
- MerchantProducts: `php artisan test --filter=MerchantProducts`
- AdminUsers: `php artisan test --filter=AdminUsers`

## Optional: show a red (failing) test then fix
1) Temporarily change an assertion (for example, expect 201 instead of 200) in one test method.
2) Rerun with the slice filter to show the failure.
3) Revert the change and rerun to show green output.

## Troubleshooting
- If migrations fail, delete old test database caches and rerun: `php artisan test` (uses SQLite in-memory).
- Ensure SQLite extension is enabled in PHP.
- If auth failures occur, verify Sanctum is installed and the test uses `authAs()` before API calls.
