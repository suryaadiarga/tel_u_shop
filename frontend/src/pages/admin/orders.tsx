import * as React from "react"
import { Link } from "react-router-dom"
import { Card, CardContent } from "../../components/ui/card"
import { Badge } from "../../components/ui/badge"
import { Input } from "../../components/ui/input"
import { Button } from "../../components/ui/button"
import { Alert, AlertDescription, AlertTitle } from "../../components/ui/alert"
import { Skeleton } from "../../components/ui/skeleton"
import { EmptyState } from "../../components/ui/empty-state"
import { TablePagination } from "../../components/ui/table"
import { apiFetch, ApiError } from "../../lib/api"
import { buildQuery } from "../../lib/query"

type Order = {
  id: number
  status: string
  total_amount: number
  created_at?: string
  user?: { id: number; name: string; email: string }
}

type Paginator<T> = {
  data: T[]
  current_page: number
  last_page: number
  total: number
}

export function AdminOrdersPage() {
  const [orders, setOrders] = React.useState<Paginator<Order> | null>(null)
  const [filters, setFilters] = React.useState({ status: "", search: "", page: 1 })
  const [error, setError] = React.useState<string | null>(null)
  const [loading, setLoading] = React.useState(false)

  React.useEffect(() => {
    setError(null)
    setLoading(true)

    apiFetch(`/admin/orders${buildQuery(filters)}`)
      .then((payload) => setOrders((payload as { data?: Paginator<Order> }).data ?? null))
      .catch((err) => {
        const message = err instanceof ApiError ? err.message : "Failed to load orders"
        setError(message)
      })
      .finally(() => setLoading(false))
  }, [filters])

  const hasOrders = (orders?.data?.length ?? 0) > 0

  return (
    <div className="space-y-6">
      <div className="flex flex-wrap gap-3">
        <Input
          placeholder="Search order or user"
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
            <Card key={`admin-order-skeleton-${index}`}>
              <CardContent className="space-y-3">
                <Skeleton className="h-4 w-1/3" />
                <Skeleton className="h-3 w-1/2" />
              </CardContent>
            </Card>
          ))}
        </div>
      ) : null}

      {!loading && !hasOrders ? (
        <EmptyState title="No orders found" description="Adjust filters or wait for new orders." />
      ) : null}

      {!loading && hasOrders ? (
        <div className="space-y-4">
          {orders?.data.map((order) => (
            <Card key={order.id}>
              <CardContent className="flex flex-wrap items-center justify-between gap-4">
                <div>
                  <div className="font-semibold text-ink">Order #{order.id}</div>
                  <div className="text-xs text-slate-500">{order.user?.name ?? ""}</div>
                </div>
                <div className="flex items-center gap-3">
                  <Badge variant="soft">{order.status}</Badge>
                  <div className="text-sm font-semibold text-ink">{order.total_amount}</div>
                  <Button variant="outline" asChild>
                    <Link to={`/app/admin/orders/${order.id}`}>View detail</Link>
                  </Button>
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
