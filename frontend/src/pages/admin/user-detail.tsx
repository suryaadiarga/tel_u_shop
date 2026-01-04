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

export function AdminUserDetailPage() {
  const { id } = useParams()
  const { push } = useToast()
  const [user, setUser] = React.useState<any | null>(null)
  const [error, setError] = React.useState<string | null>(null)
  const [role, setRole] = React.useState("customer")
  const [loading, setLoading] = React.useState(true)

  const loadUser = React.useCallback(() => {
    if (!id) return
    setError(null)
    setLoading(true)

    apiFetch(`/admin/users/${id}`)
      .then((payload) => {
        const data = (payload as { data?: any }).data ?? null
        setUser(data)
        if (data?.role?.name) setRole(data.role.name)
      })
      .catch((err) => {
        const message = formatApiError(err, "Failed to load user")
        setError(message)
      })
      .finally(() => setLoading(false))
  }, [id])

  React.useEffect(() => {
    loadUser()
  }, [loadUser])

  async function handleRoleUpdate() {
    if (!id) return
    try {
      await apiFetch(`/admin/users/${id}/role`, {
        method: "PUT",
        body: { role },
      })
      push({ title: "Role updated", variant: "success" })
      loadUser()
    } catch (err) {
      const message = formatApiError(err, "Failed to update role")
      push({ title: "Role update error", description: message, variant: "error" })
    }
  }

  async function handleApproveMerchant() {
    if (!id) return
    try {
      await apiFetch(`/admin/merchants/${id}/approve`, { method: "PUT" })
      push({ title: "Merchant approved", variant: "success" })
      loadUser()
    } catch (err) {
      const message = formatApiError(err, "Failed to approve merchant")
      push({ title: "Approval error", description: message, variant: "error" })
    }
  }

  async function handleBan() {
    if (!id) return
    try {
      await apiFetch(`/admin/users/${id}/ban`, { method: "PUT" })
      push({ title: "User banned", variant: "success" })
      loadUser()
    } catch (err) {
      const message = formatApiError(err, "Failed to ban user")
      push({ title: "Ban error", description: message, variant: "error" })
    }
  }

  async function handleUnban() {
    if (!id) return
    try {
      await apiFetch(`/admin/users/${id}/unban`, { method: "PUT" })
      push({ title: "User unbanned", variant: "success" })
      loadUser()
    } catch (err) {
      const message = formatApiError(err, "Failed to unban user")
      push({ title: "Unban error", description: message, variant: "error" })
    }
  }

  async function handleActivate() {
    if (!id) return
    try {
      await apiFetch(`/admin/users/${id}/activate`, { method: "PUT" })
      push({ title: "User activated", variant: "success" })
      loadUser()
    } catch (err) {
      const message = formatApiError(err, "Failed to activate user")
      push({ title: "Activate error", description: message, variant: "error" })
    }
  }

  async function handleDeactivate() {
    if (!id) return
    try {
      await apiFetch(`/admin/users/${id}/deactivate`, { method: "PUT" })
      push({ title: "User deactivated", variant: "success" })
      loadUser()
    } catch (err) {
      const message = formatApiError(err, "Failed to deactivate user")
      push({ title: "Deactivate error", description: message, variant: "error" })
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
      </div>
    )
  }

  if (!user || error) {
    return (
      <div className="space-y-4">
        <Alert>
          <AlertTitle>User not available</AlertTitle>
          <AlertDescription>{error ?? "User not found"}</AlertDescription>
        </Alert>
        <Button asChild>
          <Link to="/app/admin/users">Back to users</Link>
        </Button>
      </div>
    )
  }

  return (
    <div className="space-y-6">
      <Card>
        <CardContent className="space-y-4">
          <div className="flex flex-wrap items-center justify-between gap-4">
            <div>
              <div className="font-display text-xl font-semibold text-ink">{user.name}</div>
              <div className="text-sm text-slate-500">{user.email}</div>
            </div>
            <div className="flex flex-wrap gap-2">
              <Badge variant="outline">{user.role?.name ?? ""}</Badge>
              {user.merchant_status ? <Badge variant="soft">{user.merchant_status}</Badge> : null}
              {user.is_banned ? <Badge variant="outline">Banned</Badge> : null}
            </div>
          </div>

          <div className="grid gap-3 md:grid-cols-2">
            <div className="space-y-2">
              <label className="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Role</label>
              <select
                className="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm"
                value={role}
                onChange={(event) => setRole(event.target.value)}
              >
                <option value="admin">Admin</option>
                <option value="merchant">Merchant</option>
                <option value="customer">Customer</option>
              </select>
              <Button onClick={handleRoleUpdate} variant="secondary">
                Update role
              </Button>
            </div>
            <div className="space-y-2">
              <label className="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Actions</label>
              <div className="flex flex-wrap gap-2">
                {user.role?.name === "merchant" && user.merchant_status !== "approved" ? (
                  <Button onClick={handleApproveMerchant}>Approve merchant</Button>
                ) : null}
                {user.is_banned ? (
                  <Button variant="outline" onClick={handleUnban}>
                    Unban
                  </Button>
                ) : (
                  <Button variant="danger" onClick={handleBan}>
                    Ban
                  </Button>
                )}
                <Button variant="outline" onClick={handleActivate} disabled={!user.is_banned}>
                  Activate
                </Button>
                <Button variant="outline" onClick={handleDeactivate} disabled={user.is_banned}>
                  Deactivate
                </Button>
              </div>
            </div>
          </div>
        </CardContent>
      </Card>
    </div>
  )
}
