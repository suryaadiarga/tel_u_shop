import * as React from "react"
import { Link } from "react-router-dom"
import { Card, CardContent } from "../../components/ui/card"
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow, TableToolbar } from "../../components/ui/table"
import { Button } from "../../components/ui/button"
import { Input } from "../../components/ui/input"
import { Alert, AlertDescription, AlertTitle } from "../../components/ui/alert"
import { Skeleton } from "../../components/ui/skeleton"
import { EmptyState } from "../../components/ui/empty-state"
import { apiFetch, ApiError } from "../../lib/api"
import { useToast } from "../../components/ui/toast"

type CartItem = {
  id: number
  product_id: number
  qty: number
  price_snapshot: number
  subtotal: number
  product: {
    id: number
    name: string
  }
}

type CartData = {
  items: CartItem[]
  total: number
}

export function CustomerCartPage() {
  const { push } = useToast()
  const [cart, setCart] = React.useState<CartData | null>(null)
  const [draftQty, setDraftQty] = React.useState<Record<number, number>>({})
  const [loading, setLoading] = React.useState(true)
  const [error, setError] = React.useState<string | null>(null)
  const [notes, setNotes] = React.useState("")
  const [paymentMethod, setPaymentMethod] = React.useState("wallet")

  const loadCart = React.useCallback(() => {
    setLoading(true)
    setError(null)

    apiFetch("/cart")
      .then((payload) => {
        const data = (payload as { data?: CartData }).data ?? null
        setCart(data)
        if (data?.items?.length) {
          const nextDrafts: Record<number, number> = {}
          data.items.forEach((item) => {
            nextDrafts[item.id] = item.qty
          })
          setDraftQty(nextDrafts)
        }
      })
      .catch((err) => {
        const message = err instanceof ApiError ? err.message : "Failed to load cart"
        setError(message)
      })
      .finally(() => setLoading(false))
  }, [])

  React.useEffect(() => {
    loadCart()
  }, [loadCart])

  async function handleUpdateQty(item: CartItem) {
    const qty = draftQty[item.id]
    if (!qty || qty < 1) return

    try {
      await apiFetch(`/cart/update/${item.id}`, {
        method: "PUT",
        body: { qty },
      })
      loadCart()
    } catch (err) {
      const message = err instanceof ApiError ? err.message : "Failed to update cart"
      push({ title: "Cart error", description: message, variant: "error" })
    }
  }

  async function handleRemove(item: CartItem) {
    try {
      await apiFetch(`/cart/remove/${item.id}`, { method: "DELETE" })
      loadCart()
    } catch (err) {
      const message = err instanceof ApiError ? err.message : "Failed to remove item"
      push({ title: "Cart error", description: message, variant: "error" })
    }
  }

  async function handleClear() {
    try {
      await apiFetch("/cart/clear", { method: "DELETE" })
      loadCart()
    } catch (err) {
      const message = err instanceof ApiError ? err.message : "Failed to clear cart"
      push({ title: "Cart error", description: message, variant: "error" })
    }
  }

  async function handleCheckout() {
    try {
      await apiFetch("/checkout", {
        method: "POST",
        body: {
          payment_method: paymentMethod,
          notes: notes || null,
        },
      })
      push({ title: "Checkout success", variant: "success" })
      setNotes("")
      loadCart()
    } catch (err) {
      const message = err instanceof ApiError ? err.message : "Checkout failed"
      push({ title: "Checkout error", description: message, variant: "error" })
    }
  }

  if (loading) {
    return (
      <div className="space-y-6">
        <Card>
          <CardContent className="space-y-4">
            <Skeleton className="h-6 w-40" />
            <Skeleton className="h-24 w-full" />
          </CardContent>
        </Card>
        <Card>
          <CardContent className="space-y-4">
            <Skeleton className="h-6 w-32" />
            <Skeleton className="h-12 w-full" />
          </CardContent>
        </Card>
      </div>
    )
  }

  return (
    <div className="space-y-6">
      {error ? (
        <Alert>
          <AlertTitle>Cart error</AlertTitle>
          <AlertDescription>{error}</AlertDescription>
        </Alert>
      ) : null}
      <Card>
        <CardContent className="space-y-4">
          <TableToolbar
            title="Cart items"
            description="Review items before checkout"
            actions={
              <Button variant="outline" onClick={handleClear}>
                Clear cart
              </Button>
            }
          />
          {cart?.items?.length ? (
            <Table>
              <TableHeader>
                <TableRow>
                  <TableHead>Product</TableHead>
                  <TableHead>Qty</TableHead>
                  <TableHead>Subtotal</TableHead>
                  <TableHead />
                </TableRow>
              </TableHeader>
              <TableBody>
                {cart.items.map((item) => (
                  <TableRow key={item.id}>
                    <TableCell>{item.product?.name ?? ""}</TableCell>
                    <TableCell className="w-48">
                      <div className="flex items-center gap-2">
                        <Input
                          type="number"
                          min={1}
                          value={draftQty[item.id] ?? item.qty}
                          onChange={(event) =>
                            setDraftQty((prev) => ({ ...prev, [item.id]: Number(event.target.value) }))
                          }
                        />
                        <Button variant="outline" onClick={() => handleUpdateQty(item)}>
                          Update
                        </Button>
                      </div>
                    </TableCell>
                    <TableCell>{item.subtotal}</TableCell>
                    <TableCell className="text-right">
                      <Button variant="ghost" onClick={() => handleRemove(item)}>
                        Remove
                      </Button>
                    </TableCell>
                  </TableRow>
                ))}
              </TableBody>
            </Table>
          ) : (
            <EmptyState
              title="Your cart is empty"
              description="Browse the catalog and add items before checkout."
              action={
                <Button asChild>
                  <Link to="/app/catalog">Browse catalog</Link>
                </Button>
              }
            />
          )}
          <div className="flex items-center justify-between text-sm font-semibold">
            <span>Total</span>
            <span>{cart?.total ?? 0}</span>
          </div>
        </CardContent>
      </Card>

      <Card>
        <CardContent className="space-y-4">
          <div className="font-display text-lg font-semibold text-ink">Checkout</div>
          <div className="grid gap-4 md:grid-cols-[1fr_200px]">
            <Input
              placeholder="Notes (optional)"
              value={notes}
              onChange={(event) => setNotes(event.target.value)}
            />
            <select
              className="rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm"
              value={paymentMethod}
              onChange={(event) => setPaymentMethod(event.target.value)}
            >
              <option value="wallet">Wallet</option>
              <option value="cash">Cash</option>
            </select>
          </div>
          <Button onClick={handleCheckout} disabled={!cart?.items?.length}>
            Place order
          </Button>
        </CardContent>
      </Card>
    </div>
  )
}
