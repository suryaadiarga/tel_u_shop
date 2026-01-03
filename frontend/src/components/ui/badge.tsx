import * as React from "react"
import { cn } from "../../lib/utils"

type BadgeVariant = "default" | "soft" | "outline"

const variantClasses: Record<BadgeVariant, string> = {
  default: "bg-ink text-white",
  soft: "bg-glacier/10 text-glacier",
  outline: "border border-slate-200 text-slate-600",
}

export function Badge({ className, variant = "default", ...props }: React.HTMLAttributes<HTMLSpanElement> & { variant?: BadgeVariant }) {
  return (
    <span
      className={cn(
        "inline-flex items-center gap-1 rounded-full px-3 py-1 text-xs font-semibold",
        variantClasses[variant],
        className
      )}
      {...props}
    />
  )
}
