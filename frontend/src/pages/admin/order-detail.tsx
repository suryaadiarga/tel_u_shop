import * as React from "react"
import { Link, useParams } from "react-router-dom"
import { Card, CardContent } from "../../components/ui/card"
import { Badge } from "../../components/ui/badge"
import { Button } from "../../components/ui/button"
import { Alert, AlertDescription, AlertTitle } from "../../components/ui/alert"
import { Skeleton } from "../../components/ui/skeleton"
import { apiFetch, ApiError } from "../../lib/api"
import { useToast } from "../../components/ui/toast"

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
        const message = err instanceof ApiError ? err.message : "Failed to load order"
        setError(message)
      })
      .finally(() => setLoading(false))
  }, [id])

  React.useEffect(() => {
    loadOrder()
  }, [loadOrder])

  async function handleStatusUpdate(status: string) {
    if (!id) return
    try {
      await apiFetch(`/admin/orders/${id}/status`, {
        method: "PUT",
        body: { status },
      })
      push({ title: "Order updated", variant: "success" })
      loadOrder()
    } catch (err) {
      const message = err instanceof ApiError ? err.message : "Failed to update order"
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
              defaultValue={order.status}
              onChange={(event) => handleStatusUpdate(event.target.value)}
            >
              <option value="pending">Pending</option>
              <option value="processing">Processing</option>
              <option value="completed">Completed</option>
              <option value="cancelled">Cancelled</option>
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
