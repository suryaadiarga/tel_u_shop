import { Routes, Route } from "react-router-dom"
import { LandingPage } from "./pages/landing"
import { LoginPage } from "./pages/login"
import { RegisterPage } from "./pages/register"
import { NotFoundPage } from "./pages/not-found"
import { AppLayout } from "./components/layout/AppLayout"
import { DashboardPage } from "./pages/dashboard"
import { ProfilePage } from "./pages/profile"
import { RequireAuth, RequireRole } from "./routes/guards"
import { CustomerCatalogPage } from "./pages/customer/catalog"
import { CustomerProductDetailPage } from "./pages/customer/product-detail"
import { CustomerCartPage } from "./pages/customer/cart"
import { CustomerOrdersPage } from "./pages/customer/orders"
import { CustomerOrderDetailPage } from "./pages/customer/order-detail"
import { CustomerWalletPage } from "./pages/customer/wallet"
import { CustomerWishlistPage } from "./pages/customer/wishlist"
import { CustomerLoyaltyPage } from "./pages/customer/loyalty"
import { CustomerNotificationsPage } from "./pages/customer/notifications"
import { MerchantProductsPage } from "./pages/merchant/products"
import { MerchantOrdersPage } from "./pages/merchant/orders"
import { MerchantAnalyticsPage } from "./pages/merchant/analytics"
import { AdminOrdersPage } from "./pages/admin/orders"
import { AdminOrderDetailPage } from "./pages/admin/order-detail"
import { AdminUsersPage } from "./pages/admin/users"
import { AdminUserDetailPage } from "./pages/admin/user-detail"

export function App() {
  return (
    <Routes>
      <Route path="/" element={<LandingPage />} />
      <Route path="/login" element={<LoginPage />} />
      <Route path="/register" element={<RegisterPage />} />

      <Route element={<RequireAuth />}>
        <Route path="/app" element={<AppLayout />}>
          <Route index element={<DashboardPage />} />
          <Route path="profile" element={<ProfilePage />} />

          <Route element={<RequireRole roles={["customer"]} />}>
            <Route path="catalog" element={<CustomerCatalogPage />} />
            <Route path="catalog/:id" element={<CustomerProductDetailPage />} />
            <Route path="cart" element={<CustomerCartPage />} />
            <Route path="orders" element={<CustomerOrdersPage />} />
            <Route path="orders/:id" element={<CustomerOrderDetailPage />} />
            <Route path="wallet" element={<CustomerWalletPage />} />
            <Route path="wishlist" element={<CustomerWishlistPage />} />
            <Route path="loyalty" element={<CustomerLoyaltyPage />} />
            <Route path="notifications" element={<CustomerNotificationsPage />} />
          </Route>

          <Route element={<RequireRole roles={["merchant"]} />}>
            <Route path="merchant/products" element={<MerchantProductsPage />} />
            <Route path="merchant/orders" element={<MerchantOrdersPage />} />
            <Route path="merchant/analytics" element={<MerchantAnalyticsPage />} />
          </Route>

          <Route element={<RequireRole roles={["admin"]} />}>
            <Route path="admin/orders" element={<AdminOrdersPage />} />
            <Route path="admin/orders/:id" element={<AdminOrderDetailPage />} />
            <Route path="admin/users" element={<AdminUsersPage />} />
            <Route path="admin/users/:id" element={<AdminUserDetailPage />} />
          </Route>
        </Route>
      </Route>

      <Route path="*" element={<NotFoundPage />} />
    </Routes>
  )
}
