import * as React from "react"
import { Card, CardContent } from "../../components/ui/card"
import { Badge } from "../../components/ui/badge"
import { Input } from "../../components/ui/input"
import { Alert, AlertDescription, AlertTitle } from "../../components/ui/alert"
import { Skeleton } from "../../components/ui/skeleton"
import { EmptyState } from "../../components/ui/empty-state"
import { TablePagination } from "../../components/ui/table"
import { apiFetch, ApiError } from "../../lib/api"
import { buildQuery } from "../../lib/query"
import { useToast } from "../../components/ui/toast"

type Order = {
  id: number
  status: string
  total_amount: number
  created_at?: string
  user?: { id: number; name: string; email: string }
  items?: Array<{ id: number; qty: number; product?: { name: string } }>
}

type Paginator<T> = {
  data: T[]
  current_page: number
  last_page: number
  total?: number
}

export function MerchantOrdersPage() {
  const { push } = useToast()
  const [orders, setOrders] = React.useState<Paginator<Order> | null>(null)
  const [filters, setFilters] = React.useState({ status: "", search: "", page: 1 })
  const [loading, setLoading] = React.useState(false)
  const [error, setError] = React.useState<string | null>(null)

  const loadOrders = React.useCallback(() => {
    setLoading(true)
    setError(null)

    apiFetch(`/merchant/orders${buildQuery(filters)}`)
      .then((payload) => setOrders((payload as { data?: Paginator<Order> }).data ?? null))
      .catch((err) => {
        const message = err instanceof ApiError ? err.message : "Failed to load orders"
        setError(message)
      })
      .finally(() => setLoading(false))
  }, [filters])

  React.useEffect(() => {
    loadOrders()
  }, [loadOrders])

  async function handleStatusUpdate(orderId: number, status: string) {
    try {
      await apiFetch(`/merchant/orders/${orderId}/status`, {
        method: "PUT",
        body: { status },
      })
      push({ title: "Order updated", variant: "success" })
      loadOrders()
    } catch (err) {
      const message = err instanceof ApiError ? err.message : "Failed to update order"
      push({ title: "Order update error", description: message, variant: "error" })
    }
  }

  const hasOrders = (orders?.data?.length ?? 0) > 0

  return (
    <div className="space-y-6">
      <div className="flex flex-wrap gap-3">
        <Input
          placeholder="Search order ID"
          value={filters.search}
          onChange={(event) => setFilters((prev) => ({ ...prev, search: event.target.value, page: 1 }))}
        />
        <select
          className="rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm"
          value={filters.status}
          onChange={(event) => setFilters((prev) => ({ ...prev, status: event.target.value, page: 1 }))}
        >
          <option value="">All status</option>
          <option value="pending">Pending</option>
          <option value="processing">Processing</option>
          <option value="paid">Paid</option>
          <option value="shipped">Shipped</option>
          <option value="completed">Completed</option>
          <option value="cancelled">Cancelled</option>
        </select>
      </div>

      {error ? (
        <Alert>
          <AlertTitle>Orders error</AlertTitle>
          <AlertDescription>{error}</AlertDescription>
        </Alert>
      ) : null}

      {loading ? (
        <div className="space-y-3">
          {Array.from({ length: 3 }).map((_, index) => (
            <Card key={`merchant-order-skeleton-${index}`}>
              <CardContent className="space-y-3">
                <Skeleton className="h-4 w-1/3" />
                <Skeleton className="h-3 w-1/2" />
              </CardContent>
            </Card>
          ))}
        </div>
      ) : null}

      {!loading && !hasOrders ? (
        <EmptyState title="No orders yet" description="Orders for your products will appear here." />
      ) : null}

      {!loading && hasOrders ? (
        <div className="space-y-4">
          {orders?.data.map((order) => (
            <Card key={order.id}>
              <CardContent className="space-y-3">
                <div className="flex flex-wrap items-center justify-between gap-3">
                  <div>
                    <div className="font-semibold text-ink">Order #{order.id}</div>
                    <div className="text-xs text-slate-500">{order.user?.name ?? ""}</div>
                  </div>
                  <Badge variant="soft">{order.status}</Badge>
                </div>
                <div className="text-sm text-slate-500">Total: {order.total_amount}</div>
                <div className="space-y-1 text-xs text-slate-500">
                  {order.items?.map((item) => (
                    <div key={item.id}>{item.product?.name} | Qty {item.qty}</div>
                  ))}
                </div>
                <div className="flex flex-wrap items-center gap-2">
                  <select
                    className="rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm"
                    defaultValue={order.status}
                    onChange={(event) => handleStatusUpdate(order.id, event.target.value)}
                  >
                    <option value="pending">Pending</option>
                    <option value="processing">Processing</option>
                    <option value="paid">Paid</option>
                    <option value="shipped">Shipped</option>
                    <option value="completed">Completed</option>
                    <option value="cancelled">Cancelled</option>
                  </select>
                </div>
              </CardContent>
            </Card>
          ))}
        </div>
      ) : null}

      {orders && hasOrders ? (
        <TablePagination
          page={orders.current_page}
          totalPages={orders.last_page}
          total={orders.total}
          onPrev={() => setFilters((prev) => ({ ...prev, page: prev.page - 1 }))}
          onNext={() => setFilters((prev) => ({ ...prev, page: prev.page + 1 }))}
        />
      ) : null}
    </div>
  )
}
