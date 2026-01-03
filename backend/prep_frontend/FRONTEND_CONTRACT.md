# TEL-U SHOP Frontend Contract (Backend Source of Truth)

## Base URL
- APP_URL from backend `.env` + `/api`
- Default local: `http://localhost/api` (adjust port if needed, e.g. `http://localhost:8000/api`)

## Authentication Flow
- POST `/register` or `/login` returns `access_token` + `token_type` (`Bearer`).
- Store token and send `Authorization: Bearer <token>` for all authenticated requests.
- GET `/me` returns current user with role relation.
- POST `/logout` revokes all tokens for the user.

## Global Headers
- `Accept: application/json`
- `Content-Type: application/json` for JSON bodies.
- `Content-Type: multipart/form-data` for file uploads (avatar, product image).

## Response Envelope (actual backend behavior)
Success responses usually:
```json
{
  "status": "success",
  "message": "optional",
  "data": {}
}
```

Error responses (centralized in `app/Exceptions/Handler.php`):
```json
{
  "status": "error",
  "message": "Validation error.",
  "data": null,
  "errors": {
    "field": ["message"]
  }
}
```

Notes:
- Validation errors from `$request->validate()` return `status=error` and an `errors` map.
- Some success responses include extra top-level keys (for example `stats`, `timeline`, `order_id`) instead of wrapping everything under `data`.

## Common Error Codes
- 401 Unauthorized: missing/invalid token.
- 403 Forbidden: role mismatch, banned user, or merchant not approved.
- 422 Validation error: `errors` object per field.
- 404 Not found: model not found or wrong path.
- 405 Method not allowed.
- 500 Internal server error: message includes exception details when `APP_DEBUG=true`.

## Roles and Access Rules
- roles table: id=1 admin, id=2 merchant, id=3 customer.
- `/register` only accepts role id 2 or 3 (merchant or customer).
- All authenticated routes require:
  - valid Sanctum token
  - not banned (`not-banned` middleware)
- Merchant routes require role=merchant AND `merchant_status=approved`.
- Admin routes require role=admin.

## Ownership Rules (do not bypass)
- Cart item update/remove: cart item must belong to the current user.
- Activities/orders: only the user's own orders are accessible.
- Wishlist/notifications/wallet/loyalty: always scoped to current user.
- Reviews: view/update/delete only the owner's review (policy).
- Merchant order status update: allowed only if order contains merchant's products; updates entire order status.
- Admin user actions: cannot ban self. Activate/deactivate endpoints do not change state (no `is_active` field).

## Pagination and Sorting (QueryService)
- `per_page` default 20, max 100 (activities default 10, admin orders default 15).
- `page` for pagination.
- `sort_by` and `sort_order` (asc|desc), allowed values per endpoint.
- Paginated responses return a Laravel paginator object inside `data`.

## Upload Rules
- Avatar upload: `PUT /profile` with `avatar` file (jpeg/png/jpg, max 2MB).
- Product image upload: `POST /merchant/products` or `PUT /merchant/products/{id}` with `image` file (jpeg/png/jpg, max 2MB).
- Stored on `public` disk; API returns relative paths in `avatar_url` / `image_url`.
- Frontend must prefix with `APP_URL + /storage` (set `VITE_STORAGE_URL`).

## Other Notes
- Checkout `notes` is accepted but not persisted.
- `GET /activities/{id}/track` returns `timeline` at top-level, not inside `data`.
