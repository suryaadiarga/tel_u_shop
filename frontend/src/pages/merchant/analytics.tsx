import * as React from "react"
import { Card, CardContent } from "../../components/ui/card"
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
  TableToolbar,
} from "../../components/ui/table"
import { Alert, AlertDescription, AlertTitle } from "../../components/ui/alert"
import { Skeleton } from "../../components/ui/skeleton"
import { EmptyState } from "../../components/ui/empty-state"
import { apiFetch, ApiError } from "../../lib/api"

type DashboardData = {
  overview?: {
    total_products: number
    total_sales: number
    total_revenue: number
    average_rating: number
  }
  top_products?: Array<{ id: number; name: string; total_sold?: number }>
  sales_trend?: Array<{ date: string; total_quantity: number; total_revenue: number }>
  period_days?: string | number
}

function mapAnalyticsError(error: unknown) {
  if (error instanceof ApiError) {
    if (error.statusCode === 403) return "Access restricted"
    if (error.statusCode >= 500) return "Server error"
    return error.message
  }
  return "Network error"
}

export function MerchantAnalyticsPage() {
  const [dashboard, setDashboard] = React.useState<DashboardData | null>(null)
  const [sales, setSales] = React.useState<any | null>(null)
  const [products, setProducts] = React.useState<any[] | null>(null)
  const [customers, setCustomers] = React.useState<any | null>(null)
  const [error, setError] = React.useState<string | null>(null)
  const [loading, setLoading] = React.useState(true)

  React.useEffect(() => {
    setError(null)
    setLoading(true)

    Promise.all([
      apiFetch("/merchant/analytics/dashboard"),
      apiFetch("/merchant/analytics/sales"),
      apiFetch("/merchant/analytics/products"),
      apiFetch("/merchant/analytics/customers"),
      ])
      .then(([dashboardPayload, salesPayload, productsPayload, customersPayload]) => {
        setDashboard((dashboardPayload as { data?: DashboardData }).data ?? null)
        setSales((salesPayload as { data?: any }).data ?? null)
        setProducts((productsPayload as { data?: any[] }).data ?? null)
        setCustomers((customersPayload as { data?: any }).data ?? null)
      })
      .catch((err) => {
        const message = mapAnalyticsError(err)
        setError(message)
      })
      .finally(() => setLoading(false))
  }, [])

  if (loading) {
    return (
      <div className="space-y-6">
        <div className="grid gap-4 md:grid-cols-4">
          {Array.from({ length: 4 }).map((_, index) => (
            <Card key={`analytics-skeleton-${index}`}>
              <CardContent className="space-y-3">
                <Skeleton className="h-3 w-20" />
                <Skeleton className="h-6 w-16" />
              </CardContent>
            </Card>
          ))}
        </div>
        <Card>
          <CardContent className="space-y-3">
            <Skeleton className="h-5 w-32" />
            <Skeleton className="h-16 w-full" />
          </CardContent>
        </Card>
      </div>
    )
  }

  return (
    <div className="space-y-6">
      {error ? (
        <Alert>
          <AlertTitle>Analytics error</AlertTitle>
          <AlertDescription>{error}</AlertDescription>
        </Alert>
      ) : null}

      <div className="grid gap-4 md:grid-cols-4">
        {[
          { label: "Products", value: dashboard?.overview?.total_products ?? 0 },
          { label: "Total sales", value: dashboard?.overview?.total_sales ?? 0 },
          { label: "Revenue", value: dashboard?.overview?.total_revenue ?? 0 },
          { label: "Avg rating", value: dashboard?.overview?.average_rating ?? 0 },
        ].map((stat) => (
          <Card key={stat.label}>
            <CardContent>
              <div className="text-xs uppercase tracking-[0.2em] text-slate-400">{stat.label}</div>
              <div className="mt-2 text-xl font-semibold text-ink">{stat.value}</div>
            </CardContent>
          </Card>
        ))}
      </div>

      <Card>
        <CardContent className="space-y-4">
          <TableToolbar title="Top products" description="Sorted by total sold" />
          {dashboard?.top_products?.length ? (
            <Table>
              <TableHeader>
                <TableRow>
                  <TableHead>Product</TableHead>
                  <TableHead>Total sold</TableHead>
                </TableRow>
              </TableHeader>
              <TableBody>
                {dashboard?.top_products?.map((product) => (
                  <TableRow key={product.id}>
                    <TableCell>{product.name}</TableCell>
                    <TableCell>{product.total_sold ?? 0}</TableCell>
                  </TableRow>
                ))}
              </TableBody>
            </Table>
          ) : (
            <EmptyState title="No top products yet" description="Complete orders to see performance." />
          )}
        </CardContent>
      </Card>

      <Card>
        <CardContent className="space-y-4">
          <TableToolbar title="Sales analytics" description="Sorted by date" />
          {sales?.daily_sales?.length ? (
            <Table>
              <TableHeader>
                <TableRow>
                  <TableHead>Date</TableHead>
                  <TableHead>Quantity</TableHead>
                  <TableHead>Revenue</TableHead>
                </TableRow>
              </TableHeader>
              <TableBody>
                {sales?.daily_sales?.map((entry: any, index: number) => (
                  <TableRow key={`${entry.date}-${index}`}>
                    <TableCell>{entry.date}</TableCell>
                    <TableCell>{entry.total_quantity}</TableCell>
                    <TableCell>{entry.total_revenue}</TableCell>
                  </TableRow>
                ))}
              </TableBody>
            </Table>
          ) : (
            <EmptyState title="No sales data" description="Sales analytics will appear after orders complete." />
          )}
        </CardContent>
      </Card>

      <Card>
        <CardContent className="space-y-4">
          <TableToolbar title="Product performance" description="Sorted by performance score" />
          {products?.length ? (
            <Table>
              <TableHeader>
                <TableRow>
                  <TableHead>Name</TableHead>
                  <TableHead>Total sold</TableHead>
                  <TableHead>Revenue</TableHead>
                  <TableHead>Rating</TableHead>
                </TableRow>
              </TableHeader>
              <TableBody>
                {products?.map((product) => (
                  <TableRow key={product.id}>
                    <TableCell>{product.name}</TableCell>
                    <TableCell>{product.total_sold}</TableCell>
                    <TableCell>{product.total_revenue}</TableCell>
                    <TableCell>{product.average_rating}</TableCell>
                  </TableRow>
                ))}
              </TableBody>
            </Table>
          ) : (
            <EmptyState title="No performance data" description="Product performance will appear once sales exist." />
          )}
        </CardContent>
      </Card>

      <Card>
        <CardContent className="space-y-4">
          <TableToolbar title="Top customers" description="Sorted by total spent" />
          {customers?.top_customers?.length ? (
            <Table>
              <TableHeader>
                <TableRow>
                  <TableHead>Name</TableHead>
                  <TableHead>Email</TableHead>
                  <TableHead>Total spent</TableHead>
                </TableRow>
              </TableHeader>
              <TableBody>
                {customers?.top_customers?.map((customer: any) => (
                  <TableRow key={customer.id}>
                    <TableCell>{customer.name}</TableCell>
                    <TableCell>{customer.email}</TableCell>
                    <TableCell>{customer.total_spent}</TableCell>
                  </TableRow>
                ))}
              </TableBody>
            </Table>
          ) : (
            <EmptyState title="No customer data" description="Customer analytics will appear after sales occur." />
          )}
        </CardContent>
      </Card>
    </div>
  )
}
