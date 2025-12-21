# Frontend Implementation Checklist

## Auth bootstrap
- [ ] Store access_token from /login or /register.
- [ ] Send Authorization: Bearer <token> on every authenticated request.
- [ ] Call GET /me on app start to hydrate user and role.
- [ ] On 401/403, clear token and redirect to login or a blocked state.
- [ ] Ensure roles table is seeded (role_id 1-3) before first registration.

## Role-based routing
- [ ] Allow merchant routes only if role=merchant and merchant_status=approved.
- [ ] Allow admin routes only if role=admin.
- [ ] Treat banned users as blocked (403 from any authenticated route).

## Error handling
- [ ] Parse the global envelope: success, message, data, errors.
- [ ] For 422, show field errors when errors map exists; otherwise use message.
- [ ] For 404, show not found and stop retries.
- [ ] For 403, show permission/approval messaging (merchant approval or banned).

## Pagination and sorting
- [ ] Send per_page, page, sort_by, sort_order where supported (max 100).
- [ ] Read paginator fields: data, current_page, per_page, total, last_page.
- [ ] Use per_page=10 for /activities if you want the default behavior.

## Must never do
- [ ] Call activity endpoints that are not routed (confirm, cancel, review, reorder, invoice).
- [ ] Assume /admin/users/{id}/activate or /deactivate changes server state.
- [ ] Assume cart item id equals product id for updates/removals.
- [ ] Assume order status is per-merchant; merchant updates affect the whole order.
- [ ] Assume image_url or avatar_url are absolute URLs without prefixing base.
- [ ] Assume checkout notes are persisted (they are accepted but not stored).
