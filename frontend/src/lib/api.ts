import { getToken } from "./auth"

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
    throw new ApiError(message, response.status, errors)
  }

  return payload as T
}
