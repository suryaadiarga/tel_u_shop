import * as React from "react"
import { Card, CardContent } from "../../components/ui/card"
import { Button } from "../../components/ui/button"
import { Input } from "../../components/ui/input"
import { Badge } from "../../components/ui/badge"
import { Alert, AlertDescription, AlertTitle } from "../../components/ui/alert"
import { Skeleton } from "../../components/ui/skeleton"
import { EmptyState } from "../../components/ui/empty-state"
import { apiFetch, ApiError } from "../../lib/api"
import { buildQuery } from "../../lib/query"
import { resolveStorageUrl } from "../../lib/storage"
import { getCategories } from "../../lib/categories"
import { useToast } from "../../components/ui/toast"

type Product = {
  id: number
  name: string
  description: string
  price: number
  stock: number
  category?: string
  prep_time?: number
  is_available?: boolean
  image_url?: string | null
}

type Paginator<T> = {
  data: T[]
  current_page: number
  last_page: number
}

const initialCreate = {
  name: "",
  description: "",
  price: "",
  stock: "",
  category: "",
  prep_time: "",
  is_available: true,
}

export function MerchantProductsPage() {
  const { push } = useToast()
  const [products, setProducts] = React.useState<Paginator<Product> | null>(null)
  const [filters, setFilters] = React.useState({ search: "", category: "", is_available: "" })
  const [categories, setCategories] = React.useState<string[]>([])
  const [categoriesLoading, setCategoriesLoading] = React.useState(false)
  const [categoriesError, setCategoriesError] = React.useState<string | null>(null)
  const [createForm, setCreateForm] = React.useState(initialCreate)
  const [createImage, setCreateImage] = React.useState<File | null>(null)
  const [selected, setSelected] = React.useState<Product | null>(null)
  const [editForm, setEditForm] = React.useState({
    name: "",
    description: "",
    price: "",
    stock: "",
    category: "",
    is_available: true,
  })
  const [editImage, setEditImage] = React.useState<File | null>(null)
  const [loading, setLoading] = React.useState(false)
  const [error, setError] = React.useState<string | null>(null)
  const [createErrors, setCreateErrors] = React.useState<Record<string, string[]>>({})
  const [editErrors, setEditErrors] = React.useState<Record<string, string[]>>({})

  React.useEffect(() => {
    setCategoriesLoading(true)
    setCategoriesError(null)

    getCategories()
      .then((data) => setCategories(data))
      .catch((err) => {
        const message = err instanceof ApiError ? err.message : "Failed to load categories"
        setCategoriesError(message)
        setCategories([])
      })
      .finally(() => setCategoriesLoading(false))
  }, [])

  const loadProducts = React.useCallback(() => {
    setLoading(true)
    setError(null)

    apiFetch(`/merchant/products${buildQuery(filters)}`)
      .then((payload) => setProducts((payload as { data?: Paginator<Product> }).data ?? null))
      .catch((err) => {
        const message = err instanceof ApiError ? err.message : "Failed to load products"
        setError(message)
      })
      .finally(() => setLoading(false))
  }, [filters])

  React.useEffect(() => {
    loadProducts()
  }, [loadProducts])

  React.useEffect(() => {
    if (selected) {
      setEditForm({
        name: selected.name,
        description: selected.description,
        price: String(selected.price),
        stock: String(selected.stock),
        category: selected.category ?? "",
        is_available: selected.is_available ?? true,
      })
    }
  }, [selected])

  async function handleCreate(event: React.FormEvent) {
    event.preventDefault()
    setCreateErrors({})

    const payload = new FormData()
    payload.append("name", createForm.name)
    payload.append("description", createForm.description)
    payload.append("price", createForm.price)
    payload.append("stock", createForm.stock)
    payload.append("category", createForm.category)
    payload.append("prep_time", createForm.prep_time)
    payload.append("is_available", createForm.is_available ? "1" : "0")
    if (createImage) payload.append("image", createImage)

    try {
      await apiFetch("/merchant/products", {
        method: "POST",
        body: payload,
      })
      push({ title: "Product created", variant: "success" })
      setCreateForm(initialCreate)
      setCreateImage(null)
      loadProducts()
    } catch (err) {
      if (err instanceof ApiError) {
        push({ title: "Create product error", description: err.message, variant: "error" })
        if (err.errors && typeof err.errors === "object" && !Array.isArray(err.errors)) {
          setCreateErrors(err.errors as Record<string, string[]>)
        }
      } else {
        push({ title: "Create product error", description: "Failed to create product", variant: "error" })
      }
    }
  }

  async function handleUpdate(event: React.FormEvent) {
    event.preventDefault()
    if (!selected) return
    setEditErrors({})

    const payload = new FormData()
    if (editForm.name) payload.append("name", editForm.name)
    if (editForm.description) payload.append("description", editForm.description)
    if (editForm.price) payload.append("price", editForm.price)
    if (editForm.stock) payload.append("stock", editForm.stock)
    if (editForm.category) payload.append("category", editForm.category)
    payload.append("is_available", editForm.is_available ? "1" : "0")
    if (editImage) payload.append("image", editImage)

    try {
      await apiFetch(`/merchant/products/${selected.id}`, {
        method: "PUT",
        body: payload,
      })
      push({ title: "Product updated", variant: "success" })
      setEditImage(null)
      loadProducts()
    } catch (err) {
      if (err instanceof ApiError) {
        push({ title: "Update product error", description: err.message, variant: "error" })
        if (err.errors && typeof err.errors === "object" && !Array.isArray(err.errors)) {
          setEditErrors(err.errors as Record<string, string[]>)
        }
      } else {
        push({ title: "Update product error", description: "Failed to update product", variant: "error" })
      }
    }
  }

  async function handleDelete(product: Product) {
    try {
      await apiFetch(`/merchant/products/${product.id}`, { method: "DELETE" })
      push({ title: "Product deleted", variant: "success" })
      if (selected?.id === product.id) setSelected(null)
      loadProducts()
    } catch (err) {
      const message = err instanceof ApiError ? err.message : "Failed to delete product"
      push({ title: "Delete product error", description: message, variant: "error" })
    }
  }

  const hasProducts = (products?.data?.length ?? 0) > 0

  return (
    <div className="space-y-6">
      <Card>
        <CardContent className="space-y-4">
          <div className="font-display text-lg font-semibold text-ink">Create product</div>
          <form className="grid gap-4 md:grid-cols-2" onSubmit={handleCreate}>
            {[
              { label: "Name", name: "name" },
              { label: "Price", name: "price", type: "number" },
              { label: "Stock", name: "stock", type: "number" },
              { label: "Prep time (min)", name: "prep_time", type: "number" },
            ].map((field) => (
              <div key={field.name} className="space-y-2">
                <label className="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">
                  {field.label}
                </label>
                <Input
                  type={field.type ?? "text"}
                  value={String(createForm[field.name as keyof typeof createForm])}
                  onChange={(event) =>
                    setCreateForm((prev) => ({ ...prev, [field.name]: event.target.value }))
                  }
                  required
                />
                {createErrors[field.name] ? (
                  <p className="text-xs text-rose-500">{createErrors[field.name].join(", ")}</p>
                ) : null}
              </div>
            ))}
            <div className="space-y-2">
              <label className="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Category</label>
              <select
                className="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm"
                value={createForm.category}
                onChange={(event) => setCreateForm((prev) => ({ ...prev, category: event.target.value }))}
                disabled={categoriesLoading || categories.length === 0}
                required
              >
                <option value="">
                  {categoriesLoading ? "Loading categories..." : "Select category"}
                </option>
                {categories.map((category) => (
                  <option key={category} value={category}>
                    {category}
                  </option>
                ))}
              </select>
              {categoriesError ? <p className="text-xs text-rose-500">{categoriesError}</p> : null}
              {!categoriesLoading && !categoriesError && categories.length === 0 ? (
                <p className="text-xs text-rose-500">No categories available.</p>
              ) : null}
              {createErrors.category ? (
                <p className="text-xs text-rose-500">{createErrors.category.join(", ")}</p>
              ) : null}
            </div>
            <div className="space-y-2 md:col-span-2">
              <label className="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Description</label>
              <textarea
                className="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm"
                rows={3}
                value={createForm.description}
                onChange={(event) => setCreateForm((prev) => ({ ...prev, description: event.target.value }))}
                required
              />
              {createErrors.description ? (
                <p className="text-xs text-rose-500">{createErrors.description.join(", ")}</p>
              ) : null}
            </div>
            <div className="space-y-2">
              <label className="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Availability</label>
              <select
                className="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm"
                value={createForm.is_available ? "true" : "false"}
                onChange={(event) =>
                  setCreateForm((prev) => ({ ...prev, is_available: event.target.value === "true" }))
                }
              >
                <option value="true">Available</option>
                <option value="false">Unavailable</option>
              </select>
              {createErrors.is_available ? (
                <p className="text-xs text-rose-500">{createErrors.is_available.join(", ")}</p>
              ) : null}
            </div>
            <div className="space-y-2">
              <label className="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Image</label>
              <Input type="file" accept="image/*" onChange={(event) => setCreateImage(event.target.files?.[0] ?? null)} />
              {createErrors.image ? (
                <p className="text-xs text-rose-500">{createErrors.image.join(", ")}</p>
              ) : null}
            </div>
            <div className="md:col-span-2">
              <Button type="submit">Create product</Button>
            </div>
          </form>
        </CardContent>
      </Card>

      {error ? (
        <Alert>
          <AlertTitle>Product list error</AlertTitle>
          <AlertDescription>{error}</AlertDescription>
        </Alert>
      ) : null}

      {loading ? (
        <div className="grid gap-4 md:grid-cols-2">
          {Array.from({ length: 4 }).map((_, index) => (
            <Card key={`product-skeleton-${index}`}>
              <CardContent className="space-y-3">
                <Skeleton className="h-16 w-full" />
                <Skeleton className="h-4 w-1/2" />
              </CardContent>
            </Card>
          ))}
        </div>
      ) : null}

      <Card>
        <CardContent className="space-y-4">
          <div className="flex flex-wrap items-center justify-between gap-4">
            <div className="font-display text-lg font-semibold text-ink">Products</div>
            <div className="flex flex-wrap gap-2">
              <Input
                placeholder="Search"
                value={filters.search}
                onChange={(event) => setFilters((prev) => ({ ...prev, search: event.target.value }))}
              />
              <Input
                placeholder="Category"
                value={filters.category}
                onChange={(event) => setFilters((prev) => ({ ...prev, category: event.target.value }))}
              />
              <select
                className="rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm"
                value={filters.is_available}
                onChange={(event) => setFilters((prev) => ({ ...prev, is_available: event.target.value }))}
              >
                <option value="">All</option>
                <option value="true">Available</option>
                <option value="false">Unavailable</option>
              </select>
            </div>
          </div>

          {!loading && !hasProducts ? (
            <EmptyState title="No products yet" description="Create a product to start selling." />
          ) : null}

          {!loading && hasProducts ? (
            <div className="grid gap-4 md:grid-cols-2">
              {products?.data.map((product) => (
                <div key={product.id} className="rounded-xl border border-slate-200 bg-white p-4">
                  <div className="flex items-center gap-3">
                    {product.image_url ? (
                      <img
                        src={resolveStorageUrl(product.image_url) ?? ""}
                        alt={product.name}
                        className="h-14 w-14 rounded-lg object-cover"
                      />
                    ) : (
                      <div className="h-14 w-14 rounded-lg bg-slate-100" />
                    )}
                    <div className="flex-1">
                      <div className="font-semibold text-ink">{product.name}</div>
                      <div className="text-xs text-slate-500">Stock: {product.stock}</div>
                    </div>
                    <Badge variant={product.is_available ? "soft" : "outline"}>
                      {product.is_available ? "Available" : "Unavailable"}
                    </Badge>
                  </div>
                  <div className="mt-3 flex flex-wrap gap-2">
                    <Button variant="outline" onClick={() => setSelected(product)}>
                      Edit
                    </Button>
                    <Button variant="ghost" onClick={() => handleDelete(product)}>
                      Delete
                    </Button>
                  </div>
                </div>
              ))}
            </div>
          ) : null}
        </CardContent>
      </Card>

      {selected ? (
        <Card>
          <CardContent className="space-y-4">
            <div className="font-display text-lg font-semibold text-ink">Edit product</div>
            <form className="grid gap-4 md:grid-cols-2" onSubmit={handleUpdate}>
              {[
                { label: "Name", name: "name" },
                { label: "Price", name: "price", type: "number" },
                { label: "Stock", name: "stock", type: "number" },
              ].map((field) => (
                <div key={field.name} className="space-y-2">
                  <label className="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">
                    {field.label}
                  </label>
                  <Input
                    type={field.type ?? "text"}
                    value={String(editForm[field.name as keyof typeof editForm])}
                    onChange={(event) =>
                      setEditForm((prev) => ({ ...prev, [field.name]: event.target.value }))
                    }
                  />
                  {editErrors[field.name] ? (
                    <p className="text-xs text-rose-500">{editErrors[field.name].join(", ")}</p>
                  ) : null}
                </div>
              ))}
              <div className="space-y-2">
                <label className="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Category</label>
                <select
                  className="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm"
                  value={editForm.category}
                  onChange={(event) => setEditForm((prev) => ({ ...prev, category: event.target.value }))}
                  disabled={categoriesLoading || categories.length === 0}
                >
                  <option value="">
                    {categoriesLoading ? "Loading categories..." : "Select category"}
                  </option>
                  {categories.map((category) => (
                    <option key={category} value={category}>
                      {category}
                    </option>
                  ))}
                </select>
                {categoriesError ? <p className="text-xs text-rose-500">{categoriesError}</p> : null}
                {editErrors.category ? (
                  <p className="text-xs text-rose-500">{editErrors.category.join(", ")}</p>
                ) : null}
              </div>
              <div className="space-y-2">
                <label className="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Availability</label>
                <select
                  className="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm"
                  value={editForm.is_available ? "true" : "false"}
                  onChange={(event) =>
                    setEditForm((prev) => ({ ...prev, is_available: event.target.value === "true" }))
                  }
                >
                  <option value="true">Available</option>
                  <option value="false">Unavailable</option>
                </select>
                {editErrors.is_available ? (
                  <p className="text-xs text-rose-500">{editErrors.is_available.join(", ")}</p>
                ) : null}
              </div>
              <div className="space-y-2 md:col-span-2">
                <label className="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Description</label>
                <textarea
                  className="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm"
                  rows={3}
                  value={editForm.description}
                  onChange={(event) => setEditForm((prev) => ({ ...prev, description: event.target.value }))}
                />
                {editErrors.description ? (
                  <p className="text-xs text-rose-500">{editErrors.description.join(", ")}</p>
                ) : null}
              </div>
              <div className="space-y-2">
                <label className="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Image</label>
                <Input type="file" accept="image/*" onChange={(event) => setEditImage(event.target.files?.[0] ?? null)} />
                {editErrors.image ? (
                  <p className="text-xs text-rose-500">{editErrors.image.join(", ")}</p>
                ) : null}
              </div>
              <div className="md:col-span-2 flex gap-2">
                <Button type="submit">Save changes</Button>
                <Button variant="ghost" onClick={() => setSelected(null)} type="button">
                  Cancel
                </Button>
              </div>
            </form>
          </CardContent>
        </Card>
      ) : null}
    </div>
  )
}
