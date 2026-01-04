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
import { Alert, AlertDescription, AlertTitle } from "../../components/ui/alert"
import { Skeleton } from "../../components/ui/skeleton"
import { EmptyState } from "../../components/ui/empty-state"
import { apiFetch, ApiError } from "../../lib/api"
import { buildQuery } from "../../lib/query"
import { useToast } from "../../components/ui/toast"

type WalletBalance = {
  balance: number
  formatted?: string
}

type WalletTransaction = {
  id: number
  amount: number
  title?: string
  type?: string
  description?: string
  created_at?: string
}

type Paginator<T> = {
  data: T[]
  current_page: number
  last_page: number
  total: number
}

export function CustomerWalletPage() {
  const { push } = useToast()
  const [balance, setBalance] = React.useState<WalletBalance | null>(null)
  const [transactions, setTransactions] = React.useState<Paginator<WalletTransaction> | null>(null)
  const [loading, setLoading] = React.useState(false)
  const [error, setError] = React.useState<string | null>(null)
  const [fieldErrors, setFieldErrors] = React.useState<Record<string, string[]>>({})
  const [topupAmount, setTopupAmount] = React.useState("")
  const [filters, setFilters] = React.useState({ type: "", page: 1 })

  const loadTransactions = React.useCallback(() => {
    setLoading(true)
    setError(null)

    Promise.all([
      apiFetch("/wallet/balance"),
      apiFetch(`/wallet/transactions${buildQuery({ type: filters.type, page: filters.page })}`),
    ])
      .then(([balancePayload, transactionPayload]) => {
        setBalance((balancePayload as { data?: WalletBalance }).data ?? null)
        setTransactions((transactionPayload as { data?: Paginator<WalletTransaction> }).data ?? null)
      })
      .catch((err) => {
        const message = err instanceof ApiError ? err.message : "Failed to load wallet"
        setError(message)
      })
      .finally(() => setLoading(false))
  }, [filters])

  React.useEffect(() => {
    loadTransactions()
  }, [loadTransactions])

  async function handleTopup() {
    setFieldErrors({})
    try {
      await apiFetch("/wallet/topup", {
        method: "POST",
        body: {
          amount: Number(topupAmount),
        },
      })
      push({ title: "Topup successful", variant: "success" })
      setTopupAmount("")
      loadTransactions()
    } catch (err) {
      if (err instanceof ApiError) {
        push({ title: "Topup error", description: err.message, variant: "error" })
        if (err.errors && typeof err.errors === "object" && !Array.isArray(err.errors)) {
          setFieldErrors(err.errors as Record<string, string[]>)
        }
      } else {
        push({ title: "Topup error", description: "Topup failed", variant: "error" })
      }
    }
  }

  const hasTransactions = (transactions?.data?.length ?? 0) > 0

  return (
    <div className="space-y-6">
      <Card>
        <CardContent className="flex flex-wrap items-center justify-between gap-4">
          <div>
            <div className="text-xs uppercase tracking-[0.2em] text-slate-400">Current balance</div>
            <div className="mt-2 text-2xl font-semibold text-ink">{balance?.formatted ?? balance?.balance ?? 0}</div>
          </div>
          <div className="flex flex-wrap gap-3">
            <Input
              type="number"
              min={1000}
              placeholder="Topup amount"
              value={topupAmount}
              onChange={(event) => setTopupAmount(event.target.value)}
              className="w-40"
            />
            {fieldErrors.amount ? (
              <p className="text-xs text-rose-500">{fieldErrors.amount.join(", ")}</p>
            ) : null}
            <Button onClick={handleTopup} disabled={!topupAmount}>
              Top up
            </Button>
          </div>
        </CardContent>
      </Card>

      {error ? (
        <Alert>
          <AlertTitle>Wallet error</AlertTitle>
          <AlertDescription>{error}</AlertDescription>
        </Alert>
      ) : null}

      {loading ? (
        <Card>
          <CardContent className="space-y-4">
            <Skeleton className="h-5 w-32" />
            <Skeleton className="h-20 w-full" />
          </CardContent>
        </Card>
      ) : null}

      {!loading ? (
        <Card>
          <CardContent className="space-y-4">
            <TableToolbar
              title="Transactions"
              description="Sorted by newest"
              actions={
                <select
                  className="rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm"
                  value={filters.type}
                  onChange={(event) => setFilters((prev) => ({ ...prev, type: event.target.value, page: 1 }))}
                >
                  <option value="">All types</option>
                  <option value="topup">Topup</option>
                  <option value="payment">Payment</option>
                </select>
              }
            />

            {!hasTransactions ? (
              <EmptyState title="No transactions" description="Your wallet history will show here." />
            ) : (
              <Table>
                <TableHeader>
                  <TableRow>
                    <TableHead>Title</TableHead>
                    <TableHead>Type</TableHead>
                    <TableHead>Amount</TableHead>
                    <TableHead>Date</TableHead>
                  </TableRow>
                </TableHeader>
                <TableBody>
                  {transactions?.data?.map((tx) => (
                    <TableRow key={tx.id}>
                      <TableCell>{tx.title ?? tx.description ?? "-"}</TableCell>
                      <TableCell>{tx.type ?? "-"}</TableCell>
                      <TableCell>{tx.amount}</TableCell>
                      <TableCell>{tx.created_at ?? "-"}</TableCell>
                    </TableRow>
                  ))}
                </TableBody>
              </Table>
            )}

            {transactions && hasTransactions ? (
              <TablePagination
                page={transactions.current_page}
                totalPages={transactions.last_page}
                total={transactions.total}
                onPrev={() => setFilters((prev) => ({ ...prev, page: prev.page - 1 }))}
                onNext={() => setFilters((prev) => ({ ...prev, page: prev.page + 1 }))}
              />
            ) : null}
          </CardContent>
        </Card>
      ) : null}
    </div>
  )
}
