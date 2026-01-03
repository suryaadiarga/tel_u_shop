import { LogOut, Menu } from "lucide-react"
import { Button } from "../ui/button"
import { Badge } from "../ui/badge"

export function Topbar({
  title,
  subtitle,
  roleLabel,
  onLogout,
  onMenu,
}: {
  title: string
  subtitle?: string
  roleLabel?: string | null
  onLogout: () => void
  onMenu: () => void
}) {
  return (
    <header className="flex flex-wrap items-center justify-between gap-4 border-b border-slate-100 bg-white/80 px-6 py-4 backdrop-blur">
      <div className="flex items-center gap-4">
        <button
          className="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-600 shadow-sm lg:hidden"
          onClick={onMenu}
        >
          <Menu className="h-4 w-4" />
        </button>
        <div>
          <div className="font-display text-xl font-semibold text-ink">{title}</div>
          {subtitle ? <div className="text-sm text-slate-500">{subtitle}</div> : null}
        </div>
      </div>
      <div className="flex items-center gap-3">
        {roleLabel ? <Badge variant="soft">{roleLabel}</Badge> : null}
        <Button variant="ghost" onClick={onLogout}>
          <LogOut className="h-4 w-4" />
          Logout
        </Button>
      </div>
    </header>
  )
}
