import * as React from "react"
import { Card, CardContent } from "../../components/ui/card"
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TablePagination,
  TableRow,
  TableToolbar,
} from "../../components/ui/table"
import { Input } from "../../components/ui/input"
import { Button } from "../../components/ui/button"
import { Badge } from "../../components/ui/badge"
import { Alert, AlertDescription, AlertTitle } from "../../components/ui/alert"
import { Skeleton } from "../../components/ui/skeleton"
import { EmptyState } from "../../components/ui/empty-state"
import { apiFetch, ApiError } from "../../lib/api"
import { buildQuery } from "../../lib/query"
import { useToast } from "../../components/ui/toast"

type LoyaltyBalance = {
  balance: number
}

type LoyaltyHistory = {
  id: number
  points: number
  type: string
  description?: string
  created_at?: string
}

type Reward = {
  id: number
  name: string
  points_required: number
  description: string
  type: string
  can_redeem?: boolean
}

type Paginator<T> = {
  data: T[]
  current_page: number
  last_page: number
  total: number
}

export function CustomerLoyaltyPage() {
  const { push } = useToast()
  const [balance, setBalance] = React.useState<LoyaltyBalance | null>(null)
  const [history, setHistory] = React.useState<Paginator<LoyaltyHistory> | null>(null)
  const [rewards, setRewards] = React.useState<Reward[]>([])
  const [filters, setFilters] = React.useState({ type: "", page: 1 })
  const [redeemForm, setRedeemForm] = React.useState({ points: "", description: "" })
  const [error, setError] = React.useState<string | null>(null)
  const [loading, setLoading] = React.useState(false)
  const [fieldErrors, setFieldErrors] = React.useState<Record<string, string[]>>({})

  const loadLoyalty = React.useCallback(() => {
    setError(null)
    setLoading(true)
    Promise.all([
      apiFetch("/loyalty/balance"),
      apiFetch(`/loyalty/history${buildQuery({ type: filters.type, page: filters.page })}`),
      apiFetch("/loyalty/rewards"),
    ])
      .then(([balancePayload, historyPayload, rewardsPayload]) => {
        setBalance((balancePayload as { data?: LoyaltyBalance }).data ?? null)
        setHistory((historyPayload as { data?: Paginator<LoyaltyHistory> }).data ?? null)
        setRewards((rewardsPayload as { data?: Reward[] }).data ?? [])
      })
      .catch((err) => {
        const message = err instanceof ApiError ? err.message : "Failed to load loyalty data"
        setError(message)
      })
      .finally(() => setLoading(false))
  }, [filters])

  React.useEffect(() => {
    loadLoyalty()
  }, [loadLoyalty])

  async function handleRedeem() {
    setFieldErrors({})
    try {
      await apiFetch("/loyalty/redeem", {
        method: "POST",
        body: {
          points: Number(redeemForm.points),
          description: redeemForm.description,
        },
      })
      push({ title: "Redeemed successfully", variant: "success" })
      setRedeemForm({ points: "", description: "" })
      loadLoyalty()
    } catch (err) {
      if (err instanceof ApiError) {
        push({ title: "Redeem error", description: err.message, variant: "error" })
        if (err.errors && typeof err.errors === "object" && !Array.isArray(err.errors)) {
          setFieldErrors(err.errors as Record<string, string[]>)
        }
      } else {
        push({ title: "Redeem error", description: "Redeem failed", variant: "error" })
      }
    }
  }

  const hasHistory = (history?.data?.length ?? 0) > 0
  const hasRewards = rewards.length > 0

  return (
    <div className="space-y-6">
      {error ? (
        <Alert>
          <AlertTitle>Loyalty error</AlertTitle>
          <AlertDescription>{error}</AlertDescription>
        </Alert>
      ) : null}

      {loading ? (
        <Card>
          <CardContent className="space-y-4">
            <Skeleton className="h-6 w-40" />
            <Skeleton className="h-20 w-full" />
          </CardContent>
        </Card>
      ) : null}

      <Card>
        <CardContent className="flex flex-wrap items-center justify-between gap-4">
          <div>
            <div className="text-xs uppercase tracking-[0.2em] text-slate-400">Current points</div>
            <div className="mt-2 text-2xl font-semibold text-ink">{balance?.balance ?? 0}</div>
          </div>
          <div className="grid gap-2 md:grid-cols-2">
            <Input
              placeholder="Points to redeem"
              type="number"
              min={100}
              value={redeemForm.points}
              onChange={(event) => setRedeemForm((prev) => ({ ...prev, points: event.target.value }))}
            />
            {fieldErrors.points ? (
              <p className="text-xs text-rose-500">{fieldErrors.points.join(", ")}</p>
            ) : null}
            <Input
              placeholder="Description"
              value={redeemForm.description}
              onChange={(event) => setRedeemForm((prev) => ({ ...prev, description: event.target.value }))}
            />
            {fieldErrors.description ? (
              <p className="text-xs text-rose-500">{fieldErrors.description.join(", ")}</p>
            ) : null}
            <Button className="md:col-span-2" onClick={handleRedeem}>
              Redeem points
            </Button>
          </div>
        </CardContent>
      </Card>

      <Card>
        <CardContent className="space-y-4">
          <div className="font-display text-lg font-semibold text-ink">Rewards</div>
          {!hasRewards ? (
            <EmptyState title="No rewards available" description="Rewards will appear when configured." />
          ) : (
            <div className="grid gap-3 md:grid-cols-3">
              {rewards.map((reward) => (
                <div key={reward.id} className="rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm">
                  <div className="font-semibold text-ink">{reward.name}</div>
                  <div className="text-xs text-slate-500">{reward.description}</div>
                  <div className="mt-2 flex items-center justify-between">
                    <Badge variant="soft">{reward.points_required} pts</Badge>
                    <span className="text-xs text-slate-400">{reward.can_redeem ? "Available" : "Locked"}</span>
                  </div>
                </div>
              ))}
            </div>
          )}
        </CardContent>
      </Card>

      <Card>
        <CardContent className="space-y-4">
          <TableToolbar
            title="History"
            description="Sorted by newest"
            actions={
              <select
                className="rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm"
                value={filters.type}
                onChange={(event) => setFilters((prev) => ({ ...prev, type: event.target.value, page: 1 }))}
              >
                <option value="">All types</option>
                <option value="earned">Earned</option>
                <option value="redeemed">Redeemed</option>
              </select>
            }
          />
          {!hasHistory ? (
            <EmptyState title="No loyalty history" description="Your points activity will show here." />
          ) : (
            <Table>
              <TableHeader>
                <TableRow>
                  <TableHead>Description</TableHead>
                  <TableHead>Type</TableHead>
                  <TableHead>Points</TableHead>
                  <TableHead>Date</TableHead>
                </TableRow>
              </TableHeader>
              <TableBody>
                {history?.data?.map((entry) => (
                  <TableRow key={entry.id}>
                    <TableCell>{entry.description ?? "-"}</TableCell>
                    <TableCell>{entry.type}</TableCell>
                    <TableCell>{entry.points}</TableCell>
                    <TableCell>{entry.created_at ?? "-"}</TableCell>
                  </TableRow>
                ))}
              </TableBody>
            </Table>
          )}
          {history && hasHistory ? (
            <TablePagination
              page={history.current_page}
              totalPages={history.last_page}
              total={history.total}
              onPrev={() => setFilters((prev) => ({ ...prev, page: prev.page - 1 }))}
              onNext={() => setFilters((prev) => ({ ...prev, page: prev.page + 1 }))}
            />
          ) : null}
        </CardContent>
      </Card>
    </div>
  )
}
