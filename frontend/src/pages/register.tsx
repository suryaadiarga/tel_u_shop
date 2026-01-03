import * as React from "react"
import { Link, useNavigate } from "react-router-dom"
import { Button } from "../components/ui/button"
import { Card, CardContent } from "../components/ui/card"
import { Input } from "../components/ui/input"
import { Alert, AlertDescription, AlertTitle } from "../components/ui/alert"
import { useAuth } from "../store/auth"
import { ApiError } from "../lib/api"
import { useToast } from "../components/ui/toast"

const defaultForm = {
  name: "",
  username: "",
  email: "",
  password: "",
  password_confirmation: "",
  nim: "",
  kelas: "",
  phone: "",
  role: "3",
}

type FieldErrors = Record<string, string[]>

export function RegisterPage() {
  const navigate = useNavigate()
  const { register } = useAuth()
  const { push } = useToast()
  const [error, setError] = React.useState<string | null>(null)
  const [fieldErrors, setFieldErrors] = React.useState<FieldErrors>({})
  const [busy, setBusy] = React.useState(false)
  const [form, setForm] = React.useState(defaultForm)

  function updateField(name: keyof typeof form, value: string) {
    setForm((prev) => ({ ...prev, [name]: value }))
  }

  async function handleSubmit(event: React.FormEvent) {
    event.preventDefault()
    setError(null)
    setFieldErrors({})
    setBusy(true)

    try {
      await register({
        ...form,
        role: Number(form.role),
      })
      push({ title: "Account created", description: "Registration successful", variant: "success" })
      navigate("/app")
    } catch (err) {
      if (err instanceof ApiError) {
        setError(err.message)
        if (err.errors && typeof err.errors === "object" && !Array.isArray(err.errors)) {
          setFieldErrors(err.errors as FieldErrors)
        }
      } else {
        setError("Registration failed")
      }
    } finally {
      setBusy(false)
    }
  }

  return (
    <div className="mx-auto flex min-h-screen max-w-5xl items-center justify-center px-6 py-16">
      <Card className="w-full max-w-2xl">
        <CardContent className="space-y-6">
          <div className="space-y-1">
            <div className="font-display text-2xl font-semibold text-ink">Register</div>
            <p className="text-sm text-slate-500">Create your TEL-U Shop account.</p>
          </div>

          {error ? (
            <Alert>
              <AlertTitle>Registration failed</AlertTitle>
              <AlertDescription>{error}</AlertDescription>
            </Alert>
          ) : null}

          <form className="grid gap-4 sm:grid-cols-2" onSubmit={handleSubmit}>
            {[
              { label: "Full name", name: "name" },
              { label: "Username", name: "username" },
              { label: "Email", name: "email", type: "email" },
              { label: "Phone", name: "phone" },
              { label: "NIM", name: "nim" },
              { label: "Class", name: "kelas" },
            ].map((field) => (
              <div key={field.name} className="space-y-2">
                <label className="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">
                  {field.label}
                </label>
                <Input
                  type={field.type ?? "text"}
                  value={form[field.name as keyof typeof form]}
                  onChange={(event) => updateField(field.name as keyof typeof form, event.target.value)}
                  required
                />
                {fieldErrors[field.name] ? (
                  <p className="text-xs text-rose-500">{fieldErrors[field.name].join(", ")}</p>
                ) : null}
              </div>
            ))}

            <div className="space-y-2">
              <label className="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Role</label>
              <select
                className="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm"
                value={form.role}
                onChange={(event) => updateField("role", event.target.value)}
              >
                <option value="3">Customer</option>
                <option value="2">Merchant</option>
              </select>
            </div>

            <div className="space-y-2">
              <label className="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">
                Password
              </label>
              <Input
                type="password"
                value={form.password}
                onChange={(event) => updateField("password", event.target.value)}
                required
              />
              {fieldErrors.password ? (
                <p className="text-xs text-rose-500">{fieldErrors.password.join(", ")}</p>
              ) : null}
            </div>

            <div className="space-y-2">
              <label className="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">
                Confirm password
              </label>
              <Input
                type="password"
                value={form.password_confirmation}
                onChange={(event) => updateField("password_confirmation", event.target.value)}
                required
              />
              {fieldErrors.password_confirmation ? (
                <p className="text-xs text-rose-500">{fieldErrors.password_confirmation.join(", ")}</p>
              ) : null}
            </div>

            <div className="sm:col-span-2">
              <Button type="submit" className="w-full" disabled={busy}>
                {busy ? "Creating..." : "Register"}
              </Button>
            </div>
          </form>

          <div className="text-center text-sm text-slate-500">
            Already have an account? <Link className="font-semibold text-ink" to="/login">Login</Link>
          </div>
        </CardContent>
      </Card>
    </div>
  )
}
