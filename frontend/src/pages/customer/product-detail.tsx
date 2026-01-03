import * as React from "react"
import { Link, useParams } from "react-router-dom"
import { Card, CardContent } from "../../components/ui/card"
import { Button } from "../../components/ui/button"
import { Input } from "../../components/ui/input"
import { Badge } from "../../components/ui/badge"
import { Alert, AlertDescription, AlertTitle } from "../../components/ui/alert"
import { Skeleton } from "../../components/ui/skeleton"
import { apiFetch, ApiError } from "../../lib/api"
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
  created_at?: string
}

type Review = {
  id: number
  rating: number
  comment?: string | null
  user?: { id: number; name: string }
  created_at?: string
}

type Paginator<T> = {
  data: T[]
  current_page: number
  last_page: number
}

export function CustomerProductDetailPage() {
  const { id } = useParams()
  const { push } = useToast()
  const [product, setProduct] = React.useState<Product | null>(null)
  const [reviews, setReviews] = React.useState<Paginator<Review> | null>(null)
  const [loading, setLoading] = React.useState(true)
  const [error, setError] = React.useState<string | null>(null)
  const [qty, setQty] = React.useState(1)
  const [reviewForm, setReviewForm] = React.useState({ rating: "5", comment: "" })

  React.useEffect(() => {
    if (!id) return
    setLoading(true)
    setError(null)

    Promise.all([
      apiFetch(`/products/${id}`),
      apiFetch(`/products/${id}/reviews`),
    ])
      .then(([productPayload, reviewsPayload]) => {
        setProduct((productPayload as { data?: Product }).data ?? null)
        setReviews((reviewsPayload as { data?: Paginator<Review> }).data ?? null)
      })
      .catch((err) => {
        const message = err instanceof ApiError ? err.message : "Failed to load product"
        setError(message)
      })
      .finally(() => setLoading(false))
  }, [id])

  async function handleAddToCart() {
    if (!id) return
    try {
      await apiFetch(`/cart/add/${id}`, { method: "POST", body: { qty } })
      push({ title: "Added to cart", variant: "success" })
    } catch (err) {
      const message = err instanceof ApiError ? err.message : "Failed to add to cart"
      push({ title: "Cart error", description: message, variant: "error" })
    }
  }

  async function handleReviewSubmit(event: React.FormEvent) {
    event.preventDefault()
    if (!id) return
    try {
      await apiFetch(`/products/${id}/reviews`, {
        method: "POST",
        body: {
          rating: Number(reviewForm.rating),
          comment: reviewForm.comment || null,
        },
      })
      push({ title: "Review submitted", variant: "success" })
      const updated = await apiFetch(`/products/${id}/reviews`)
      setReviews((updated as { data?: Paginator<Review> }).data ?? null)
      setReviewForm({ rating: "5", comment: "" })
    } catch (err) {
      const message = err instanceof ApiError ? err.message : "Failed to submit review"
      push({ title: "Review error", description: message, variant: "error" })
    }
  }

  if (loading) {
    return (
      <div className="space-y-6">
        <Card>
          <CardContent className="grid gap-6 lg:grid-cols-[1fr_1.2fr]">
            <Skeleton className="h-64 w-full" />
            <div className="space-y-4">
              <Skeleton className="h-6 w-2/3" />
              <Skeleton className="h-4 w-full" />
              <Skeleton className="h-4 w-1/2" />
            </div>
          </CardContent>
        </Card>
        <Card>
          <CardContent className="space-y-4">
            <Skeleton className="h-5 w-40" />
            <Skeleton className="h-10 w-full" />
            <Skeleton className="h-16 w-full" />
          </CardContent>
        </Card>
      </div>
    )
  }

  if (error || !product) {
    return (
      <div className="space-y-4">
        <Alert>
          <AlertTitle>Product unavailable</AlertTitle>
          <AlertDescription>{error ?? "Product not found"}</AlertDescription>
        </Alert>
        <Button asChild>
          <Link to="/app/catalog">Back to catalog</Link>
        </Button>
      </div>
    )
  }

  return (
    <div className="space-y-6">
      <Card>
        <CardContent className="grid gap-6 lg:grid-cols-[1fr_1.2fr]">
          {product.image_url ? (
            <img
              src={resolveStorageUrl(product.image_url) ?? ""}
              alt={product.name}
              className="h-64 w-full rounded-xl object-cover"
            />
          ) : (
            <div className="h-64 w-full rounded-xl bg-slate-100" />
          )}
          <div className="space-y-4">
            <div>
              <div className="font-display text-2xl font-semibold text-ink">{product.name}</div>
              <div className="text-sm text-slate-500">{product.merchant?.name ?? ""}</div>
            </div>
            <p className="text-sm text-slate-600">{product.description}</p>
            <div className="flex flex-wrap gap-2">
              {product.category ? <Badge variant="outline">{product.category}</Badge> : null}
              {product.stock_status ? <Badge variant="soft">{product.stock_status}</Badge> : null}
              {product.prep_time ? <Badge variant="outline">{product.prep_time} min</Badge> : null}
            </div>
            <div className="text-lg font-semibold text-ink">{product.formatted_price ?? product.price}</div>
            <div className="flex flex-wrap items-center gap-3">
              <Input
                type="number"
                min={1}
                value={qty}
                onChange={(event) => setQty(Number(event.target.value))}
                className="w-24"
              />
              <Button onClick={handleAddToCart}>Add to cart</Button>
            </div>
          </div>
        </CardContent>
      </Card>

      <Card>
        <CardContent className="space-y-4">
          <div className="font-display text-lg font-semibold text-ink">Reviews</div>
          <form className="grid gap-3 sm:grid-cols-[120px_1fr_auto]" onSubmit={handleReviewSubmit}>
            <select
              className="rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm"
              value={reviewForm.rating}
              onChange={(event) => setReviewForm((prev) => ({ ...prev, rating: event.target.value }))}
            >
              {[5, 4, 3, 2, 1].map((rating) => (
                <option key={rating} value={rating}>
                  {rating} stars
                </option>
              ))}
            </select>
            <Input
              placeholder="Share your thoughts"
              value={reviewForm.comment}
              onChange={(event) => setReviewForm((prev) => ({ ...prev, comment: event.target.value }))}
            />
            <Button type="submit" variant="secondary">
              Submit
            </Button>
          </form>

          <div className="space-y-3">
            {reviews?.data.length ? (
              reviews.data.map((review) => (
                <div key={review.id} className="rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm">
                  <div className="flex items-center justify-between">
                    <div className="font-semibold text-ink">{review.user?.name ?? "Customer"}</div>
                    <Badge variant="soft">{review.rating} / 5</Badge>
                  </div>
                  {review.comment ? <p className="mt-2 text-slate-600">{review.comment}</p> : null}
                </div>
              ))
            ) : (
              <div className="text-sm text-slate-500">No reviews yet.</div>
            )}
          </div>
        </CardContent>
      </Card>
    </div>
  )
}
