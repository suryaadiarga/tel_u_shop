import * as React from "react"
import { cn } from "../../lib/utils"

export type ToastVariant = "default" | "success" | "error"

export type ToastOptions = {
  title: string
  description?: string
  variant?: ToastVariant
}

type ToastItem = ToastOptions & { id: string }

type ToastContextValue = {
  push: (toast: ToastOptions) => void
}

const ToastContext = React.createContext<ToastContextValue | undefined>(undefined)

const variantClasses: Record<ToastVariant, string> = {
  default: "border-slate-200 bg-white text-slate-900",
  success: "border-emerald-200 bg-emerald-50 text-emerald-900",
  error: "border-rose-200 bg-rose-50 text-rose-900",
}

export function ToastProvider({ children }: { children: React.ReactNode }) {
  const [toasts, setToasts] = React.useState<ToastItem[]>([])

  const push = React.useCallback((toast: ToastOptions) => {
    const id = crypto.randomUUID()
    setToasts((prev) => [...prev, { ...toast, id }])

    window.setTimeout(() => {
      setToasts((prev) => prev.filter((item) => item.id !== id))
    }, 4000)
  }, [])

  return (
    <ToastContext.Provider value={{ push }}>
      {children}
      <div className="fixed bottom-6 right-6 z-50 flex w-[320px] flex-col gap-3">
        {toasts.map((toast) => (
          <div
            key={toast.id}
            className={cn(
              "rounded-xl border px-4 py-3 text-sm shadow-lg animate-slide-in-right",
              variantClasses[toast.variant ?? "default"]
            )}
          >
            <div className="font-semibold">{toast.title}</div>
            {toast.description ? <div className="mt-1 text-xs opacity-80">{toast.description}</div> : null}
          </div>
        ))}
      </div>
    </ToastContext.Provider>
  )
}

export function useToast() {
  const context = React.useContext(ToastContext)
  if (!context) {
    throw new Error("useToast must be used within ToastProvider")
  }
  return context
}
