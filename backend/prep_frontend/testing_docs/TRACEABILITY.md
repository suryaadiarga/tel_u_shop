# Traceability

## Feature/Endpoint Coverage
- POST `/api/login` -> `tests/Feature/Api/AuthFeatureTest.php` -> `testLoginPositive`, `testLoginNegative`
- GET `/api/products` -> `tests/Feature/Api/CustomerProductsFeatureTest.php` -> `testProductIndexPositive`
- GET `/api/products/{id}` -> `tests/Feature/Api/CustomerProductsFeatureTest.php` -> `testProductShowNegative`
- POST `/api/wallet/topup` -> `tests/Feature/Api/WalletFeatureTest.php` -> `testTopupPositive`, `testTopupNegative`
- POST `/api/merchant/products` -> `tests/Feature/Api/MerchantProductsFeatureTest.php` -> `testCreateProductPositive`, `testCreateProductNegative`
- GET `/api/admin/users` -> `tests/Feature/Api/AdminUsersFeatureTest.php` -> `testAdminIndexPositive`, `testAdminIndexNegative`

## Unit Helper Coverage
- `App\Models\User::isBanned()` -> `tests/Unit/AuthUnitTest.php` -> `testIsBannedPositive`, `testIsBannedNegative`
- `App\Models\Product::getStockStatusAttribute()` -> `tests/Unit/CustomerProductsUnitTest.php` -> `testStockStatusPositive`, `testStockStatusNegative`
- `App\Services\QueryService::perPage()` -> `tests/Unit/WalletUnitTest.php` -> `testPerPagePositive`, `testPerPageNegative`
- `App\Models\User::isMerchantApproved()` -> `tests/Unit/MerchantProductsUnitTest.php` -> `testMerchantApprovalPositive`, `testMerchantApprovalNegative`
- `App\Models\User::hasRole()` -> `tests/Unit/AdminUsersUnitTest.php` -> `testHasRolePositive`, `testHasRoleNegative`
