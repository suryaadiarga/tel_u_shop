import * as React from "react"
import { Link, useParams } from "react-router-dom"
import { Card, CardContent } from "../../components/ui/card"
import { Badge } from "../../components/ui/badge"
import { Button } from "../../components/ui/button"
import { Alert, AlertDescription, AlertTitle } from "../../components/ui/alert"
import { Skeleton } from "../../components/ui/skeleton"
import { apiFetch, ApiError } from "../../lib/api"
import { useToast } from "../../components/ui/toast"

function formatApiError(error: unknown, fallback: string) {
  if (!(error instanceof ApiError)) return fallback
  if (!error.errors) return error.message
  if (typeof error.errors === "string") return `${error.message}: ${error.errors}`
  const details = Object.values(error.errors).flat().join(" ")
  return details ? `${error.message}: ${details}` : error.message
}

export function AdminOrderDetailPage() {
  const { id } = useParams()
  const { push } = useToast()
  const [order, setOrder] = React.useState<any | null>(null)
  const [error, setError] = React.useState<string | null>(null)
  const [loading, setLoading] = React.useState(true)

  const loadOrder = React.useCallback(() => {
    if (!id) return
    setError(null)
    setLoading(true)

    apiFetch(`/admin/orders/${id}`)
      .then((payload) => setOrder((payload as { data?: any }).data ?? null))
      .catch((err) => {
        const message = formatApiError(err, "Failed to load order")
        setError(message)
      })
      .finally(() => setLoading(false))
  }, [id])

  React.useEffect(() => {
    loadOrder()
  }, [loadOrder])

  async function handleStatusUpdate(status: string) {
    if (!id) return
    if (order?.status === status) return
    try {
      await apiFetch(`/admin/orders/${id}/status`, {
        method: "PUT",
        body: { status },
      })
      push({ title: "Order updated", variant: "success" })
      loadOrder()
    } catch (err) {
      const message = formatApiError(err, "Failed to update order")
      push({ title: "Order update error", description: message, variant: "error" })
    }
  }

  if (loading) {
    return (
      <div className="space-y-6">
        <Card>
          <CardContent className="space-y-3">
            <Skeleton className="h-6 w-40" />
            <Skeleton className="h-4 w-1/3" />
          </CardContent>
        </Card>
        <Card>
          <CardContent className="space-y-3">
            <Skeleton className="h-4 w-32" />
            <Skeleton className="h-16 w-full" />
          </CardContent>
        </Card>
      </div>
    )
  }

  if (!order || error) {
    return (
      <div className="space-y-4">
        <Alert>
          <AlertTitle>Order not available</AlertTitle>
          <AlertDescription>{error ?? "Order not found"}</AlertDescription>
        </Alert>
        <Button asChild>
          <Link to="/app/admin/orders">Back to orders</Link>
        </Button>
      </div>
    )
  }

  const allowedStatuses = ["pending", "processing", "completed", "cancelled"]
  const legacyStatus = order.status && !allowedStatuses.includes(order.status) ? order.status : null

  return (
    <div className="space-y-6">
      <Card>
        <CardContent className="space-y-3">
          <div className="flex items-center justify-between">
            <div className="font-display text-xl font-semibold text-ink">Order #{order.id}</div>
            <Badge variant="soft">{order.status}</Badge>
          </div>
          <div className="text-sm text-slate-500">Total: {order.total_amount}</div>
          <div className="text-sm text-slate-500">Customer: {order.user?.name ?? ""}</div>
          <div>
            <select
              className="rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm"
              value={order.status ?? ""}
              onChange={(event) => handleStatusUpdate(event.target.value)}
            >
              {legacyStatus ? (
                <option value={legacyStatus} disabled>
                  {legacyStatus}
                </option>
              ) : null}
              {allowedStatuses.map((status) => (
                <option key={status} value={status}>
                  {status.charAt(0).toUpperCase() + status.slice(1)}
                </option>
              ))}
            </select>
          </div>
        </CardContent>
      </Card>

      <Card>
        <CardContent className="space-y-3">
          <div className="font-display text-lg font-semibold text-ink">Items</div>
          <div className="space-y-2">
            {order.items?.map((item: any) => (
              <div key={item.id} className="rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm">
                <div className="font-semibold text-ink">{item.product?.name ?? ""}</div>
                <div className="text-xs text-slate-500">Qty: {item.qty} | Price: {item.price}</div>
              </div>
            ))}
          </div>
        </CardContent>
      </Card>
    </div>
  )
}
