import * as React from "react"
import { Outlet, useLocation } from "react-router-dom"
import { Sidebar } from "./Sidebar"
import { Topbar } from "./Topbar"
import { Dialog, DialogContent } from "../ui/dialog"
import { useAuth, useRole } from "../../store/auth"
import { navSections } from "../../lib/navigation"

function usePageMeta(pathname: string) {
  const items = navSections.flatMap((section) => section.items)
  const matches = items
    .filter((item) => pathname === item.to || pathname.startsWith(`${item.to}/`))
    .sort((a, b) => b.to.length - a.to.length)

  const match = matches[0]
  return {
    title: match?.label ?? "Overview",
    subtitle: match?.description ?? "Your workspace overview",
  }
}

export function AppLayout() {
  const { user, logout } = useAuth()
  const role = useRole()
  const [mobileOpen, setMobileOpen] = React.useState(false)
  const location = useLocation()
  const { title, subtitle } = usePageMeta(location.pathname)

  return (
    <div className="flex min-h-screen">
      <Sidebar role={role} className="hidden lg:flex" />

      <Dialog open={mobileOpen} onOpenChange={setMobileOpen}>
        <DialogContent className="h-[90vh] w-full max-w-xs overflow-y-auto p-0">
          <Sidebar role={role} onNavigate={() => setMobileOpen(false)} className="flex w-full" />
        </DialogContent>
      </Dialog>

      <div className="flex min-h-screen flex-1 flex-col">
        <Topbar
          title={title}
          subtitle={subtitle}
          roleLabel={role ? `${role}${user?.merchant_status ? ` - ${user.merchant_status}` : ""}` : null}
          onLogout={logout}
          onMenu={() => setMobileOpen(true)}
        />
        <main className="flex-1 space-y-6 px-6 py-8">
          <Outlet />
        </main>
      </div>
    </div>
  )
}
