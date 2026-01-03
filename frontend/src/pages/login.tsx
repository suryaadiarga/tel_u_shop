import * as React from "react"
import { Link, useNavigate } from "react-router-dom"
import { Button } from "../components/ui/button"
import { Card, CardContent } from "../components/ui/card"
import { Input } from "../components/ui/input"
import { Alert, AlertDescription, AlertTitle } from "../components/ui/alert"
import { useAuth } from "../store/auth"
import { ApiError } from "../lib/api"
import { useToast } from "../components/ui/toast"

export function LoginPage() {
  const navigate = useNavigate()
  const { login } = useAuth()
  const { push } = useToast()
  const [error, setError] = React.useState<string | null>(null)
  const [busy, setBusy] = React.useState(false)
  const [form, setForm] = React.useState({ email: "", password: "" })

  async function handleSubmit(event: React.FormEvent) {
    event.preventDefault()
    setError(null)
    setBusy(true)

    try {
      await login(form)
      push({ title: "Welcome back", description: "Login successful", variant: "success" })
      navigate("/app")
    } catch (err) {
      const message = err instanceof ApiError ? err.message : "Login failed"
      setError(message)
    } finally {
      setBusy(false)
    }
  }

  return (
    <div className="mx-auto flex min-h-screen max-w-5xl items-center justify-center px-6 py-16">
      <Card className="w-full max-w-md">
        <CardContent className="space-y-6">
          <div className="space-y-1">
            <div className="font-display text-2xl font-semibold text-ink">Login</div>
            <p className="text-sm text-slate-500">Enter your TEL-U Shop credentials.</p>
          </div>

          {error ? (
            <Alert>
              <AlertTitle>Login failed</AlertTitle>
              <AlertDescription>{error}</AlertDescription>
            </Alert>
          ) : null}

          <form className="space-y-4" onSubmit={handleSubmit}>
            <div className="space-y-2">
              <label className="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">
                Email
              </label>
              <Input
                type="email"
                value={form.email}
                onChange={(event) => setForm((prev) => ({ ...prev, email: event.target.value }))}
                required
              />
            </div>
            <div className="space-y-2">
              <label className="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">
                Password
              </label>
              <Input
                type="password"
                value={form.password}
                onChange={(event) => setForm((prev) => ({ ...prev, password: event.target.value }))}
                required
              />
            </div>
            <Button type="submit" className="w-full" disabled={busy}>
              {busy ? "Signing in..." : "Login"}
            </Button>
          </form>

          <div className="text-center text-sm text-slate-500">
            Need an account? <Link className="font-semibold text-ink" to="/register">Register</Link>
          </div>
        </CardContent>
      </Card>
    </div>
  )
}
