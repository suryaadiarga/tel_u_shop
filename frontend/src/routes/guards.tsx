import { Navigate, Outlet } from "react-router-dom"
import { useAuth, useRole } from "../store/auth"
import type { RoleName } from "../store/auth"
import { BlockedPage } from "../pages/blocked"
import { MerchantPendingPage } from "../pages/merchant-pending"
import { ForbiddenPage } from "../pages/forbidden"
import { Skeleton } from "../components/ui/skeleton"

export function RequireAuth() {
  const { user, token, loading } = useAuth()

  if (loading) {
    return (
      <div className="mx-auto mt-12 max-w-5xl space-y-4 px-6">
        <Skeleton className="h-10 w-1/3" />
        <Skeleton className="h-40" />
      </div>
    )
  }

  if (!token) {
    return <Navigate to="/login" replace />
  }

  if (user?.is_banned) {
    return <BlockedPage />
  }

  return <Outlet />
}

export function RequireRole({ roles }: { roles: RoleName[] }) {
  const { user } = useAuth()
  const role = useRole()

  if (!role || !roles.includes(role)) {
    return <ForbiddenPage />
  }

  if (role === "merchant" && user?.merchant_status !== "approved") {
    return <MerchantPendingPage />
  }

  return <Outlet />
}
