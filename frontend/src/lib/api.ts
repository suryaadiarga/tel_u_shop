import { getToken, setToken } from "./auth"

const API_URL = import.meta.env.VITE_API_URL as string

export type ApiEnvelope<T = unknown> = {
  status: "success" | "error"
  message?: string
  data?: T
  errors?: Record<string, string[]> | string | null
  [key: string]: unknown
}

export class ApiError extends Error {
  statusCode: number
  errors?: Record<string, string[]> | string | null

  constructor(message: string, statusCode: number, errors?: Record<string, string[]> | string | null) {
    super(message)
    this.name = "ApiError"
    this.statusCode = statusCode
    this.errors = errors
  }
}

export async function apiFetch<T = ApiEnvelope>(
  path: string,
  options: Omit<RequestInit, "body"> & { body?: unknown } = {}
) {
  const token = getToken()
  const headers = new Headers(options.headers)
  headers.set("Accept", "application/json")

  let body = options.body as BodyInit | null | undefined
  if (options.body && !(options.body instanceof FormData)) {
    headers.set("Content-Type", "application/json")
    body = JSON.stringify(options.body)
  }

  if (token) {
    headers.set("Authorization", `Bearer ${token}`)
  }

  const response = await fetch(`${API_URL}${path}`, {
    ...options,
    headers,
    body,
  })

  const payload = await response.json().catch(() => null)

  if (!response.ok || (payload && payload.status === "error")) {
    const message = payload?.message || response.statusText || "Request failed"
    const errors = payload?.errors
    if (response.status === 401) {
      setToken(null)
      if (typeof window !== "undefined" && !window.location.pathname.startsWith("/login")) {
        window.location.assign("/login")
      }
    }
    throw new ApiError(message, response.status, errors)
  }

  return payload as T
}

export type UnwrapListResult<T> = {
  items: T[]
  meta?: unknown
  links?: unknown
}

function isRecord(value: unknown): value is Record<string, unknown> {
  return typeof value === "object" && value !== null && !Array.isArray(value)
}

function extractListFromRecord<T>(record: Record<string, unknown>): UnwrapListResult<T> | null {
  const list = Array.isArray(record.items) ? record.items : Array.isArray(record.data) ? record.data : null
  if (!list) return null

  const rest: Record<string, unknown> = { ...record }
  delete rest.items
  delete rest.data

  const meta = isRecord(record.meta) ? record.meta : Object.keys(rest).length ? rest : undefined
  const links = record.links

  return {
    items: list as T[],
    meta,
    links,
  }
}

function unwrapListDepth<T>(payload: unknown, depth: number): UnwrapListResult<T> | null {
  if (Array.isArray(payload)) {
    return { items: payload as T[] }
  }

  if (!isRecord(payload)) return null

  const direct = extractListFromRecord<T>(payload)
  if (direct) return direct

  if (depth >= 3 || !("data" in payload)) {
    return null
  }

  return unwrapListDepth<T>(payload.data, depth + 1)
}

export function unwrapList<T>(payload: unknown): UnwrapListResult<T> {
  const result = unwrapListDepth<T>(payload, 0)
  return result ?? { items: [] }
}
