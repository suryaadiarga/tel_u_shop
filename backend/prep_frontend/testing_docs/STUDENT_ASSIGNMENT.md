# Student Assignment

Recommended distribution (5 students, 1 slice each). Each student runs exactly one filter command.

Student 1 — Auth
- Unit: `tests/Unit/AuthUnitTest.php`
  - `testIsBannedPositive`
  - `testIsBannedNegative`
- Feature: `tests/Feature/Api/AuthFeatureTest.php`
  - `testLoginPositive`
  - `testLoginNegative`
- Command: `php artisan test --filter=Auth`

Student 2 — CustomerProducts
- Unit: `tests/Unit/CustomerProductsUnitTest.php`
  - `testStockStatusPositive`
  - `testStockStatusNegative`
- Feature: `tests/Feature/Api/CustomerProductsFeatureTest.php`
  - `testProductIndexPositive`
  - `testProductShowNegative`
- Command: `php artisan test --filter=CustomerProducts`

Student 3 — Wallet
- Unit: `tests/Unit/WalletUnitTest.php`
  - `testPerPagePositive`
  - `testPerPageNegative`
- Feature: `tests/Feature/Api/WalletFeatureTest.php`
  - `testTopupPositive`
  - `testTopupNegative`
- Command: `php artisan test --filter=Wallet`

Student 4 — MerchantProducts
- Unit: `tests/Unit/MerchantProductsUnitTest.php`
  - `testMerchantApprovalPositive`
  - `testMerchantApprovalNegative`
- Feature: `tests/Feature/Api/MerchantProductsFeatureTest.php`
  - `testCreateProductPositive`
  - `testCreateProductNegative`
- Command: `php artisan test --filter=MerchantProducts`

Student 5 — AdminUsers
- Unit: `tests/Unit/AdminUsersUnitTest.php`
  - `testHasRolePositive`
  - `testHasRoleNegative`
- Feature: `tests/Feature/Api/AdminUsersFeatureTest.php`
  - `testAdminIndexPositive`
  - `testAdminIndexNegative`
- Command: `php artisan test --filter=AdminUsers`

If there are fewer than 5 students, combine slices and reuse the commands above.
