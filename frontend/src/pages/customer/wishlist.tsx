import * as React from "react"
import { Link } from "react-router-dom"
import { Card, CardContent } from "../../components/ui/card"
import { Button } from "../../components/ui/button"
import { Input } from "../../components/ui/input"
import { Alert, AlertDescription, AlertTitle } from "../../components/ui/alert"
import { Skeleton } from "../../components/ui/skeleton"
import { EmptyState } from "../../components/ui/empty-state"
import { apiFetch, ApiError } from "../../lib/api"
import { buildQuery } from "../../lib/query"
import { resolveStorageUrl } from "../../lib/storage"
import { useToast } from "../../components/ui/toast"

type WishlistItem = {
  id: number
  product_id: number
  product?: {
    id: number
    name: string
    image_url?: string | null
    price?: number
  }
}

type Paginator<T> = {
  data: T[]
  current_page: number
  last_page: number
}

export function CustomerWishlistPage() {
  const { push } = useToast()
  const [wishlist, setWishlist] = React.useState<Paginator<WishlistItem> | null>(null)
  const [loading, setLoading] = React.useState(false)
  const [error, setError] = React.useState<string | null>(null)
  const [search, setSearch] = React.useState("")

  const loadWishlist = React.useCallback(() => {
    setLoading(true)
    setError(null)

    apiFetch(`/wishlist${buildQuery({ search })}`)
      .then((payload) => setWishlist((payload as { data?: Paginator<WishlistItem> }).data ?? null))
      .catch((err) => {
        const message = err instanceof ApiError ? err.message : "Failed to load wishlist"
        setError(message)
      })
      .finally(() => setLoading(false))
  }, [search])

  React.useEffect(() => {
    loadWishlist()
  }, [loadWishlist])

  async function handleRemove(productId: number) {
    try {
      await apiFetch(`/wishlist/remove/${productId}`, { method: "DELETE" })
      loadWishlist()
    } catch (err) {
      const message = err instanceof ApiError ? err.message : "Failed to remove"
      push({ title: "Wishlist error", description: message, variant: "error" })
    }
  }

  const hasItems = (wishlist?.data?.length ?? 0) > 0

  return (
    <div className="space-y-6">
      <Input
        placeholder="Search wishlist"
        value={search}
        onChange={(event) => setSearch(event.target.value)}
      />

      {error ? (
        <Alert>
          <AlertTitle>Wishlist error</AlertTitle>
          <AlertDescription>{error}</AlertDescription>
        </Alert>
      ) : null}

      {loading ? (
        <div className="grid gap-4 md:grid-cols-2">
          {Array.from({ length: 4 }).map((_, index) => (
            <Card key={`wishlist-skeleton-${index}`}>
              <CardContent className="space-y-3">
                <Skeleton className="h-16 w-full" />
                <Skeleton className="h-4 w-1/2" />
              </CardContent>
            </Card>
          ))}
        </div>
      ) : null}

      {!loading && !hasItems ? (
        <EmptyState title="Wishlist is empty" description="Save items from the catalog to see them here." />
      ) : null}

      {!loading && hasItems ? (
        <div className="grid gap-4 md:grid-cols-2">
          {wishlist?.data.map((item) => (
            <Card key={item.id}>
              <CardContent className="flex flex-wrap items-center justify-between gap-4">
                <div className="flex items-center gap-3">
                  {item.product?.image_url ? (
                    <img
                      src={resolveStorageUrl(item.product.image_url) ?? ""}
                      alt={item.product.name}
                      className="h-16 w-16 rounded-lg object-cover"
                    />
                  ) : (
                    <div className="h-16 w-16 rounded-lg bg-slate-100" />
                  )}
                  <div>
                    <div className="font-semibold text-ink">{item.product?.name ?? ""}</div>
                    <div className="text-xs text-slate-500">{item.product?.price ?? ""}</div>
                  </div>
                </div>
                <div className="flex gap-2">
                  <Button variant="outline" asChild>
                    <Link to={`/app/catalog/${item.product_id}`}>View</Link>
                  </Button>
                  <Button variant="ghost" onClick={() => handleRemove(item.product_id)}>
                    Remove
                  </Button>
                </div>
              </CardContent>
            </Card>
          ))}
        </div>
      ) : null}
    </div>
  )
}
