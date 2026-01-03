# Frontend Implementation Checklist

## Contract usage
- [ ] Use only endpoints listed in `FRONTEND_API_MAP.json`.
- [ ] Do not invent fields or payloads.
- [ ] Do not modify backend code or routes.

## Auth bootstrap
- [ ] Save `access_token` after `/login` or `/register`.
- [ ] Send `Authorization: Bearer <token>` on all authenticated requests.
- [ ] On app load, call `GET /me` to hydrate user + role.
- [ ] On 401, clear token and redirect to login.
- [ ] On 403, show blocked state (banned user or merchant not approved).

## Role-based routing
- [ ] Customer routes require role=customer.
- [ ] Merchant routes require role=merchant and `merchant_status=approved`.
- [ ] Admin routes require role=admin.

## Error handling
- [ ] Parse `status`, `message`, `data`, `errors` on every response.
- [ ] For 422, show field errors from `errors` map.
- [ ] Some success responses include top-level `stats` or `timeline`; do not assume `data` always exists.
- [ ] Treat 404 as not found and stop retries.

## Pagination and sorting
- [ ] Send `per_page`, `page`, `sort_by`, `sort_order` where supported (max 100).
- [ ] Activities default `per_page=10`, admin orders default `per_page=15`.
- [ ] Read paginator fields inside `data`.

## Uploads
- [ ] Use `multipart/form-data` for avatar and product image uploads.
- [ ] Build image URLs using `VITE_STORAGE_URL` + `/<path>`.

## Manual test steps per role
- [ ] Public: register, login, invalid credentials.
- [ ] Customer: cart CRUD, checkout (wallet/cash), products list/detail/categories, wishlist add/remove, reviews CRUD, wallet balance/topup/transactions, loyalty balance/history/redeem/rewards, notifications list/mark/read/delete.
- [ ] Merchant: products CRUD with image, orders list/update status, analytics endpoints.
- [ ] Admin: orders list/detail/update status; users list/detail/role update/approve merchant/ban/unban.

## Guardrails
- [ ] Do not call non-routed Activity methods (confirm/cancel/reorder/review/invoice).
- [ ] Do not assume image_url/avatar_url are absolute URLs.
- [ ] Do not assume `/admin/users/{id}/activate` or `/admin/users/{id}/deactivate` changes state.
- [ ] Do not assume order status is per-merchant; updates affect entire order.
