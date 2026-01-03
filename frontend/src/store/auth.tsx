import * as React from "react"
import { apiFetch } from "../lib/api"
import type { ApiEnvelope } from "../lib/api"
import { getToken, setToken } from "../lib/auth"

export type RoleName = "admin" | "merchant" | "customer"

export type Role = {
  id: number
  name: string
  display_name?: string
}

export type User = {
  id: number
  name: string
  email: string
  role_id: number
  role?: Role
  username?: string
  nim?: string
  kelas?: string
  phone?: string
  avatar_url?: string | null
  merchant_status?: string | null
  is_banned?: boolean
}

type LoginPayload = {
  email: string
  password: string
}

type RegisterPayload = {
  name: string
  username: string
  email: string
  password: string
  password_confirmation: string
  nim: string
  kelas: string
  phone: string
  role: number
}

type AuthContextValue = {
  user: User | null
  token: string | null
  loading: boolean
  login: (payload: LoginPayload) => Promise<void>
  register: (payload: RegisterPayload) => Promise<void>
  logout: () => Promise<void>
  refresh: () => Promise<void>
}

const AuthContext = React.createContext<AuthContextValue | undefined>(undefined)

function resolveRoleName(user: User | null): RoleName | null {
  if (!user) return null
  if (user.role?.name) return user.role.name as RoleName
  if (user.role_id === 1) return "admin"
  if (user.role_id === 2) return "merchant"
  if (user.role_id === 3) return "customer"
  return null
}

export function AuthProvider({ children }: { children: React.ReactNode }) {
  const [user, setUser] = React.useState<User | null>(null)
  const [token, setTokenState] = React.useState<string | null>(() => getToken())
  const [loading, setLoading] = React.useState(true)

  const refresh = React.useCallback(async () => {
    if (!getToken()) {
      setUser(null)
      return
    }

    const response = await apiFetch<ApiEnvelope<User>>("/me")
    setUser(response.data ?? null)
  }, [])

  React.useEffect(() => {
    refresh()
      .catch(() => {
        setToken(null)
        setTokenState(null)
        setUser(null)
      })
      .finally(() => setLoading(false))
  }, [refresh])

  const login = React.useCallback(async (payload: LoginPayload) => {
    const response = await apiFetch<ApiEnvelope<{ access_token: string }>>("/login", {
      method: "POST",
      body: payload,
    })
    const accessToken = (response.data as { access_token?: string } | undefined)?.access_token
    if (accessToken) {
      setToken(accessToken)
      setTokenState(accessToken)
    }
    await refresh()
  }, [refresh])

  const register = React.useCallback(async (payload: RegisterPayload) => {
    const response = await apiFetch<ApiEnvelope<{ access_token: string }>>("/register", {
      method: "POST",
      body: payload,
    })
    const accessToken = (response.data as { access_token?: string } | undefined)?.access_token
    if (accessToken) {
      setToken(accessToken)
      setTokenState(accessToken)
    }
    await refresh()
  }, [refresh])

  const logout = React.useCallback(async () => {
    try {
      await apiFetch<ApiEnvelope>("/logout", { method: "POST" })
    } finally {
      setToken(null)
      setTokenState(null)
      setUser(null)
    }
  }, [])

  const value = React.useMemo<AuthContextValue>(
    () => ({
      user,
      token,
      loading,
      login,
      register,
      logout,
      refresh,
    }),
    [user, token, loading, login, register, logout, refresh]
  )

  return <AuthContext.Provider value={value}>{children}</AuthContext.Provider>
}

export function useAuth() {
  const context = React.useContext(AuthContext)
  if (!context) {
    throw new Error("useAuth must be used within AuthProvider")
  }
  return context
}

export function useRole() {
  const { user } = useAuth()
  return resolveRoleName(user)
}
