import { NavLink } from "react-router-dom"
import { navSections } from "../../lib/navigation"
import { cn } from "../../lib/utils"

type Role = "customer" | "merchant" | "admin" | null

export function Sidebar({
  role,
  onNavigate,
  className,
}: {
  role: Role
  onNavigate?: () => void
  className?: string
}) {
  const sections = role ? navSections.filter((section) => section.roles.includes(role)) : []

  return (
    <aside
      className={cn(
        "flex h-full w-72 flex-col gap-6 border-r border-slate-100 bg-white/80 px-6 py-8 backdrop-blur",
        className
      )}
    >
      <div className="space-y-2">
        <div className="font-display text-xl font-semibold text-ink">TEL-U Shop</div>
        <p className="text-xs text-slate-500">Premium operations dashboard</p>
      </div>

      <div className="space-y-6 overflow-y-auto pb-4">
        {sections.map((section) => (
          <div key={section.title} className="space-y-3">
            <div className="nav-group-title">{section.title}</div>
            <div className="space-y-2">
              {section.items.map((item) => (
                <NavLink
                  key={item.label}
                  to={item.to}
                  className={({ isActive }) =>
                    cn("tile flex items-start gap-3", isActive && "border-slate-300 bg-slate-50 text-ink")
                  }
                  onClick={onNavigate}
                >
                  <item.icon className="mt-0.5 h-4 w-4 text-slate-400" />
                  <div>
                    <div className="text-sm font-semibold text-ink">{item.label}</div>
                    <div className="text-xs text-slate-400">{item.description}</div>
                  </div>
                </NavLink>
              ))}
            </div>
          </div>
        ))}
      </div>
    </aside>
  )
}
