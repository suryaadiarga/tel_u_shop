# Cart Controller, Model, and Routes Fix

## Current Issue
- Cart update route `PUT /cart/update/{item}` returns 404 error despite being registered
- Route is defined with `auth:sanctum` middleware
- Controller method `updateQty` exists with proper validation

## Plan
1. Temporarily move cart update route outside auth:sanctum middleware to test
2. If successful, investigate and fix Sanctum middleware issue for PUT requests
3. Ensure proper authentication while fixing route accessibility

## Files to Edit
- routes/api.php (temporary route move)
- config/sanctum.php (if middleware config needs adjustment)

## Progress
- [ ] Move cart update route outside auth:sanctum middleware
- [ ] Test if route works without middleware
- [ ] Fix Sanctum middleware configuration if needed
- [ ] Restore proper authentication
- [ ] Verify all cart routes work correctly
