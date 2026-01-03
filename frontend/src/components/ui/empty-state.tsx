import * as React from "react"
import { cn } from "../../lib/utils"

export function EmptyState({
  title,
  description,
  action,
  className,
}: {
  title: string
  description?: string
  action?: React.ReactNode
  className?: string
}) {
  return (
    <div
      className={cn(
        "flex flex-col items-center gap-3 rounded-xl border border-slate-200 bg-white/70 px-6 py-8 text-center",
        className
      )}
    >
      <div className="text-base font-semibold text-ink">{title}</div>
      {description ? <div className="text-sm text-slate-500">{description}</div> : null}
      {action ? <div className="mt-2">{action}</div> : null}
    </div>
  )
}
