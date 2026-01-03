import * as React from "react"
import { Link } from "react-router-dom"
import { Card, CardContent } from "../../components/ui/card"
import { Badge } from "../../components/ui/badge"
import { Input } from "../../components/ui/input"
import { Button } from "../../components/ui/button"
import { Alert, AlertDescription, AlertTitle } from "../../components/ui/alert"
import { Skeleton } from "../../components/ui/skeleton"
import { EmptyState } from "../../components/ui/empty-state"
import { apiFetch, ApiError } from "../../lib/api"
import { buildQuery } from "../../lib/query"

type Order = {
  id: number
  status: string
  total_amount: number
  payment_method?: string
  created_at?: string
  placed_at?: string
}

type Stats = {
  total_orders: number
  pending: number
  completed: number
  total_spent: number
}

type Paginator<T> = {
  data: T[]
  current_page: number
  last_page: number
  total: number
}

export function CustomerOrdersPage() {
  const [orders, setOrders] = React.useState<Paginator<Order> | null>(null)
  const [stats, setStats] = React.useState<Stats | null>(null)
  const [loading, setLoading] = React.useState(false)
  const [error, setError] = React.useState<string | null>(null)
  const [filters, setFilters] = React.useState({ status: "all", search: "", page: 1 })

  React.useEffect(() => {
    setLoading(true)
    setError(null)

    apiFetch(`/activities${buildQuery({
      status: filters.status,
      search: filters.search,
      page: filters.page,
    })}`)
      .then((payload) => {
        const typed = payload as { data?: Paginator<Order>; stats?: Stats }
        setOrders(typed.data ?? null)
        setStats(typed.stats ?? null)
      })
      .catch((err) => {
        const message = err instanceof ApiError ? err.message : "Failed to load orders"
        setError(message)
      })
      .finally(() => setLoading(false))
  }, [filters])

  const hasOrders = (orders?.data?.length ?? 0) > 0

  return (
    <div className="space-y-6">
      <div className="grid gap-4 md:grid-cols-4">
        {[
          { label: "Total orders", value: stats?.total_orders ?? 0 },
          { label: "Pending", value: stats?.pending ?? 0 },
          { label: "Completed", value: stats?.completed ?? 0 },
          { label: "Total spent", value: stats?.total_spent ?? 0 },
        ].map((stat) => (
          <Card key={stat.label}>
            <CardContent>
              <div className="text-xs uppercase tracking-[0.2em] text-slate-400">{stat.label}</div>
              <div className="mt-2 text-xl font-semibold text-ink">{stat.value}</div>
            </CardContent>
          </Card>
        ))}
      </div>

      <div className="flex flex-wrap gap-4">
        <Input
          placeholder="Search order ID or product name"
          value={filters.search}
          onChange={(event) => setFilters((prev) => ({ ...prev, search: event.target.value, page: 1 }))}
        />
        <select
          className="rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm"
          value={filters.status}
          onChange={(event) => setFilters((prev) => ({ ...prev, status: event.target.value, page: 1 }))}
        >
          <option value="all">All status</option>
          <option value="pending">Pending</option>
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
            <Card key={`order-skeleton-${index}`}>
              <CardContent className="space-y-3">
                <Skeleton className="h-4 w-1/3" />
                <Skeleton className="h-3 w-1/2" />
              </CardContent>
            </Card>
          ))}
        </div>
      ) : null}

      {!loading && !hasOrders ? (
        <EmptyState
          title="No orders yet"
          description="Your order history will appear here once you place an order."
          action={
            <Button asChild>
              <Link to="/app/catalog">Browse catalog</Link>
            </Button>
          }
        />
      ) : null}

      {!loading && hasOrders ? (
        <div className="space-y-4">
          {orders?.data.map((order) => (
            <Card key={order.id}>
              <CardContent className="flex flex-wrap items-center justify-between gap-4">
                <div>
                  <div className="font-semibold text-ink">Order #{order.id}</div>
                  <div className="text-xs text-slate-500">{order.placed_at ?? order.created_at ?? ""}</div>
                </div>
                <div className="flex items-center gap-3">
                  <Badge variant="soft">{order.status}</Badge>
                  <div className="text-sm font-semibold text-ink">{order.total_amount}</div>
                  <Button variant="outline" asChild>
                    <Link to={`/app/orders/${order.id}`}>View detail</Link>
                  </Button>
                </div>
              </CardContent>
            </Card>
          ))}
        </div>
      ) : null}

      {orders && hasOrders ? (
        <div className="flex items-center justify-between text-sm text-slate-500">
          <div>
            Page {orders.current_page} of {orders.last_page} ({orders.total} orders)
          </div>
          <div className="flex gap-2">
            <Button
              variant="outline"
              disabled={orders.current_page <= 1}
              onClick={() => setFilters((prev) => ({ ...prev, page: prev.page - 1 }))}
            >
              Previous
            </Button>
            <Button
              variant="outline"
              disabled={orders.current_page >= orders.last_page}
              onClick={() => setFilters((prev) => ({ ...prev, page: prev.page + 1 }))}
            >
              Next
            </Button>
          </div>
        </div>
      ) : null}
    </div>
  )
}
