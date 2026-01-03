import * as React from "react"
import { Link, useParams } from "react-router-dom"
import { Card, CardContent } from "../../components/ui/card"
import { Badge } from "../../components/ui/badge"
import { Button } from "../../components/ui/button"
import { Alert, AlertDescription, AlertTitle } from "../../components/ui/alert"
import { Skeleton } from "../../components/ui/skeleton"
import { apiFetch, ApiError } from "../../lib/api"

export function CustomerOrderDetailPage() {
  const { id } = useParams()
  const [order, setOrder] = React.useState<any | null>(null)
  const [timeline, setTimeline] = React.useState<any[] | null>(null)
  const [loading, setLoading] = React.useState(true)
  const [error, setError] = React.useState<string | null>(null)

  React.useEffect(() => {
    if (!id) return
    setLoading(true)
    setError(null)

    Promise.all([
      apiFetch(`/activities/${id}`),
      apiFetch(`/activities/${id}/track`),
    ])
      .then(([orderPayload, trackPayload]) => {
        setOrder((orderPayload as { data?: any }).data ?? null)
        setTimeline((trackPayload as { timeline?: any[] }).timeline ?? null)
      })
      .catch((err) => {
        const message = err instanceof ApiError ? err.message : "Failed to load order"
        setError(message)
      })
      .finally(() => setLoading(false))
  }, [id])

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
          <Link to="/app/orders">Back to orders</Link>
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
          <div className="text-sm text-slate-500">Payment: {order.payment_method ?? "-"}</div>
          <div className="text-sm text-slate-500">Total: {order.total_amount}</div>
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

      <Card>
        <CardContent className="space-y-3">
          <div className="font-display text-lg font-semibold text-ink">Tracking timeline</div>
          <div className="space-y-2">
            {timeline?.length ? (
              timeline.map((step, index) => (
                <div key={`${step.status}-${index}`} className="rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm">
                  <div className="font-semibold text-ink">{step.label}</div>
                  <div className="text-xs text-slate-500">Status: {step.status}</div>
                </div>
              ))
            ) : (
              <div className="text-sm text-slate-500">No tracking data.</div>
            )}
          </div>
        </CardContent>
      </Card>
    </div>
  )
}
