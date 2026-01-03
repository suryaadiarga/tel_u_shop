import * as React from "react"
import { Link } from "react-router-dom"
import { Card, CardContent } from "../../components/ui/card"
import { Input } from "../../components/ui/input"
import { Button } from "../../components/ui/button"
import { Badge } from "../../components/ui/badge"
import { Alert, AlertDescription, AlertTitle } from "../../components/ui/alert"
import { Skeleton } from "../../components/ui/skeleton"
import { EmptyState } from "../../components/ui/empty-state"
import { apiFetch, ApiError } from "../../lib/api"
import { buildQuery } from "../../lib/query"
import { resolveStorageUrl } from "../../lib/storage"
import { useToast } from "../../components/ui/toast"

type Product = {
  id: number
  name: string
  description: string
  price: number
  formatted_price?: string
  stock: number
  stock_status?: string
  prep_time?: number
  image_url?: string | null
  category?: string | null
  merchant?: { id: number; name: string } | null
}

type Paginator<T> = {
  data: T[]
  current_page: number
  last_page: number
  per_page: number
  total: number
}

export function CustomerCatalogPage() {
  const { push } = useToast()
  const [products, setProducts] = React.useState<Paginator<Product> | null>(null)
  const [categories, setCategories] = React.useState<string[]>([])
  const [loading, setLoading] = React.useState(false)
  const [error, setError] = React.useState<string | null>(null)
  const [filters, setFilters] = React.useState({ search: "", category: "", page: 1 })

  React.useEffect(() => {
    apiFetch("/products/categories")
      .then((payload) => setCategories((payload as { data?: string[] }).data ?? []))
      .catch(() => setCategories([]))
  }, [])

  React.useEffect(() => {
    setLoading(true)
    setError(null)

    apiFetch(`/products${buildQuery({
      search: filters.search,
      category: filters.category,
      page: filters.page,
    })}`)
      .then((payload) => setProducts((payload as { data?: Paginator<Product> }).data ?? null))
      .catch((err) => {
        const message = err instanceof ApiError ? err.message : "Failed to load products"
        setError(message)
      })
      .finally(() => setLoading(false))
  }, [filters])

  async function handleWishlist(productId: number) {
    try {
      await apiFetch(`/wishlist/add/${productId}`, { method: "POST" })
      push({ title: "Added to wishlist", variant: "success" })
    } catch (err) {
      const message = err instanceof ApiError ? err.message : "Failed to add to wishlist"
      push({ title: "Wishlist error", description: message, variant: "error" })
    }
  }

  const hasResults = (products?.data?.length ?? 0) > 0

  return (
    <div className="space-y-6">
      <div className="flex flex-wrap gap-4">
        <div className="min-w-[220px] flex-1">
          <Input
            placeholder="Search products"
            value={filters.search}
            onChange={(event) => setFilters((prev) => ({ ...prev, search: event.target.value, page: 1 }))}
          />
        </div>
        <select
          className="rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm"
          value={filters.category}
          onChange={(event) => setFilters((prev) => ({ ...prev, category: event.target.value, page: 1 }))}
        >
          <option value="">All categories</option>
          {categories.map((category) => (
            <option key={category} value={category}>
              {category}
            </option>
          ))}
        </select>
      </div>

      {error ? (
        <Alert>
          <AlertTitle>Catalog error</AlertTitle>
          <AlertDescription>{error}</AlertDescription>
        </Alert>
      ) : null}

      {loading ? (
        <div className="grid gap-6 lg:grid-cols-3">
          {Array.from({ length: 6 }).map((_, index) => (
            <Card key={`skeleton-${index}`}>
              <Skeleton className="h-40 w-full" />
              <CardContent className="space-y-3">
                <Skeleton className="h-4 w-3/4" />
                <Skeleton className="h-3 w-full" />
                <Skeleton className="h-3 w-1/2" />
              </CardContent>
            </Card>
          ))}
        </div>
      ) : null}

      {!loading && !hasResults ? (
        <EmptyState
          title="No products found"
          description="Try adjusting your filters or search keywords."
          action={
            <Button variant="outline" onClick={() => setFilters({ search: "", category: "", page: 1 })}>
              Clear filters
            </Button>
          }
        />
      ) : null}

      {!loading && hasResults ? (
        <div className="grid gap-6 lg:grid-cols-3">
          {products?.data.map((product) => (
            <Card key={product.id} className="overflow-hidden">
              {product.image_url ? (
                <img
                  src={resolveStorageUrl(product.image_url) ?? ""}
                  alt={product.name}
                  className="h-40 w-full object-cover"
                />
              ) : (
                <div className="h-40 w-full bg-slate-100" />
              )}
              <CardContent className="space-y-3">
                <div className="flex items-start justify-between gap-3">
                  <div>
                    <div className="font-semibold text-ink">{product.name}</div>
                    <div className="text-xs text-slate-500">{product.merchant?.name ?? ""}</div>
                  </div>
                  {product.category ? <Badge variant="outline">{product.category}</Badge> : null}
                </div>
                <div className="text-sm text-slate-600">{product.description}</div>
                <div className="flex items-center justify-between">
                  <div className="text-sm font-semibold text-ink">{product.formatted_price ?? product.price}</div>
                  <div className="text-xs text-slate-500">{product.stock_status ?? ""}</div>
                </div>
                <div className="flex flex-wrap gap-2">
                  <Button variant="outline" asChild>
                    <Link to={`/app/catalog/${product.id}`}>View</Link>
                  </Button>
                  <Button variant="ghost" onClick={() => handleWishlist(product.id)}>
                    Add to wishlist
                  </Button>
                </div>
              </CardContent>
            </Card>
          ))}
        </div>
      ) : null}

      {products && hasResults ? (
        <div className="flex items-center justify-between text-sm text-slate-500">
          <div>
            Page {products.current_page} of {products.last_page} ({products.total} items)
          </div>
          <div className="flex gap-2">
            <Button
              variant="outline"
              disabled={products.current_page <= 1}
              onClick={() => setFilters((prev) => ({ ...prev, page: prev.page - 1 }))}
            >
              Previous
            </Button>
            <Button
              variant="outline"
              disabled={products.current_page >= products.last_page}
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
