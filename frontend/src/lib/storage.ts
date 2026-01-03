const STORAGE_URL = import.meta.env.VITE_STORAGE_URL as string | undefined

export function resolveStorageUrl(path?: string | null) {
  if (!path) return null
  if (!STORAGE_URL) return path
  const trimmed = path.replace(/^\/+/, "")
  return `${STORAGE_URL}/${trimmed}`
}
