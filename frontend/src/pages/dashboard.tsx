import * as React from "react"
import { Card, CardContent } from "../components/ui/card"
import { Badge } from "../components/ui/badge"
import { Alert, AlertDescription, AlertTitle } from "../components/ui/alert"
import { EmptyState } from "../components/ui/empty-state"
import { Skeleton } from "../components/ui/skeleton"
import { apiFetch, ApiError, unwrapList } from "../lib/api"
import { buildQuery } from "../lib/query"
import { useAuth, useRole } from "../store/auth"

type KpiCard = {
  label: string
  value: string
  helper?: string
}

type ActivityItem = {
  id: string
  title: string
  subtitle?: string
  badge?: string
}

type ActivityStream = {
  title: string
  items: ActivityItem[]
  emptyMessage: string
}

type TodayTask = {
  label: string
  value: string
}

type DashboardState = {
  kpis: KpiCard[]
  streams: ActivityStream[]
  tasks: TodayTask[]
}

type Order = {
  id: number
  status: string
  total_amount: number
  payment_method?: string
  placed_at?: string
  created_at?: string
  user?: { name?: string }
}

type WalletTransaction = {
  id: number
  amount: number
  title?: string
  type?: string
  description?: string
  created_at?: string
}

type LoyaltyPoint = {
  id: number
  points: number
  type: string
  description?: string
  created_at?: string
}

type MerchantOverview = {
  overview?: {
    total_products?: number
    total_sales?: number
    total_revenue?: number
    average_rating?: number
  }
  top_products?: Array<{ id: number; name: string; total_sold?: number }>
  sales_trend?: Array<{ date: string; total_quantity: number; total_revenue: number }>
  period_days?: number | string
}

type MerchantCustomers = {
  top_customers?: Array<{ id: number; name: string; total_spent?: number }>
  customer_acquisition?: Array<{ first_purchase_date?: string; new_customers?: number }>
}

type WalletBalance = {
  balance?: number
  formatted?: string
}

type LoyaltyBalance = {
  balance?: number
}

type ActivityStats = {
  total_orders?: number
  pending?: number
  completed?: number
  total_spent?: number
}

type PendingMerchant = {
  id: number
  name?: string
  email?: string
  merchant_status?: string
}

function mapOverviewError(error: unknown) {
  if (error instanceof ApiError) {
    if (error.statusCode === 403) return "Access restricted"
    if (error.statusCode >= 500) return "Server error"
    return error.message
  }
  return "Network error"
}

function isRecord(value: unknown): value is Record<string, unknown> {
  return typeof value === "object" && value !== null && !Array.isArray(value)
}

function readNumber(value: unknown, fallback = 0) {
  if (typeof value === "number") return value
  if (typeof value === "string") {
    const parsed = Number(value)
    return Number.isFinite(parsed) ? parsed : fallback
  }
  return fallback
}

function readMetaNumber(meta: unknown, key: string, fallback = 0) {
  if (!isRecord(meta)) return fallback
  return readNumber(meta[key], fallback)
}

function todayString() {
  const now = new Date()
  const year = now.getFullYear()
  const month = String(now.getMonth() + 1).padStart(2, "0")
  const day = String(now.getDate()).padStart(2, "0")
  return `${year}-${month}-${day}`
}

