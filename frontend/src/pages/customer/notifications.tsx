import * as React from "react"
import { Card, CardContent } from "../../components/ui/card"
import { Badge } from "../../components/ui/badge"
import { Button } from "../../components/ui/button"
import { Alert, AlertDescription, AlertTitle } from "../../components/ui/alert"
import { Skeleton } from "../../components/ui/skeleton"
import { EmptyState } from "../../components/ui/empty-state"
import { TablePagination } from "../../components/ui/table"
import { apiFetch, ApiError } from "../../lib/api"
import { buildQuery } from "../../lib/query"
import { useToast } from "../../components/ui/toast"

type Notification = {
  id: number
  title?: string
  message?: string
  type?: string
  is_read?: boolean
  created_at?: string
}

type NotificationStats = {
  total: number
  unread: number
}

type Paginator<T> = {
  data: T[]
  current_page: number
  last_page: number
}

export function CustomerNotificationsPage() {
  const { push } = useToast()
  const [stats, setStats] = React.useState<NotificationStats | null>(null)
  const [notifications, setNotifications] = React.useState<Paginator<Notification> | null>(null)
  const [filters, setFilters] = React.useState({ type: "", is_read: "", page: 1 })
  const [error, setError] = React.useState<string | null>(null)
  const [loading, setLoading] = React.useState(false)

  const loadNotifications = React.useCallback(() => {
    setLoading(true)
    setError(null)

    Promise.all([
      apiFetch("/notifications/stats"),
      apiFetch(`/notifications${buildQuery({
        type: filters.type,
        is_read: filters.is_read,
        page: filters.page,
      })}`),
    ])
      .then(([statsPayload, listPayload]) => {
        setStats((statsPayload as { data?: NotificationStats }).data ?? null)
        setNotifications((listPayload as { data?: Paginator<Notification> }).data ?? null)
      })
      .catch((err) => {
        const message = err instanceof ApiError ? err.message : "Failed to load notifications"
        setError(message)
      })
      .finally(() => setLoading(false))
  }, [filters])

  React.useEffect(() => {
    loadNotifications()
  }, [loadNotifications])

  async function handleMarkRead(id: number) {
    try {
      await apiFetch(`/notifications/${id}/read`, { method: "PUT" })
      loadNotifications()
    } catch (err) {
      const message = err instanceof ApiError ? err.message : "Failed to mark read"
      push({ title: "Notification error", description: message, variant: "error" })
    }
  }

  async function handleMarkAll() {
    try {
      await apiFetch("/notifications/mark-all-read", { method: "PUT" })
      loadNotifications()
    } catch (err) {
      const message = err instanceof ApiError ? err.message : "Failed to mark all"
      push({ title: "Notification error", description: message, variant: "error" })
    }
  }

  async function handleDelete(id: number) {
    try {
      await apiFetch(`/notifications/${id}`, { method: "DELETE" })
      loadNotifications()
    } catch (err) {
      const message = err instanceof ApiError ? err.message : "Failed to delete"
      push({ title: "Notification error", description: message, variant: "error" })
    }
  }

  const hasNotifications = (notifications?.data?.length ?? 0) > 0

  return (
    <div className="space-y-6">
      <div className="grid gap-4 md:grid-cols-2">
        <Card>
          <CardContent>
            <div className="text-xs uppercase tracking-[0.2em] text-slate-400">Total</div>
            <div className="mt-2 text-2xl font-semibold text-ink">{stats?.total ?? 0}</div>
          </CardContent>
        </Card>
        <Card>
          <CardContent>
            <div className="text-xs uppercase tracking-[0.2em] text-slate-400">Unread</div>
            <div className="mt-2 text-2xl font-semibold text-ink">{stats?.unread ?? 0}</div>
          </CardContent>
        </Card>
      </div>

      <div className="flex flex-wrap items-center justify-between gap-4">
        <div className="flex flex-wrap gap-3">
          <input
            className="rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm"
            placeholder="Type filter (optional)"
            value={filters.type}
            onChange={(event) => setFilters((prev) => ({ ...prev, type: event.target.value, page: 1 }))}
          />
          <select
            className="rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm"
            value={filters.is_read}
            onChange={(event) => setFilters((prev) => ({ ...prev, is_read: event.target.value, page: 1 }))}
          >
            <option value="">All status</option>
            <option value="true">Read</option>
            <option value="false">Unread</option>
          </select>
        </div>
        <Button variant="outline" onClick={handleMarkAll}>
          Mark all read
        </Button>
      </div>

      {error ? (
        <Alert>
          <AlertTitle>Notifications error</AlertTitle>
          <AlertDescription>{error}</AlertDescription>
        </Alert>
      ) : null}

      {loading ? (
        <div className="space-y-3">
          {Array.from({ length: 3 }).map((_, index) => (
            <Card key={`notif-skeleton-${index}`}>
              <CardContent className="space-y-3">
                <Skeleton className="h-4 w-1/3" />
                <Skeleton className="h-3 w-2/3" />
              </CardContent>
            </Card>
          ))}
        </div>
      ) : null}

      {!loading && !hasNotifications ? (
        <EmptyState title="No notifications" description="Updates and alerts will appear here." />
      ) : null}

      {!loading && hasNotifications ? (
        <div className="space-y-3">
          {notifications?.data?.map((notification) => (
            <Card key={notification.id}>
              <CardContent className="flex flex-wrap items-center justify-between gap-4">
                <div>
                  <div className="font-semibold text-ink">{notification.title ?? "Notification"}</div>
                  <div className="text-xs text-slate-500">{notification.message ?? ""}</div>
                </div>
                <div className="flex items-center gap-2">
                  <Badge variant={notification.is_read ? "outline" : "soft"}>
                    {notification.is_read ? "Read" : "Unread"}
                  </Badge>
                  {!notification.is_read ? (
                    <Button variant="ghost" onClick={() => handleMarkRead(notification.id)}>
                      Mark read
                    </Button>
                  ) : null}
                  <Button variant="ghost" onClick={() => handleDelete(notification.id)}>
                    Delete
                  </Button>
                </div>
              </CardContent>
            </Card>
          ))}
        </div>
      ) : null}

      {notifications && hasNotifications ? (
        <TablePagination
          page={notifications.current_page}
          totalPages={notifications.last_page}
          onPrev={() => setFilters((prev) => ({ ...prev, page: prev.page - 1 }))}
          onNext={() => setFilters((prev) => ({ ...prev, page: prev.page + 1 }))}
        />
      ) : null}
    </div>
  )
}
