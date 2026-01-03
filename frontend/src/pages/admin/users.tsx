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

type User = {
  id: number
  name: string
  email: string
  role?: { name: string }
  merchant_status?: string | null
  is_banned?: boolean
}

type Paginator<T> = {
  data: T[]
  current_page: number
  last_page: number
  total: number
}

export function AdminUsersPage() {
  const [users, setUsers] = React.useState<Paginator<User> | null>(null)
  const [filters, setFilters] = React.useState({
    role: "",
    merchant_status: "",
    is_banned: "",
    search: "",
    page: 1,
  })
  const [error, setError] = React.useState<string | null>(null)
  const [loading, setLoading] = React.useState(false)

  React.useEffect(() => {
    setError(null)
    setLoading(true)

    apiFetch(`/admin/users${buildQuery(filters)}`)
      .then((payload) => setUsers((payload as { data?: Paginator<User> }).data ?? null))
      .catch((err) => {
        const message = err instanceof ApiError ? err.message : "Failed to load users"
        setError(message)
      })
      .finally(() => setLoading(false))
  }, [filters])

  const hasUsers = (users?.data?.length ?? 0) > 0

  return (
    <div className="space-y-6">
      <div className="flex flex-wrap gap-3">
        <Input
          placeholder="Search name, email, username"
          value={filters.search}
          onChange={(event) => setFilters((prev) => ({ ...prev, search: event.target.value, page: 1 }))}
        />
        <select
          className="rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm"
          value={filters.role}
          onChange={(event) => setFilters((prev) => ({ ...prev, role: event.target.value, page: 1 }))}
        >
          <option value="">All roles</option>
          <option value="admin">Admin</option>
          <option value="merchant">Merchant</option>
          <option value="customer">Customer</option>
        </select>
        <select
          className="rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm"
          value={filters.merchant_status}
          onChange={(event) => setFilters((prev) => ({ ...prev, merchant_status: event.target.value, page: 1 }))}
        >
          <option value="">Merchant status</option>
          <option value="pending">Pending</option>
          <option value="approved">Approved</option>
        </select>
        <select
          className="rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm"
          value={filters.is_banned}
          onChange={(event) => setFilters((prev) => ({ ...prev, is_banned: event.target.value, page: 1 }))}
        >
          <option value="">Ban status</option>
          <option value="true">Banned</option>
          <option value="false">Active</option>
        </select>
      </div>

      {error ? (
        <Alert>
          <AlertTitle>Users error</AlertTitle>
          <AlertDescription>{error}</AlertDescription>
        </Alert>
      ) : null}

      {loading ? (
        <div className="space-y-3">
          {Array.from({ length: 3 }).map((_, index) => (
            <Card key={`admin-user-skeleton-${index}`}>
              <CardContent className="space-y-3">
                <Skeleton className="h-4 w-1/3" />
                <Skeleton className="h-3 w-1/2" />
              </CardContent>
            </Card>
          ))}
        </div>
      ) : null}

      {!loading && !hasUsers ? (
        <EmptyState title="No users found" description="Try adjusting your filters." />
      ) : null}

      {!loading && hasUsers ? (
        <div className="space-y-4">
          {users?.data.map((user) => (
            <Card key={user.id}>
              <CardContent className="flex flex-wrap items-center justify-between gap-4">
                <div>
                  <div className="font-semibold text-ink">{user.name}</div>
                  <div className="text-xs text-slate-500">{user.email}</div>
                </div>
                <div className="flex items-center gap-3">
                  <Badge variant="outline">{user.role?.name ?? ""}</Badge>
                  {user.merchant_status ? <Badge variant="soft">{user.merchant_status}</Badge> : null}
                  {user.is_banned ? <Badge variant="outline">Banned</Badge> : null}
                  <Button variant="outline" asChild>
                    <Link to={`/app/admin/users/${user.id}`}>View</Link>
                  </Button>
                </div>
              </CardContent>
            </Card>
          ))}
        </div>
      ) : null}

      {users && hasUsers ? (
        <TablePagination
          page={users.current_page}
          totalPages={users.last_page}
          total={users.total}
          onPrev={() => setFilters((prev) => ({ ...prev, page: prev.page - 1 }))}
          onNext={() => setFilters((prev) => ({ ...prev, page: prev.page + 1 }))}
        />
      ) : null}
    </div>
  )
}
