import { apiFetch } from "./api"

export async function getCategories(): Promise<string[]> {
  const payload = await apiFetch<{ data?: string[] }>("/products/categories")
  return payload.data ?? []
}