export function DashboardPage() {
  const { user } = useAuth()
  const role = useRole()
  const [state, setState] = React.useState<DashboardState | null>(null)
  const [loading, setLoading] = React.useState(true)
  const [error, setError] = React.useState<string | null>(null)

  React.useEffect(() => {
    if (!role) return
    const day = todayString()

    async function loadCustomerOverview() {
      const activitiesQuery = buildQuery({ status: "all", page: 1, per_page: 5 })
      const todayCompletedQuery = buildQuery({
        status: "completed",
        start_date: day,
        end_date: day,
        per_page: 1,
      })

      const [
        activitiesPayload,
        walletBalancePayload,
        walletTransactionsPayload,
        loyaltyBalancePayload,
        loyaltyHistoryPayload,
        completedPayload,
      ] = await Promise.all([
        apiFetch(`/activities${activitiesQuery}`),
        apiFetch("/wallet/balance"),
        apiFetch(`/wallet/transactions${buildQuery({ page: 1, per_page: 5 })}`),
        apiFetch("/loyalty/balance"),
        apiFetch(`/loyalty/history${buildQuery({ page: 1, per_page: 5 })}`),
        apiFetch(`/activities${todayCompletedQuery}`),
      ])

      const root = (activitiesPayload as { data?: unknown }).data ?? activitiesPayload
      const stats = isRecord(root) && isRecord(root.stats) ? (root.stats as ActivityStats) : null
      const activityList = unwrapList<Order>(activitiesPayload)

      const walletBalance = (walletBalancePayload as { data?: WalletBalance }).data ?? null
      const walletTransactions = unwrapList<WalletTransaction>(walletTransactionsPayload)

      const loyaltyBalance = (loyaltyBalancePayload as { data?: LoyaltyBalance }).data ?? null
      const loyaltyHistory = unwrapList<LoyaltyPoint>(loyaltyHistoryPayload)

      const completedMeta = unwrapList<Order>(completedPayload)
      const completedToday = readMetaNumber(completedMeta.meta, "total", completedMeta.items.length)

      const kpis: KpiCard[] = [
        {
          label: "Realtime order flow",
          value: String(stats?.total_orders ?? activityList.items.length),
          helper: `Pending ${stats?.pending ?? 0} | Completed ${stats?.completed ?? 0}`,
        },
        {
          label: "Wallet movements",
          value: walletBalance?.formatted ?? String(walletBalance?.balance ?? 0),
          helper: `Recent ${walletTransactions.items.length} transactions`,
        },
        {
          label: "Merchant performance",
          value: "-",
          helper: "Not available for customer accounts.",
        },
        {
          label: "Customer retention",
          value: String(loyaltyBalance?.balance ?? 0),
          helper: `Recent ${loyaltyHistory.items.length} loyalty events`,
        },
      ]

      const streams: ActivityStream[] = [
        {
          title: "Recent orders",
          emptyMessage: "No orders yet.",
          items: activityList.items.map((order) => ({
            id: String(order.id),
            title: `Order #${order.id}`,
            subtitle: order.placed_at ?? order.created_at ?? "",
            badge: order.status,
          })),
        },
        {
          title: "Wallet movements",
          emptyMessage: "No wallet activity yet.",
          items: walletTransactions.items.map((tx) => ({
            id: String(tx.id),
            title: tx.title ?? tx.description ?? `Transaction #${tx.id}`,
            subtitle: tx.created_at ?? "",
            badge: tx.type ?? "",
          })),
        },
        {
          title: "Loyalty activity",
          emptyMessage: "No loyalty updates yet.",
          items: loyaltyHistory.items.map((entry) => ({
            id: String(entry.id),
            title: entry.description ?? `Points ${entry.points}`,
            subtitle: entry.created_at ?? "",
            badge: entry.type,
          })),
        },
      ]

      const tasks: TodayTask[] = [
        {
          label: "Sync profile data with /me",
          value: user ? `Synced as ${user.name}` : "Not synced",
        },
        {
          label: "Review pending approvals",
          value: "Not applicable for customer.",
        },
        {
          label: "Monitor checkout success",
          value: `${completedToday} completed orders today`,
        },
      ]

      return { kpis, streams, tasks }
    }

    async function loadMerchantOverview() {
      const todayQuery = buildQuery({ start_date: day, end_date: day, per_page: 5 })
      const todayCompletedQuery = buildQuery({
        start_date: day,
        end_date: day,
        status: "completed",
        per_page: 1,
      })

      const [
        dashboardPayload,
        customersPayload,
        ordersPayload,
        todayCompletedPayload,
      ] = await Promise.all([
        apiFetch("/merchant/analytics/dashboard"),
        apiFetch("/merchant/analytics/customers"),
        apiFetch(`/merchant/orders${todayQuery}`),
        apiFetch(`/merchant/orders${todayCompletedQuery}`),
      ])

      const overview = (dashboardPayload as { data?: MerchantOverview }).data ?? null
      const customerInsights = (customersPayload as { data?: MerchantCustomers }).data ?? null

      const orderList = unwrapList<Order>(ordersPayload)
      const ordersToday = readMetaNumber(orderList.meta, "total", orderList.items.length)

      const completedMeta = unwrapList<Order>(todayCompletedPayload)
      const completedToday = readMetaNumber(completedMeta.meta, "total", completedMeta.items.length)

      const kpis: KpiCard[] = [
        {
          label: "Realtime order flow",
          value: String(ordersToday),
          helper: `Completed today: ${completedToday}`,
        },
        {
          label: "Wallet movements",
          value: "-",
          helper: "Not available for merchant accounts.",
        },
        {
          label: "Merchant performance",
          value: String(overview?.overview?.total_revenue ?? 0),
          helper: `Sales ${overview?.overview?.total_sales ?? 0} | Rating ${overview?.overview?.average_rating ?? 0}`,
        },
        {
          label: "Customer retention",
          value: String(customerInsights?.top_customers?.length ?? 0),
          helper: "Top customers tracked",
        },
      ]

      const streams: ActivityStream[] = [
        {
          title: "Recent orders",
          emptyMessage: "No orders today.",
          items: orderList.items.map((order) => ({
            id: String(order.id),
            title: `Order #${order.id}`,
            subtitle: order.user?.name ?? "",
            badge: order.status,
          })),
        },
        {
          title: "Top customers",
          emptyMessage: "No customer data yet.",
          items: (customerInsights?.top_customers ?? []).map((customer) => ({
            id: String(customer.id),
            title: customer.name ?? "Customer",
            subtitle: `Total spent ${customer.total_spent ?? 0}`,
          })),
        },
      ]

      const tasks: TodayTask[] = [
        {
          label: "Sync profile data with /me",
          value: user ? `Synced as ${user.name}` : "Not synced",
        },
        {
          label: "Review pending approvals",
          value: user?.merchant_status === "approved" ? "Approved" : "Pending approval",
        },
        {
          label: "Monitor checkout success",
          value: `${completedToday} completed orders today`,
        },
      ]

      return { kpis, streams, tasks }
    }

    async function loadAdminOverview() {
      const todayQuery = buildQuery({ start_date: day, end_date: day, per_page: 5 })
      const todayCompletedQuery = buildQuery({
        start_date: day,
        end_date: day,
        status: "completed",
        per_page: 1,
      })
      const pendingQuery = buildQuery({ role: "merchant", merchant_status: "pending", per_page: 5 })

      const [ordersPayload, completedPayload, pendingPayload] = await Promise.all([
        apiFetch(`/admin/orders${todayQuery}`),
        apiFetch(`/admin/orders${todayCompletedQuery}`),
        apiFetch(`/admin/users${pendingQuery}`),
      ])

      const orderList = unwrapList<Order>(ordersPayload)
      const ordersToday = readMetaNumber(orderList.meta, "total", orderList.items.length)

      const completedMeta = unwrapList<Order>(completedPayload)
      const completedToday = readMetaNumber(completedMeta.meta, "total", completedMeta.items.length)

      const pendingList = unwrapList<PendingMerchant>(pendingPayload)
      const pendingCount = readMetaNumber(pendingList.meta, "total", pendingList.items.length)

      const kpis: KpiCard[] = [
        {
          label: "Realtime order flow",
          value: String(ordersToday),
          helper: "Orders created today",
        },
        {
          label: "Wallet movements",
          value: "-",
          helper: "No admin wallet endpoint available.",
        },
        {
          label: "Merchant performance",
          value: "-",
          helper: "No aggregate merchant analytics endpoint.",
        },
        {
          label: "Customer retention",
          value: "-",
          helper: "No retention endpoint for admin.",
        },
      ]

      const streams: ActivityStream[] = [
        {
          title: "Recent orders",
          emptyMessage: "No orders today.",
          items: orderList.items.map((order) => ({
            id: String(order.id),
            title: `Order #${order.id}`,
            subtitle: order.user?.name ?? "",
            badge: order.status,
          })),
        },
        {
          title: "Pending approvals",
          emptyMessage: "No pending merchant approvals.",
          items: pendingList.items.map((merchant) => ({
            id: String(merchant.id),
            title: merchant.name ?? "Merchant",
            subtitle: merchant.email ?? "",
            badge: merchant.merchant_status ?? "",
          })),
        },
      ]

      const tasks: TodayTask[] = [
        {
          label: "Sync profile data with /me",
          value: user ? `Synced as ${user.name}` : "Not synced",
        },
        {
          label: "Review pending approvals",
          value: `${pendingCount} merchants pending`,
        },
        {
          label: "Monitor checkout success",
          value: `${completedToday} completed orders today`,
        },
      ]

      return { kpis, streams, tasks }
    }

    setLoading(true)
    setError(null)

    const loader =
      role === "admin"
        ? loadAdminOverview
        : role === "merchant"
          ? loadMerchantOverview
          : loadCustomerOverview

    loader()
      .then((result) => setState(result))
      .catch((err) => {
        const message = mapOverviewError(err)
        setError(message)
      })
      .finally(() => setLoading(false))
  }, [role, user])

  return (
    <div className="grid gap-6 lg:grid-cols-[1.2fr_0.8fr]">
      <Card>
        <CardContent className="space-y-4">
          <div className="flex items-center justify-between">
            <div>
              <div className="text-xs uppercase tracking-[0.2em] text-slate-400">Workspace</div>
              <div className="font-display text-2xl font-semibold text-ink">Operational pulse</div>
            </div>
            <Badge variant="soft">Live</Badge>
          </div>
          {error ? (
            <Alert>
              <AlertTitle>Overview error</AlertTitle>
              <AlertDescription>{error}</AlertDescription>
            </Alert>
          ) : null}

          {loading ? (
            <div className="grid gap-3 sm:grid-cols-2">
              {Array.from({ length: 4 }).map((_, index) => (
                <Card key={`kpi-skeleton-${index}`}>
                  <CardContent className="space-y-2">
                    <Skeleton className="h-4 w-1/2" />
                    <Skeleton className="h-6 w-2/3" />
                  </CardContent>
                </Card>
              ))}
            </div>
          ) : null}

          {!loading && state ? (
            <div className="grid gap-3 sm:grid-cols-2">
              {state.kpis.map((item) => (
                <Card key={item.label}>
                  <CardContent className="space-y-1">
                    <div className="text-xs uppercase tracking-[0.2em] text-slate-400">{item.label}</div>
                    <div className="text-xl font-semibold text-ink">{item.value}</div>
                    {item.helper ? <div className="text-xs text-slate-500">{item.helper}</div> : null}
                  </CardContent>
                </Card>
              ))}
            </div>
          ) : null}

          {!loading && state ? (
            <div className="space-y-3">
              <div className="text-xs uppercase tracking-[0.2em] text-slate-400">Activity streams</div>
              <div className="grid gap-3 md:grid-cols-2">
                {state.streams.map((stream) => (
                  <Card key={stream.title}>
                    <CardContent className="space-y-3">
                      <div className="font-semibold text-ink">{stream.title}</div>
                      {stream.items.length ? (
                        <div className="space-y-2 text-sm">
                          {stream.items.map((item) => (
                            <div key={item.id} className="flex items-center justify-between gap-3">
                              <div>
                                <div className="font-semibold text-ink">{item.title}</div>
                                {item.subtitle ? (
                                  <div className="text-xs text-slate-500">{item.subtitle}</div>
                                ) : null}
                              </div>
                              {item.badge ? <Badge variant="soft">{item.badge}</Badge> : null}
                            </div>
                          ))}
                        </div>
                      ) : (
                        <EmptyState title={stream.emptyMessage} />
                      )}
                    </CardContent>
                  </Card>
                ))}
              </div>
            </div>
          ) : null}
        </CardContent>
      </Card>
      <Card>
        <CardContent className="space-y-4">
          <div className="text-xs uppercase tracking-[0.2em] text-slate-400">Today</div>
          {loading ? (
            <div className="space-y-3">
              {Array.from({ length: 3 }).map((_, index) => (
                <Card key={`task-skeleton-${index}`}>
                  <CardContent className="space-y-2">
                    <Skeleton className="h-4 w-2/3" />
                    <Skeleton className="h-4 w-1/2" />
                  </CardContent>
                </Card>
              ))}
            </div>
          ) : null}

          {!loading && state ? (
            <div className="space-y-3">
              {state.tasks.map((task) => (
                <div key={task.label} className="rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm">
                  <div className="font-semibold text-ink">{task.label}</div>
                  <div className="text-xs text-slate-500">{task.value}</div>
                </div>
              ))}
            </div>
          ) : null}
        </CardContent>
      </Card>
    </div>
  )
}
